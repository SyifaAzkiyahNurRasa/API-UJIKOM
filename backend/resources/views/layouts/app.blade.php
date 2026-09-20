<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <!-- FontAwesome CDN -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <title>@yield('title', 'Dashboard')</title>

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-slate-50 text-slate-800 font-sans antialiased overflow-hidden">

    <!-- Layout Utama -->
    <div class="flex h-screen w-full">

        <!-- Sidebar (Full Tinggi & Flex Column untuk dorong User ke bawah) -->
        <aside class="w-64 bg-slate-900 text-white hidden md:flex flex-col justify-between shrink-0 h-full shadow-lg">

            <!-- BAGIAN ATAS: Judul & Navigasi -->
            <div>
                <div class="p-6 text-base font-bold tracking-wider border-b border-slate-800 text-indigo-400 flex items-center gap-2.5">
                    <svg class="w-5 h-5 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                    </svg>
                    <span>
                        @if(auth()->check() && auth()->user()->role === 'petugas')
                            PANEL PETUGAS
                        @elseif(auth()->check() && auth()->user()->role === 'peminjam')
                            PANEL PEMINJAM
                        @else
                            PANEL ADMIN
                        @endif
                    </span>
                </div>

                <nav class="p-4 space-y-1.5">

                    <!-- MENU KHUSUS ADMIN -->
                    @if(auth()->check() && auth()->user()->role === 'admin')

                        {{-- Dashboard --}}
                        <a href="{{ route('admin.dashboard') }}" 
                           class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium transition-all duration-200 hover:translate-x-1 {{ request()->routeIs('admin.dashboard') ? 'bg-slate-800/60 text-white border border-slate-700/50 shadow-sm' : 'text-slate-400 hover:bg-slate-800/40 hover:text-white' }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                            </svg>
                            <span>Dashboard</span>
                        </a>

                        {{-- Kelola User --}}
                        <a href="{{ route('admin.user.index') }}"
                            class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium transition-all duration-200 hover:translate-x-1 {{ request()->routeIs('admin.user*') ? 'bg-slate-800/60 text-white border border-slate-700/50 shadow-sm' : 'text-slate-400 hover:bg-slate-800/40 hover:text-white' }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                            </svg>
                            <span>Kelola User</span>
                        </a>

                        {{-- Kelola Kategori --}}
                        <a href="{{ route('admin.kategori.index') }}"
                            class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium transition-all duration-200 hover:translate-x-1 {{ request()->routeIs('admin.kategori*') ? 'bg-slate-800/60 text-white border border-slate-700/50 shadow-sm' : 'text-slate-400 hover:bg-slate-800/40 hover:text-white' }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"></path>
                            </svg>
                            <span>Kelola Kategori</span>
                        </a>

                        {{-- Kelola Alat --}}
                        <a href="{{ route('admin.alat.index') }}"
                            class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium transition-all duration-200 hover:translate-x-1 {{ request()->routeIs('admin.alat*') ? 'bg-slate-800/60 text-white border border-slate-700/50 shadow-sm' : 'text-slate-400 hover:bg-slate-800/40 hover:text-white' }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                            <span>Kelola Alat</span>
                        </a>

                        {{-- Kelola Peminjaman --}}
                        <a href="{{ route('admin.peminjaman.index') }}"
                            class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium transition-all duration-200 hover:translate-x-1 {{ request()->routeIs('admin.peminjaman*') ? 'bg-slate-800/60 text-white border border-slate-700/50 shadow-sm' : 'text-slate-400 hover:bg-slate-800/40 hover:text-white' }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                            </svg>
                            <span>Kelola Peminjaman</span>
                        </a>

                        {{-- Kelola Pengembalian --}}
                        <a href="{{ route('admin.pengembalian.index') }}"
                            class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium transition-all duration-200 hover:translate-x-1 {{ request()->routeIs('admin.pengembalian*') ? 'bg-slate-800/60 text-white border border-slate-700/50 shadow-sm' : 'text-slate-400 hover:bg-slate-800/40 hover:text-white' }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <span>Kelola Pengembalian</span>
                        </a>

                    <!-- MENU KHUSUS PETUGAS -->
                    @elseif(auth()->check() && auth()->user()->role === 'petugas')

                        {{-- Persetujuan Peminjaman --}}
                        <a href="{{ route('petugas.peminjaman.index') }}"
                            class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium transition-all duration-200 hover:translate-x-1 {{ request()->routeIs('petugas.peminjaman*') ? 'bg-slate-800/60 text-white border border-slate-700/50 shadow-sm' : 'text-slate-400 hover:bg-slate-800/40 hover:text-white' }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <span>Persetujuan Peminjaman</span>
                        </a>

                        {{-- Pemantauan Pengembalian --}}
                        <a href="{{ route('petugas.pengembalian.index') }}"
                            class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium transition-all duration-200 hover:translate-x-1 {{ request()->routeIs('petugas.pengembalian*') ? 'bg-slate-800/60 text-white border border-slate-700/50 shadow-sm' : 'text-slate-400 hover:bg-slate-800/40 hover:text-white' }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                            </svg>
                            <span>Pemantauan Pengembalian</span>
                        </a>

                        {{-- Cetak Laporan --}}
                        <a href="{{ route('petugas.laporan.index') }}"
                            class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium transition-all duration-200 hover:translate-x-1 {{ request()->routeIs('petugas.laporan*') ? 'bg-slate-800/60 text-white border border-slate-700/50 shadow-sm' : 'text-slate-400 hover:bg-slate-800/40 hover:text-white' }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                            </svg>
                            <span>Cetak Laporan</span>
                        </a>

                    @elseif(auth()->check() && auth()->user()->role === 'peminjam')

                        {{-- Katalog Alat --}}
                        <a href="{{ route('peminjam.katalog') }}"
                            class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium transition-all duration-200 hover:translate-x-1 {{ request()->routeIs('peminjam.katalog') ? 'bg-slate-800/60 text-white border border-slate-700/50 shadow-sm' : 'text-slate-400 hover:bg-slate-800/40 hover:text-white' }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h12a2 2 0 012 2v12a2 2 0 01-2 2H6a2 2 0 01-2-2V6zm3 0v12m6-12v12M4 10h16M4 14h16"></path>
                            </svg>
                            <span>Katalog Alat</span>
                        </a>

                        {{-- Riwayat Peminjaman --}}
                        <a href="{{ route('peminjam.riwayat') }}"
                            class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium transition-all duration-200 hover:translate-x-1 {{ request()->routeIs('peminjam.riwayat') ? 'bg-slate-800/60 text-white border border-slate-700/50 shadow-sm' : 'text-slate-400 hover:bg-slate-800/40 hover:text-white' }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                            </svg>
                            <span>Riwayat Peminjaman</span>
                        </a>

                    @endif

                </nav>
            </div>

            <!-- BAGIAN BAWAH: Status User Login (Otomatis di bawah) -->
            <div class="p-4 border-t border-slate-800 text-xs text-slate-400 bg-slate-950/40">
                <p class="mb-0.5">Logged in as :</p>
                @auth
                    <span class="font-semibold text-slate-200 text-sm">
                        {{ auth()->user()->name }}
                    </span>
                @endauth
            </div>

        </aside>

        <!-- Content sebelah kanan -->
        <div class="flex-1 flex flex-col min-w-0 overflow-hidden">

            <!-- Navbar -->
            <header class="bg-white border-b border-slate-100 h-16 flex justify-between items-center px-6 shrink-0 shadow-sm">

                <h1 class="text-base font-bold text-slate-800">
                    @yield('header-title', 'Dashboard')
                </h1>

                @auth
                    <form action="{{ route('logout') }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin logout?');">
                        @csrf
                        <button
                            type="submit"
                            class="bg-rose-50 hover:bg-rose-100 text-rose-600 hover:text-rose-700 font-medium px-4 py-2 rounded-xl text-sm transition">
                            Logout
                        </button>
                    </form>
                @endauth

            </header>

            <!-- Content Utama yang bisa di-scroll -->
            <main class="flex-1 p-6 overflow-y-auto">
                @yield('content')
            </main>

        </div>

    </div>

</body>
</html>