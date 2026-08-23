<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Branch;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. MATA UANG (Panggil Seeder Terpisah)
        // Pastikan CurrencySeeder.php sudah ada
        $this->call([CurrencySeeder::class]);

        // 2. SETTINGS (Draft Website / Ganti Kulit)
        if (Schema::hasTable('settings')) {
            $settings = [
                ['key' => 'app_name', 'value' => 'MONEY CHANGER PRO'],
                ['key' => 'app_logo', 'value' => 'logo.png'],
                ['key' => 'primary_color', 'value' => '#0A2647'],   // Navy Blue
                ['key' => 'secondary_color', 'value' => '#D4AF37'], // Gold
            ];
            foreach ($settings as $setting) {
                // updateOrInsert: Kalau ada diupdate, kalau belum ada dibuat
                DB::table('settings')->updateOrInsert(
                    ['key' => $setting['key']],
                    ['value' => $setting['value'], 'created_at' => now(), 'updated_at' => now()]
                );
            }
        }

        // 3. KANTOR CABANG (Pakai firstOrCreate agar tidak error duplicate)
        $pusat = Branch::firstOrCreate(
            ['name' => 'KANTOR PUSAT JAKARTA'], // Kunci Pengecekan
            ['address' => 'Jl. Jenderal Sudirman No. 1, Jakarta']
        );

        $bali = Branch::firstOrCreate(
            ['name' => 'CABANG KUTA BALI'],
            ['address' => 'Jl. Legian No. 99, Kuta, Bali']
        );

        // 4. USERS (HIERARKI LENGKAP - ANTI DUPLIKAT)
        
        // A. OWNER (Pemilik Tunggal - Super Power)
        User::firstOrCreate(
            ['username' => 'owner'], // <--- Cek username 'owner', jika ada SKIP
            [
                'name' => 'Big Boss Owner',
                'email' => 'owner@example.com',
                'role' => 'owner',
                'password' => Hash::make('password'),
                'is_active' => true,
            ]
        );

        // B. ADMIN (Manajer Operasional)
        User::firstOrCreate(
            ['username' => 'admin'],
            [
                'name' => 'Manajer Operasional',
                'email' => 'admin@example.com',
                'role' => 'admin',
                'password' => Hash::make('password'),
                'is_active' => true,
            ]
        );

        // C. KASIR 1 (Di Jakarta)
        $kasirJKT = User::firstOrCreate(
            ['username' => 'kasir1'],
            [
                'name' => 'Budi Kasir Pusat',
                'email' => 'kasir1@example.com',
                'role' => 'cashier',
                'password' => Hash::make('password'),
                'is_active' => true,
            ]
        );

        // D. KASIR 2 (Di Bali)
        $kasirBali = User::firstOrCreate(
            ['username' => 'kasir2'],
            [
                'name' => 'Sinta Kasir Bali',
                'email' => 'kasir2@example.com',
                'role' => 'cashier',
                'password' => Hash::make('password'),
                'is_active' => true,
            ]
        );

        // 5. RELASI KASIR KE CABANG (Attach Aman)
        
        // Cek Budi (Kasir 1) -> Jakarta
        // Hanya attach jika belum punya relasi ke cabang tersebut
        if ($kasirJKT->branches()->where('branch_id', $pusat->id)->doesntExist()) {
            $kasirJKT->branches()->attach($pusat->id);
        }
        
        // Cek Sinta (Kasir 2) -> Bali
        if ($kasirBali->branches()->where('branch_id', $bali->id)->doesntExist()) {
            $kasirBali->branches()->attach($bali->id);
        }
    }
}