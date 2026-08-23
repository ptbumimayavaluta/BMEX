@extends('layouts.app')

@section('content')
{{-- 
    WRAPPER UTAMA DENGAN ALPINE.JS 
    truncateModalOpen: Mengontrol buka/tutup pop-up hapus database
--}}
<div x-data="{ truncateModalOpen: false }" class="min-h-screen bg-gray-50 pb-12">

    {{-- HEADER & TOMBOL RESET --}}
    <div class="bg-white p-4 shadow-sm border-b flex flex-wrap justify-between items-center rounded-xl mb-6 gap-4">
        <div class="flex items-center gap-3">
            <div class="bg-primary/10 p-2 rounded-lg text-primary">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
            </div>
            <div>
                <h2 class="font-bold text-gray-800 text-lg">Database DTTOT</h2>
                <p class="text-xs text-gray-500">Daftar Terduga Teroris & Organisasi Teror (Sumber: Mabes Polri)</p>
            </div>
        </div>
        
        <div class="flex items-center gap-3">
            {{-- TOMBOL BARU: MEMBUKA MODAL POP-UP (Tanpa Form Disini) --}}
            <button type="button" 
                    @click="truncateModalOpen = true" 
                    class="bg-red-100 text-red-600 px-4 py-2 rounded-lg text-xs font-bold border border-red-200 hover:bg-red-600 hover:text-white transition flex items-center gap-2 shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                KOSONGKAN DATABASE (RESET)
            </button>

            {{-- INDIKATOR STATUS --}}
            <div class="hidden md:flex bg-green-50 text-green-700 px-3 py-2 rounded-lg text-xs font-bold border border-green-100 items-center gap-2">
                <span class="relative flex h-2 w-2">
                  <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
                  <span class="relative inline-flex rounded-full h-2 w-2 bg-green-500"></span>
                </span>
                LIVE SYSTEM
            </div>
        </div>
    </div>

    {{-- BAGIAN 1: UPDATE DATABASE (UPLOAD) --}}
    <div class="mb-8">
        <div class="bg-primary rounded-xl shadow-lg overflow-hidden relative">
            {{-- Decoration --}}
            <div class="absolute top-0 right-0 p-4 opacity-10">
                <svg class="w-32 h-32 text-white" fill="currentColor" viewBox="0 0 24 24"><path d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
            </div>
            
            <div class="p-6 flex flex-col md:flex-row items-center gap-6 relative z-10">
                <div class="flex-1 text-white">
                    <h3 class="font-bold text-secondary text-xl mb-2">Update Database DTTOT</h3>
                    <p class="text-sm text-blue-100 leading-relaxed mb-4">
                        Upload file PDF resmi (Lampiran DTTOT) terbaru dari Kepolisian.<br>
                        <span class="text-yellow-300 font-bold">Tips:</span> Sebaiknya tekan tombol <strong>"KOSONGKAN DATABASE"</strong> di atas terlebih dahulu sebelum mengupload data baru agar tidak terjadi duplikasi.
                    </p>
                    <div class="flex gap-2">
                        <span class="text-[10px] bg-white/20 px-2 py-1 rounded text-white border border-white/20">Format: .PDF Only</span>
                        <span class="text-[10px] bg-white/20 px-2 py-1 rounded text-white border border-white/20">Max: 10MB</span>
                    </div>
                </div>

                <div class="w-full md:w-1/3 bg-white/5 p-4 rounded-lg border border-white/10 backdrop-blur-sm">
                    <form action="{{ route('compliance.dttot.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="relative group cursor-pointer mb-3">
                            <input type="file" name="pdf_file" accept=".pdf" required 
                                   class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-20"
                                   onchange="document.getElementById('fileNameDisplay').innerText = this.files[0].name">
                            
                            <div class="bg-white border-2 border-dashed border-gray-300 rounded-lg p-4 text-center group-hover:border-secondary transition shadow-sm">
                                <svg class="w-8 h-8 mx-auto text-gray-400 group-hover:text-secondary mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path></svg>
                                <p id="fileNameDisplay" class="text-sm font-bold text-gray-600 group-hover:text-secondary truncate">Klik untuk pilih PDF</p>
                            </div>
                        </div>
                        <button type="submit" class="w-full bg-secondary text-primary font-bold py-2.5 rounded-lg shadow-lg hover:bg-yellow-400 transition flex justify-center items-center gap-2 transform active:scale-[0.98]">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                            Mulai Scanning
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- BAGIAN 2: DAFTAR TERDUGA (TABEL) --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="bg-gray-50 px-6 py-4 border-b flex flex-wrap justify-between items-center gap-4">
            <div class="flex items-center gap-2">
                <div class="bg-red-100 p-1.5 rounded text-red-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                </div>
                <div>
                    <h3 class="font-bold text-gray-800">Daftar Terduga (Hasil Scan)</h3>
                    <p class="text-xs text-gray-500">Total Data Tersimpan: <span class="font-bold text-primary">{{ $lists->total() }}</span></p>
                </div>
            </div>
            
            {{-- Search Function --}}
            <div class="relative w-full md:w-64">
                <form action="{{ route('compliance.dttot.index') }}" method="GET">
                    <input type="text" 
                           name="search" 
                           value="{{ request('search') }}"
                           placeholder="Cari Nama / Alias + Enter..." 
                           class="w-full pl-8 pr-4 py-2 rounded-lg border border-gray-300 text-sm focus:ring-primary focus:border-primary shadow-sm"
                           onkeypress="if(event.keyCode == 13) { this.form.submit(); }"> 
                    <button type="submit" class="absolute left-2.5 top-2.5 text-gray-400 hover:text-primary">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    </button>
                </form>
            </div>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left table-fixed">
                <thead class="bg-primary text-white text-xs uppercase">
                    <tr>
                        <th class="p-4 border-b border-white/10 w-[5%] text-center">#</th>
                        <th class="p-4 border-b border-white/10 w-[20%]">Nama Lengkap</th>
                        <th class="p-4 border-b border-white/10 w-[20%]">Tempat & Tgl Lahir</th>
                        <th class="p-4 border-b border-white/10">Keterangan / Alias</th>
                        <th class="p-4 border-b border-white/10 w-[15%]">Sumber</th>
                        <th class="p-4 border-b border-white/10 w-[10%] text-center">Hapus</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($lists as $index => $item)
                    <tr class="hover:bg-red-50 transition group align-top">
                        <td class="p-4 text-gray-500 text-xs text-center font-mono">{{ $loop->iteration + ($lists->currentPage() - 1) * $lists->perPage() }}</td>
                        <td class="p-4">
                            <span class="font-bold text-gray-900 text-sm block">{{ $item->name }}</span>
                            <span class="text-[10px] text-white bg-red-600 px-1.5 py-0.5 rounded inline-block mt-1 font-bold tracking-wider">WANTED</span>
                        </td>
                        <td class="p-4 text-gray-600 text-xs leading-relaxed">
                            {!! nl2br(e($item->birth_info)) !!}
                        </td>
                        <td class="p-4 text-gray-600 text-xs leading-relaxed">
                            <div class="max-h-24 overflow-y-auto pr-1 scrollbar-thin">
                                {{ $item->description }}
                            </div>
                        </td>
                        <td class="p-4">
                            <span class="bg-gray-100 text-gray-600 px-2 py-1 rounded text-[10px] border border-gray-300 block w-fit truncate max-w-[120px]" title="{{ $item->source_doc }}">
                                {{ $item->source_doc }}
                            </span>
                            <div class="text-[10px] text-gray-400 mt-1">
                                {{ \Carbon\Carbon::parse($item->created_at)->format('d/m/Y') }}
                            </div>
                        </td>
                        <td class="p-4 text-center">
                            {{-- Hapus per item tetap pakai confirm biasa agar cepat --}}
                            <form action="{{ route('compliance.dttot.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Hapus satu data ini?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-gray-400 hover:text-red-600 transition p-1" title="Hapus Item">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="p-12 text-center text-gray-400">
                            <div class="flex flex-col items-center justify-center py-8">
                                <div class="bg-gray-100 p-4 rounded-full mb-3">
                                    <svg class="w-12 h-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                </div>

                                @if(request('search'))
                                    <p class="font-bold text-gray-800 text-lg">Pencarian Tidak Ditemukan</p>
                                    <p class="text-sm text-gray-500 mt-1 mb-4">
                                        Tidak ada data terduga dengan nama/alias "<span class="font-bold text-red-500">{{ request('search') }}</span>"
                                    </p>
                                    <a href="{{ route('compliance.dttot.index') }}" class="inline-flex items-center gap-2 bg-primary text-white px-4 py-2 rounded-lg text-sm font-bold hover:bg-yellow-500 transition shadow-sm">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                                        Reset Pencarian
                                    </a>
                                @else
                                    <p class="font-bold text-gray-500 text-lg">Database DTTOT Kosong</p>
                                    <p class="text-sm text-gray-400 mt-1 max-w-sm mx-auto">
                                        Sistem aman bersih. Silakan upload file PDF baru jika ada update dari Mabes Polri.
                                    </p>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <div class="p-4 border-t bg-gray-50">
            {{ $lists->links() }}
        </div>
    </div>

    {{-- ========================================== --}}
    {{-- MODAL KONFIRMASI TRUNCATE (POP-UP KEREN)   --}}
    {{-- ========================================== --}}
    <div x-show="truncateModalOpen" 
         class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-sm"
         style="display: none;"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100">
        
        <div class="bg-white rounded-xl shadow-2xl p-6 max-w-sm w-full transform transition-all border-t-4 border-red-600"
             @click.away="truncateModalOpen = false"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 scale-90"
             x-transition:enter-end="opacity-100 scale-100">
            
            <div class="text-center">
                <div class="mx-auto flex items-center justify-center h-14 w-14 rounded-full bg-red-100 mb-4 animate-bounce">
                    <svg class="h-8 w-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                </div>
                <h3 class="text-lg leading-6 font-bold text-gray-900">PERINGATAN KERAS!</h3>
                <div class="mt-2">
                    <p class="text-sm text-gray-500 mb-2">
                        Anda yakin ingin <strong>MENGHAPUS SEMUA</strong> data DTTOT?
                    </p>
                    <p class="text-xs text-red-500 bg-red-50 p-2 rounded border border-red-100">
                        Tindakan ini tidak bisa dibatalkan. Lakukan hanya jika Anda ingin meng-update data baru dari Polri.
                    </p>
                </div>
            </div>

            {{-- TOMBOL AKSI --}}
            <div class="mt-6 flex gap-3">
                <button @click="truncateModalOpen = false" type="button" class="w-full inline-flex justify-center rounded-lg border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none sm:text-sm font-bold transition">
                    Batal
                </button>
                
                {{-- Form Submit Sesungguhnya --}}
                <form action="{{ route('compliance.dttot.truncate') }}" method="POST" class="w-full">
                    @csrf @method('DELETE')
                    <button type="submit" class="w-full inline-flex justify-center rounded-lg border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none sm:text-sm font-bold transition transform active:scale-95">
                        YA, HAPUS SEMUA
                    </button>
                </form>
            </div>
        </div>
    </div>

</div>

{{-- SCRIPT TAMBAHAN UNTUK SWEETALERT --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    // 1. POP-UP SUKSES
    @if(session('success'))
        Swal.fire({
            title: 'BERHASIL!',
            text: "{!! session('success') !!}",
            icon: 'success',
            confirmButtonText: 'OK, SIAP',
            confirmButtonColor: '#0A2647',
            width: '600px',
            padding: '2em',
            backdrop: `rgba(0,0,123,0.4) left top no-repeat`
        });
    @endif

    // 2. POP-UP ERROR
    @if(session('error'))
        Swal.fire({
            title: 'GAGAL!',
            text: "{!! session('error') !!}",
            icon: 'error',
            confirmButtonText: 'Tutup',
            confirmButtonColor: '#EF4444',
        });
    @endif

    // 3. POP-UP VALIDASI
    @if($errors->any())
        let errorMsg = '';
        @foreach($errors->all() as $error)
            errorMsg += '{{ $error }}\n';
        @endforeach
        
        Swal.fire({
            title: 'Perhatian',
            text: errorMsg,
            icon: 'warning',
            confirmButtonText: 'Perbaiki',
            confirmButtonColor: '#F59E0B'
        });
    @endif
</script>
@endsection