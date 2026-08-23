<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Tabel DTTOT (Daftar Terduga Teroris)
        Schema::create('dttot_lists', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Nama Terduga
            $table->text('birth_info')->nullable(); // Tempat Tgl Lahir
            $table->text('address')->nullable(); // Alamat
            $table->string('nationality')->nullable(); // Kebangsaan
            $table->text('description')->nullable(); // Keterangan tambahan
            $table->string('source_doc')->nullable(); // Nama File PDF Sumber
            $table->timestamps();
        });

        // 2. Tabel LTKM (Laporan Transaksi Keuangan Mencurigakan)
        Schema::create('suspicious_reports', function (Blueprint $table) {
            $table->id();
            // Bisa disambungkan ke transaksi yang ada (Opsional)
            $table->foreignId('transaction_id')->nullable()->constrained('transactions')->onDelete('set null');
            
            // Atau input manual jika orangnya tidak jadi transaksi (Attempted)
            $table->string('customer_name')->nullable();
            $table->string('identity_no')->nullable();
            
            $table->text('suspicious_reason'); // Alasan mencurigakan (Structuring, Identitas Palsu, dll)
            $table->string('status')->default('PENDING'); // PENDING, REPORTED to PPATK
            $table->foreignId('reported_by')->constrained('users'); // Siapa yang lapor (Admin)
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dttot_lists');
        Schema::dropIfExists('suspicious_reports');
    }
};