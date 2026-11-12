<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Sistem Bukti Pembelian A4') - {{ config('app.name', 'FinProof') }}</title>
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;600;700&display=swap" rel="stylesheet">
    <!-- Tailwind via CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'sans-serif'],
                        mono: ['"JetBrains Mono"', 'monospace'],
                    },
                    colors: {
                        brand: {
                            50: '#eff6ff',
                            100: '#dbeafe',
                            500: '#3b82f6',
                            600: '#2563eb',
                            700: '#1d4ed8',
                            900: '#1e3a8a',
                        }
                    }
                }
            }
        }
    </script>
    <style>
        @media print {
            .no-print { display: none !important; }
        }
    </style>
    @stack('styles')
</head>
<body class="bg-slate-900 text-slate-100 min-h-screen flex flex-col font-sans antialiased">
    <!-- Main Topbar Navigation -->
    <nav class="bg-slate-800/90 backdrop-blur-md border-b border-slate-700/80 sticky top-0 z-50 no-print">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <div class="flex items-center gap-3">
                    <a href="{{ route('proofs.index') }}" class="flex items-center gap-3 group">
                        <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-blue-600 to-indigo-500 flex items-center justify-center font-extrabold text-white text-lg shadow-lg shadow-blue-500/30 group-hover:scale-105 transition-transform">
                            P4
                        </div>
                        <div>
                            <span class="text-base font-bold text-white tracking-tight block leading-tight">ProofA4 Finance</span>
                            <span class="text-xs text-slate-400 font-medium">Standard A4 Purchase Evidence</span>
                        </div>
                    </a>

                    <!-- Nav Links -->
                    <div class="hidden md:flex items-center gap-1 ml-8">
                        <a href="{{ route('proofs.index') }}" class="px-3 py-2 rounded-lg text-sm font-semibold {{ request()->routeIs('proofs.index') ? 'bg-blue-600/20 text-blue-400 border border-blue-500/30' : 'text-slate-300 hover:text-white hover:bg-slate-700/50' }} transition">
                            📋 Daftar Transaksi
                        </a>
                        <a href="{{ route('proofs.create') }}" class="px-3 py-2 rounded-lg text-sm font-semibold {{ request()->routeIs('proofs.create') ? 'bg-blue-600/20 text-blue-400 border border-blue-500/30' : 'text-slate-300 hover:text-white hover:bg-slate-700/50' }} transition">
                            ➕ Input Bukti Baru
                        </a>
                        <a href="{{ route('settings.index') }}" class="px-3 py-2 rounded-lg text-sm font-semibold {{ request()->routeIs('settings.index') ? 'bg-blue-600/20 text-blue-400 border border-blue-500/30' : 'text-slate-300 hover:text-white hover:bg-slate-700/50' }} transition">
                            ⚙️ Pengaturan Kop A4
                        </a>
                    </div>
                </div>

                <div class="flex items-center gap-3">
                    <a href="{{ route('proofs.batch-print') }}" target="_blank" class="hidden sm:inline-flex items-center gap-2 px-3.5 py-2 bg-slate-700 hover:bg-slate-600 text-slate-200 text-xs font-semibold rounded-lg border border-slate-600 transition shadow-sm">
                        🖨️ Cetak Semua A4
                    </a>
                    <a href="{{ route('proofs.create') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-500 text-white text-sm font-semibold rounded-lg shadow-md shadow-blue-600/30 transition hover:-translate-y-0.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path></svg>
                        Upload Bukti
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Flash Notifications -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4 w-full no-print">
        @if(session('success'))
            <div class="p-4 rounded-xl bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 flex items-center justify-between shadow-sm">
                <div class="flex items-center gap-3">
                    <span class="text-xl">✅</span>
                    <span class="text-sm font-medium">{{ session('success') }}</span>
                </div>
                <button onclick="this.parentElement.remove()" class="text-slate-400 hover:text-white text-lg">&times;</button>
            </div>
        @endif

        @if($errors->any())
            <div class="p-4 rounded-xl bg-rose-500/10 border border-rose-500/30 text-rose-400 shadow-sm">
                <div class="font-bold text-sm mb-1">Terdapat kesalahan pengisian data:</div>
                <ul class="list-disc list-inside text-xs space-y-0.5">
                    @foreach($errors->all() as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
    </div>

    <!-- Main Content Body -->
    <main class="flex-1 w-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-slate-950 border-t border-slate-800 py-6 text-center text-xs text-slate-500 no-print">
        <p>ProofA4 System &copy; {{ date('Y') }} - Format Standar Pelaporan Keuangan (Setengah Atas: Foto Nota, Setengah Bawah: Foto Barang).</p>
    </footer>

    @stack('scripts')
</body>
</html>
