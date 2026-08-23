<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
// --- IMPORT MODEL ---
use App\Models\Transaction;
use App\Models\DttotList;
use App\Models\SuspiciousReport;
// --- IMPORT FACADES ---
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB; 
// --- IMPORT LIBRARY PDF ---
use Smalot\PdfParser\Parser;

class ComplianceController extends Controller
{
    // =========================================================================
    // 1. DTTOT (DAFTAR TERDUGA TERORIS & ORGANISASI TERORIS)
    // =========================================================================
    
    public function dttotIndex(Request $request)
    {
        $query = DttotList::query();

        // Fitur Pencarian Cepat
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'LIKE', '%' . $search . '%')
                  ->orWhere('description', 'LIKE', '%' . $search . '%')
                  ->orWhere('birth_info', 'LIKE', '%' . $search . '%');
            });
        }

        // Pagination 50 data per halaman agar ringan
        $lists = $query->orderBy('created_at', 'desc')->paginate(50);
        
        // Penting: Append query string agar saat pindah halaman search tidak hilang
        $lists->appends(['search' => $request->search]);

        return view('admin.compliance.dttot', compact('lists'));
    }

    public function dttotStore(Request $request)
    {
        // [CONFIG KHUSUS] NAIKKAN LIMIT MEMORY & WAKTU KHUSUS UNTUK PROSES BERAT INI
        ini_set('memory_limit', '1024M'); // 1GB (Aman untuk PDF besar)
        ini_set('max_execution_time', '600'); // 10 Menit

        $request->validate([
            'pdf_file' => 'required|mimes:pdf|max:20480', // Max 20MB
        ]);

        try {
            // Cek Ketersediaan Library PDF Parser
            if (!class_exists('Smalot\PdfParser\Parser')) {
                return back()->with('error', 'Library smalot/pdfparser belum terinstall. Jalankan "composer require smalot/pdfparser" di terminal.');
            }

            $file = $request->file('pdf_file');
            $parser = new Parser();
            
            // Parse File PDF
            $pdf = $parser->parseFile($file->getPathname());
            $text = $pdf->getText();
            
            // Bersihkan spasi ganda & newline
            $text = preg_replace('/\s+/', ' ', $text);
            
            // POLA REGEX UTAMA (Mencari data yang diawali angka urut dan "Nama :")
            // Contoh: "1. Nama : ABDULLAH..."
            preg_match_all('/(\d+\.\s*Nama\s*:.*?)(?=\d+\.\s*Nama\s*:|$)/i', $text, $matches);
            
            $entries = $matches[0]; 
            $count = 0;

            foreach ($entries as $entry) {
                // A. AMBIL NAMA (Wajib Ada)
                preg_match('/Nama\s*:\s*(.*?)(?=\s*Nama alias|\s*Tempat|\s*Kewarganegaraan)/i', $entry, $mName);
                $name = isset($mName[1]) ? substr(trim($mName[1]), 0, 250) : null;

                if (!$name || strlen($name) < 3) continue; // Skip jika nama tidak valid

                // B. AMBIL ALIAS (Opsional)
                $aliasInfo = "";
                preg_match('/Nama alias\s*:\s*(.*?)(?=\s*Tempat|\s*Kewarganegaraan)/i', $entry, $mAlias);
                if (!empty($mAlias[1]) && trim($mAlias[1]) != '-') {
                    $aliasInfo = "ALIAS: " . trim($mAlias[1]);
                }

                // C. INFO LAHIR (Opsional)
                preg_match('/Tempat.*?Lahir\s*:\s*(.*?)(?=\s*Kewarganegaraan|\s*Alamat|\s*Keterangan)/i', $entry, $mBirth);
                $birthInfo = isset($mBirth[1]) ? trim($mBirth[1]) : '-';

                // D. ALAMAT (Opsional)
                preg_match('/Alamat\s*:\s*(.*?)(?=\s*Keterangan|$)/i', $entry, $mAddress);
                $address = isset($mAddress[1]) ? trim($mAddress[1]) : '-';

                // E. KEWARGANEGARAAN (Opsional)
                preg_match('/Kewarganegaraan\s*:\s*(.*?)(?=\s*Alamat|\s*Keterangan|$)/i', $entry, $mNation);
                $nationality = isset($mNation[1]) ? trim($mNation[1]) : 'Indonesia';

                $description = "Import PDF Otomatis. " . $aliasInfo;

                // Simpan ke Database
                DttotList::create([
                    'name' => strtoupper($name),
                    'birth_info' => $birthInfo,
                    'address' => $address,
                    'nationality' => $nationality,
                    'description' => $description,
                    'source_doc' => $file->getClientOriginalName()
                ]);
                
                $count++;
            }

            if ($count == 0) {
                return back()->with('error', 'Gagal membaca pola data. Pastikan format PDF sesuai standar DTTOT Polri/PPATK.');
            }

            return back()->with('success', "BERHASIL! {$count} Data Terduga Teroris telah ditambahkan ke database.");

        } catch (\Exception $e) {
            return back()->with('error', 'Gagal Memproses PDF: ' . $e->getMessage());
        }
    }

    public function dttotTruncate()
    {
        try {
            DttotList::truncate();
            return back()->with('success', 'DATABASE DTTOT BERHASIL DIKOSONGKAN (RESET).');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal reset database: ' . $e->getMessage());
        }
    }

    public function dttotDestroy($id)
    {
        DttotList::findOrFail($id)->delete();
        return back()->with('success', 'Data DTTOT berhasil dihapus.');
    }

    // =========================================================================
    // 2. LTKT (LAPORAN TRANSAKSI KEUANGAN TUNAI) - DIATAS 100 JUTA
    // =========================================================================
    
    public function ltktIndex(Request $request)
    {
        // Default: Bulan Ini
        $month = $request->input('month', date('m'));
        $year  = $request->input('year', date('Y'));
        
        // Ambang Batas Laporan (Rp 100.000.000)
        $threshold = 100000000;

        // A. Single Transaction > 100 Juta
        $singleTrx = Transaction::with('branch')
                        ->whereMonth('created_at', $month)
                        ->whereYear('created_at', $year)
                        ->where('total_idr', '>=', $threshold) 
                        ->orderBy('total_idr', 'desc')
                        ->get();

        // B. Akumulasi Harian > 100 Juta (Structuring)
        // Group by NIK + Nama + Tanggal
        $dailyAccumulation = Transaction::select(
                                'customer_name', 
                                'customer_identity_no', 
                                DB::raw('DATE(created_at) as trx_date'), 
                                DB::raw('SUM(total_idr) as total_daily'),
                                DB::raw('COUNT(*) as freq'),
                                DB::raw('MAX(branch_id) as branch_id') // Ambil salah satu cabang
                            )
                            ->whereMonth('created_at', $month)
                            ->whereYear('created_at', $year)
                            ->groupBy('customer_identity_no', 'customer_name', DB::raw('DATE(created_at)'))
                            ->having('total_daily', '>=', $threshold)
                            ->having('freq', '>', 1) // Minimal 2x transaksi
                            ->get();

        // C. Profil Risiko Tinggi (Top 10 Volume Transaksi Bulan Ini)
        $highRiskProfiles = Transaction::select(
                                'customer_name', 
                                'customer_identity_no',
                                DB::raw('SUM(total_idr) as total_volume'),
                                DB::raw('COUNT(*) as total_freq'),
                                DB::raw('MAX(created_at) as last_trx')
                            )
                            ->whereMonth('created_at', $month)
                            ->whereYear('created_at', $year)
                            ->groupBy('customer_identity_no', 'customer_name')
                            ->orderBy('total_volume', 'desc')
                            ->limit(10) // Hanya Top 10 agar ringan
                            ->get();

        return view('admin.compliance.ltkt', compact(
            'singleTrx', 'dailyAccumulation', 'highRiskProfiles', 
            'month', 'year'
        ));
    }

    // =========================================================================
    // 3. LTKM (LAPORAN TRANSAKSI KEUANGAN MENCURIGAKAN)
    // =========================================================================
    
    public function ltkmIndex()
    {
        $reports = SuspiciousReport::with('user')->orderBy('created_at', 'desc')->get();
        return view('admin.compliance.ltkm', compact('reports'));
    }

    public function ltkmStore(Request $request)
    {
        $request->validate([
            'customer_name' => 'required',
            'suspicious_reason' => 'required',
        ]);

        SuspiciousReport::create([
            'customer_name' => strtoupper($request->customer_name),
            'identity_no' => $request->identity_no ?? '-',
            'suspicious_reason' => $request->suspicious_reason,
            'status' => 'PENDING', // Status Awal
            'reported_by' => Auth::id()
        ]);

        return back()->with('success', 'Laporan Transaksi Mencurigakan (LTKM) Berhasil Dibuat. Segera proses ke PPATK.');
    }
}