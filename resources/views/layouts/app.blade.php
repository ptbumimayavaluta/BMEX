<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>BALI MONEY EXCHANGE</title>
    <link rel="icon" href="{{ asset('img/bmex.png') }}" type="image/png">
    
    {{-- Scripts --}}
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    {{-- Konfigurasi Tema  --}}
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        // CUKUP GANTI KODE INI, SEMUA HALAMAN AKAN BERUBAH
                        primary: '#040d3f',   /* Biru Laut Cerah */
                        
                        // Warna background halaman (Putih Abu Tipis)
                        bglo: '#f8fafc',      
                    },
                    fontFamily: {
                        sans: ['Inter', 'ui-sans-serif', 'system-ui', 'sans-serif'],
                    }
                }
            }
        }
    </script>
    
    {{-- Style Tambahan untuk Scrollbar Halus --}}
    <style>
        /* Custom Scrollbar agar terlihat rapi */
        ::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }
        ::-webkit-scrollbar-track {
            background: #f1f5f9; 
        }
        ::-webkit-scrollbar-thumb {
            background: #cbd5e1; 
            border-radius: 3px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: #94a3b8; 
        }
    </style>
</head>
<body class="bg-bglo font-sans antialiased text-slate-800">

    <div class="flex h-screen overflow-hidden">
        
        {{-- Sidebar Desktop (Hidden di Mobile) --}}
        <div class="hidden md:block">
            @include('layouts.sidebar')
        </div>

        {{-- Konten Utama --}}
        {{-- md:ml-64 menyesuaikan lebar sidebar --}}
        <div class="flex-1 flex flex-col md:ml-64 transition-all duration-300 h-screen overflow-hidden bg-bglo">
            
            {{-- HEADER MOBILE (Hanya tampil di HP) --}}
            {{-- Menggunakan warna Primary (Merah) agar senada --}}
            <div class="md:hidden print:hidden bg-primary text-white h-16 flex items-center justify-between px-4 shadow-md shrink-0 z-30">
                <div class="font-bold text-lg flex items-center gap-3">
                    {{-- Logo Box --}}
                    <div class="w-8 h-8 bg-white/20 backdrop-blur-sm text-white rounded-lg flex items-center justify-center font-bold text-sm border border-white/30">
                        MC
                    </div>
                    <span class="tracking-wide text-sm">BALI MONEY EXCHANGE</span>
                </div>
                {{-- Tombol Menu Mobile --}}
                <a href="{{ route('admin.dashboard') }}" class="text-xs bg-white text-primary px-3 py-1.5 rounded-lg font-bold shadow-sm hover:bg-gray-100 transition">
                    Menu
                </a>
            </div>

            {{-- MAIN CONTENT --}}
            <main class="flex-1 overflow-y-auto p-4 md:p-8">
                {{-- Area konten akan diisi oleh halaman lain --}}
                @yield('content') 
            </main>
            
        </div>
    </div>

    <!-- Modal Notifikasi Sesi Habis -->
<div id="session-timeout-modal" style="display: none; position: fixed; top: 0; left: 0; width: 100vw; height: 100vh; background: rgba(0, 0, 0, 0.65); z-index: 99999; justify-content: center; align-items: center; backdrop-filter: blur(2px);">
    <div style="background: #ffffff; padding: 30px; border-radius: 12px; text-align: center; max-width: 420px; width: 90%; box-shadow: 0 10px 25px rgba(0,0,0,0.2); font-family: sans-serif;">
        <div style="font-size: 48px; margin-bottom: 10px;">⏰</div>
        <h4 style="margin: 0 0 10px 0; color: #333; font-weight: 600;">Sesi Anda telah Habis</h4>
        <p style="color: #666; font-size: 14px; line-height: 1.5; margin-bottom: 20px;">
            Anda telah tidak aktif dalam beberapa waktu. Demi keamanan data keuangan, silakan login kembali.
        </p>
        <a href="{{ route('login') }}" style="display: inline-block; background-color: #2563eb; color: #ffffff; text-decoration: none; padding: 12px 24px; font-size: 14px; font-weight: 600; border-radius: 6px; cursor: pointer; transition: background 0.2s;">
            Login Ulang Sekarang
        </a>
    </div>
</div>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // Cek sesi setiap 60 detik (1 menit)
            const checkInterval = 60000; 

            setInterval(function() {
                fetch("{{ route('check.session') }}", {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                })
                .then(response => {
                    // Jika status 401 (Unauthorized), 419 (Page Expired), atau bukan HTTP 200 OK
                    if (response.status === 401 || response.status === 419 || !response.ok) {
                        showSessionExpiredModal();
                        return null;
                    }
                    return response.json();
                })
                .then(data => {
                    if (data && data.auth === false) {
                        showSessionExpiredModal();
                    }
                })
                .catch(error => {
                    // Jika koneksi terputus atau gagal membaca respon
                    showSessionExpiredModal();
                });
            }, checkInterval);

            function showSessionExpiredModal() {
                const modal = document.getElementById('session-timeout-modal');
                if (modal && modal.style.display !== 'flex') {
                    modal.style.display = 'flex';

                    // Otomatis arahkan ke halaman login dalam 3 detik
                    setTimeout(function() {
                        window.location.href = "{{ route('login') }}";
                    }, 3000);
                }
            }
        });
    </script>
    
</body>
</html>