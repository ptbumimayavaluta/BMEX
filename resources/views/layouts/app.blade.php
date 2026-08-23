<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>PT. BALI MERTHA MANDIRI</title>
    <link rel="icon" href="{{ asset('img/bmm.png') }}" type="image/png">
    
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
                        primary: '#0A2647',   /* Biru Laut Cerah */
                        
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
                    <span class="tracking-wide text-sm">PT. BALI MERTHA MANDIRI</span>
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
</body>
</html>