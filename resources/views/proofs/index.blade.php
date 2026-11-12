@extends('layouts.app')
@section('title', 'Daftar Bukti Pembelian A4')

@section('content')
<div class="space-y-6">
    <!-- Header Page & Stats -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-white tracking-tight">Daftar Bukti Pembelian & Nota</h1>
            <p class="text-sm text-slate-400 mt-1">Pengarsipan pengeluaran kas dalam format 1 lembar A4 per transaksi.</p>
        </div>
        <div class="flex flex-wrap items-center gap-2.5">
            <a href="{{ route('proofs.export.csv') }}" class="inline-flex items-center gap-2 px-3.5 py-2 bg-slate-800 hover:bg-slate-700 text-slate-200 text-sm font-semibold rounded-lg border border-slate-700 transition shadow-sm">
                📊 Export Excel/CSV
            </a>
            <a href="{{ route('proofs.batch-print') }}" target="_blank" class="inline-flex items-center gap-2 px-3.5 py-2 bg-indigo-600 hover:bg-indigo-500 text-white text-sm font-semibold rounded-lg shadow-sm shadow-indigo-600/30 transition">
                🖨️ Cetak Semua A4
            </a>
            <a href="{{ route('proofs.create') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-500 text-white text-sm font-semibold rounded-lg shadow-md shadow-blue-600/30 transition">
                ➕ Input Bukti Baru
            </a>
        </div>
    </div>

    <!-- Stat Summary Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="bg-slate-800/80 border border-slate-700/80 rounded-2xl p-5 shadow-sm">
            <div class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Total Pengeluaran Tercatat</div>
            <div class="text-2xl font-bold font-mono text-emerald-400 mt-2">Rp {{ number_format($totalExpense, 0, ',', '.') }}</div>
            <div class="text-xs text-slate-500 mt-1">Akumulasi seluruh bukti tersimpan</div>
        </div>
        <div class="bg-slate-800/80 border border-slate-700/80 rounded-2xl p-5 shadow-sm">
            <div class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Total Dokumen Bukti (A4)</div>
            <div class="text-2xl font-bold font-mono text-blue-400 mt-2">{{ $totalCount }} Lembar</div>
            <div class="text-xs text-slate-500 mt-1">Siap dicetak atau diexport ke PDF</div>
        </div>
        <div class="bg-slate-800/80 border border-slate-700/80 rounded-2xl p-5 shadow-sm">
            <div class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Pengeluaran Bulan Ini</div>
            <div class="text-2xl font-bold font-mono text-indigo-400 mt-2">Rp {{ number_format($thisMonthExpense, 0, ',', '.') }}</div>
            <div class="text-xs text-slate-500 mt-1">Periode {{ now()->translatedFormat('F Y') }}</div>
        </div>
    </div>

    <!-- Filter & Search Toolbar -->
    <div class="bg-slate-800/60 border border-slate-700 rounded-2xl p-4">
        <form method="GET" action="{{ route('proofs.index') }}" class="grid grid-cols-1 md:grid-cols-12 gap-3">
            <div class="md:col-span-4">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nomor bukti, nama toko, catatan..." class="w-full bg-slate-900 border border-slate-700 rounded-lg px-3.5 py-2 text-sm text-slate-200 placeholder-slate-500 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
            </div>
            <div class="md:col-span-3">
                <select name="category" class="w-full bg-slate-900 border border-slate-700 rounded-lg px-3.5 py-2 text-sm text-slate-200 focus:outline-none focus:border-blue-500">
                    <option value="">Semua Kategori</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat }}" {{ request('category') == $cat ? 'selected' : '' }}>{{ $cat }}</option>
                    @endforeach
                </select>
            </div>
            <div class="md:col-span-3">
                <input type="month" name="month" value="{{ request('month') }}" class="w-full bg-slate-900 border border-slate-700 rounded-lg px-3.5 py-2 text-sm text-slate-200 focus:outline-none focus:border-blue-500">
            </div>
            <div class="md:col-span-2 flex gap-2">
                <button type="submit" class="flex-1 bg-slate-700 hover:bg-slate-600 text-white text-sm font-semibold py-2 px-3 rounded-lg transition">Filter</button>
                <a href="{{ route('proofs.index') }}" class="bg-slate-800 hover:bg-slate-700 text-slate-400 hover:text-white py-2 px-3 rounded-lg text-sm transition">Reset</a>
            </div>
        </form>
    </div>

    <!-- Table of Transactions -->
    <div class="bg-slate-800/80 border border-slate-700/80 rounded-2xl overflow-hidden shadow-lg">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-sm">
                <thead>
                    <tr class="bg-slate-900/80 text-xs font-semibold text-slate-400 uppercase tracking-wider border-b border-slate-700">
                        <th class="py-3.5 px-4">No. Bukti / Tanggal</th>
                        <th class="py-3.5 px-4">Toko / Vendor</th>
                        <th class="py-3.5 px-4">Kategori & Keterangan</th>
                        <th class="py-3.5 px-4 text-center">Lampiran Foto (Nota & Barang)</th>
                        <th class="py-3.5 px-4 text-right">Nominal (Rp)</th>
                        <th class="py-3.5 px-4 text-center">Aksi Dokumen A4</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-700/60 text-slate-200">
                    @forelse($proofs as $proof)
                        <tr class="hover:bg-slate-700/30 transition">
                            <td class="py-3.5 px-4">
                                <a href="{{ route('proofs.show', $proof) }}" class="font-mono font-bold text-blue-400 hover:underline block">
                                    {{ $proof->proof_number }}
                                </a>
                                <span class="text-xs text-slate-400">{{ $proof->transaction_date->format('d/m/Y') }}</span>
                            </td>
                            <td class="py-3.5 px-4">
                                <div class="font-semibold text-white">{{ $proof->vendor_name }}</div>
                                <span class="text-xs text-slate-400">Oleh: {{ $proof->submitted_by ?? 'Staf' }}</span>
                            </td>
                            <td class="py-3.5 px-4 max-w-xs">
                                <span class="inline-block px-2 py-0.5 rounded text-[11px] font-medium bg-blue-900/50 text-blue-300 border border-blue-800 mb-1">
                                    {{ $proof->category }}
                                </span>
                                <div class="text-xs text-slate-300 truncate" title="{{ $proof->description }}">{{ $proof->description ?? '-' }}</div>
                            </td>
                            <td class="py-3.5 px-4 text-center">
                                <div class="flex items-center justify-center gap-2">
                                    @if($proof->receipt_photo_url)
                                        <div class="relative group cursor-pointer" title="Foto Nota (Setengah Atas A4)">
                                            <img src="{{ $proof->receipt_photo_url }}" class="w-10 h-10 object-cover rounded border border-slate-600 bg-slate-900 group-hover:scale-125 transition-transform">
                                            <span class="absolute -bottom-1 -right-1 bg-amber-600 text-white text-[9px] font-bold px-1 rounded">Nota</span>
                                        </div>
                                    @else
                                        <span class="text-xs text-slate-500">No Nota</span>
                                    @endif

                                    @if($proof->item_photo_url)
                                        <div class="relative group cursor-pointer" title="Foto Barang (Setengah Bawah A4)">
                                            <img src="{{ $proof->item_photo_url }}" class="w-10 h-10 object-cover rounded border border-slate-600 bg-slate-900 group-hover:scale-125 transition-transform">
                                            <span class="absolute -bottom-1 -right-1 bg-emerald-600 text-white text-[9px] font-bold px-1 rounded">Barang</span>
                                        </div>
                                    @else
                                        <span class="text-xs text-slate-500">No Fisik</span>
                                    @endif
                                </div>
                            </td>
                            <td class="py-3.5 px-4 text-right font-mono font-bold text-emerald-400">
                                {{ $proof->formatted_amount }}
                            </td>
                            <td class="py-3.5 px-4 text-center">
                                <div class="flex items-center justify-center gap-1.5">
                                    <a href="{{ route('proofs.show', $proof) }}" class="p-1.5 bg-slate-700 hover:bg-slate-600 text-slate-200 rounded-lg text-xs" title="Lihat Lembar A4">
                                        👁️
                                    </a>
                                    <a href="{{ route('proofs.print', $proof) }}" target="_blank" class="p-1.5 bg-blue-600 hover:bg-blue-500 text-white rounded-lg text-xs font-semibold" title="Cetak / PDF A4">
                                        🖨️ Cetak A4
                                    </a>
                                    <a href="{{ route('proofs.edit', $proof) }}" class="p-1.5 bg-slate-700 hover:bg-slate-600 text-amber-400 rounded-lg text-xs" title="Edit">
                                        ✏️
                                    </a>
                                    <form action="{{ route('proofs.destroy', $proof) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus bukti pembelian ini?')" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-1.5 bg-slate-700 hover:bg-rose-900 text-rose-400 rounded-lg text-xs" title="Hapus">
                                            🗑️
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-12 text-slate-400">
                                <div class="text-3xl mb-2">📄</div>
                                <div class="font-semibold">Belum ada bukti pembelian yang tercatat.</div>
                                <div class="text-xs text-slate-500 mt-1">Silakan klik tombol "Input Bukti Baru" untuk memulai.</div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($proofs->hasPages())
            <div class="p-4 border-t border-slate-700">
                {{ $proofs->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
