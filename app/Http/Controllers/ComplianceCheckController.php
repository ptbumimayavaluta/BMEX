<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\ComplianceService;
use App\Models\Transaction; 
use Carbon\Carbon;          

class ComplianceCheckController extends Controller
{
    protected ComplianceService $complianceService;

    public function __construct(ComplianceService $complianceService)
    {
        $this->complianceService = $complianceService;
    }

    /**
     * Endpoint API untuk mengecek limit threshold nasabah secara real-time
     */
    public function checkCustomerThreshold(Request $request, $customerId)
    {
        $currentAmount = (float) $request->get('amount', 0);
        
        // Membatasi pencarian hanya dari tanggal 1 sampai akhir bulan ini
        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth = Carbon::now()->endOfMonth();

        // Pencarian super cepat menggunakan Index database
        $historicalTotal = Transaction::where('customer_identity_no', $customerId)
            ->whereBetween('created_at', [$startOfMonth, $endOfMonth])
            ->sum('total_idr');

        $projectedTotal = $historicalTotal + $currentAmount;
        
        // Limit Threshold APU-PPT (Rp 150.000.000 / USD 10.000)
        $limitThreshold = 150000000;

        return response()->json([
            'status' => 'success',
            'data' => [
                'customer_identity_no' => $customerId,
                'current_total'        => (float) $historicalTotal,
                'projected_total'      => (float) $projectedTotal,
                'is_exceeded'          => $projectedTotal >= $limitThreshold,
                'is_warning'           => $projectedTotal >= ($limitThreshold * 0.8) && $projectedTotal < $limitThreshold,
                'remaining_limit'      => max(0, $limitThreshold - $historicalTotal),
            ]
        ]);
    }

    /**
     * Halaman Khusus Verifikasi LTKT
     */
    public function showLtktWarningPage(Request $request)
    {
        $customerId = $request->query('customer_id');
        $amount = $request->query('amount', 0);

        $complianceData = $this->complianceService->checkThresholdStatus($customerId, (float)$amount);

        return view('admin.compliance.ltkt_warning', compact('complianceData'));
    }
}