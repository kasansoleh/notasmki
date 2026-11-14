@extends('layouts.app')
@section('title', 'Detail Lembar A4 - ' . $proof->proof_number)

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2">
                <a href="{{ route('proofs.index') }}" class="text-sm text-slate-400 hover:text-white">&larr; Kembali</a>
                <span class="text-slate-600">/</span>
                <span class="text-sm font-mono text-blue-400 font-bold">{{ $proof->proof_number }}</span>
            </div>
            <h1 class="text-2xl font-extrabold text-white tracking-tight mt-1">{{ $proof->vendor_name }}</h1>
        </div>
        <div class="flex flex-wrap items-center gap-2.5">
            <a href="{{ route('proofs.print', $proof) }}" target="_blank" class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-500 text-white text-sm font-bold rounded-lg shadow-md shadow-blue-600/30 transition">
                🖨️ Cetak / Unduh PDF A4
            </a>
            <a href="{{ route('proofs.edit', $proof) }}" class="inline-flex items-center gap-2 px-3.5 py-2 bg-slate-800 hover:bg-slate-700 text-slate-200 text-sm font-semibold rounded-lg border border-slate-700 transition">
                ✏️ Edit Transaksi
            </a>
            <form action="{{ route('proofs.destroy', $proof) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus bukti pembelian ini?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="inline-flex items-center gap-2 px-3.5 py-2 bg-rose-950/60 hover:bg-rose-900 text-rose-400 text-sm font-semibold rounded-lg border border-rose-800/50 transition">
                    🗑️ Hapus
                </button>
            </form>
        </div>
    </div>

    <!-- Center A4 Paper Display -->
    <div class="flex justify-center p-2 sm:p-6 bg-slate-950/60 rounded-3xl border border-slate-800">
        <div class="w-full max-w-[210mm] bg-white text-slate-900 rounded-lg shadow-2xl p-6 sm:p-10 flex flex-col justify-between border border-slate-300" style="min-height: 980px; aspect-ratio: 210/297;">
            
            <!-- TOP HALF: NOTA (134mm) -->
            <div class="flex-1 flex flex-col border-b-2 border-dashed border-slate-400 pb-5 mb-5">
                <!-- Header -->
                <div class="flex justify-between items-start border-b-2 border-slate-900 pb-2 mb-3">
                    <div>
                        <div class="font-extrabold text-base tracking-tight uppercase text-slate-900">{{ $settings->company_name }}</div>
                        <div class="text-[11px] text-slate-600">{{ $settings->company_address }}</div>
                        <div class="text-xs font-bold text-blue-700 uppercase mt-0.5">{{ $settings->doc_title }}</div>
                    </div>
                    <div class="text-right text-xs">
                        <div class="text-slate-500 font-medium">Standard A4 Format</div>
                        <div class="font-bold text-slate-900 text-sm font-mono">{{ $proof->proof_number }}</div>
                    </div>
                </div>

                <!-- Meta Table -->
                <div class="grid grid-cols-4 gap-3 bg-slate-100 p-2.5 rounded text-xs mb-3 border border-slate-300">
                    <div>
                        <span class="block text-[9px] uppercase font-bold text-slate-500">TANGGAL</span>
                        <span class="font-bold text-slate-900">{{ $proof->transaction_date->format('d/m/Y') }}</span>
                    </div>
                    <div>
                        <span class="block text-[9px] uppercase font-bold text-slate-500">TOKO / VENDOR</span>
                        <span class="font-bold text-slate-900 truncate block">{{ $proof->vendor_name }}</span>
                    </div>
                    <div>
                        <span class="block text-[9px] uppercase font-bold text-slate-500">KATEGORI</span>
                        <span class="font-bold text-slate-900 truncate block">{{ $proof->category }}</span>
                    </div>
                    <div>
                        <span class="block text-[9px] uppercase font-bold text-slate-500">NOMINAL</span>
                        <span class="font-bold text-emerald-700 font-mono text-sm">{{ $proof->formatted_amount }}</span>
                    </div>
                </div>

                <!-- Label Atas -->
                <div class="text-[10px] font-bold text-slate-700 bg-slate-200 px-2.5 py-1 border-l-4 border-amber-600 mb-2">
                    🧾 BAGIAN 1: FOTO NOTA / STRUK ASLI (SETENGAH LEMBAR ATAS A4)
                </div>

                <!-- Box Foto Nota -->
                <div class="flex-1 bg-slate-50 border border-slate-300 rounded flex items-center justify-center overflow-hidden min-h-[260px]">
                    @if($proof->receipt_photo_url)
                        <img src="{{ $proof->receipt_photo_url }}" class="max-h-[300px] w-auto max-w-full object-contain">
                    @else
                        <div class="text-center text-slate-400 text-xs p-4">
                            [ Tidak Ada Foto Nota Terlampir ]
                        </div>
                    @endif
                </div>
            </div>

            <!-- BOTTOM HALF: BARANG (134mm) -->
            <div class="flex-1 flex flex-col justify-between">
                <!-- Label Bawah -->
                <div class="text-[10px] font-bold text-slate-700 bg-slate-200 px-2.5 py-1 border-l-4 border-blue-600 mb-2">
                    📦 BAGIAN 2: FOTO FISIK BARANG / HASIL PEMBELIAN (SETENGAH LEMBAR BAWAH A4)
                </div>

                <!-- Box Foto Barang -->
                <div class="flex-1 bg-slate-50 border border-slate-300 rounded flex items-center justify-center overflow-hidden min-h-[260px] mb-3">
                    @if($proof->item_photo_url)
                        <img src="{{ $proof->item_photo_url }}" class="max-h-[300px] w-auto max-w-full object-contain">
                    @else
                        <div class="text-center text-slate-400 text-xs p-4">
                            [ Tidak Ada Foto Fisik Barang Terlampir ]
                        </div>
                    @endif
                </div>

                <!-- Catatan / Rincian -->
                <div class="text-xs bg-slate-100 p-2.5 rounded border border-slate-200 mb-3">
                    <strong class="text-slate-900">Rincian Barang:</strong> <span class="text-slate-800">{{ $proof->description ?? '-' }}</span>
                </div>

                <!-- Tanda Tangan -->
                <div class="grid grid-cols-2 gap-12 max-w-lg mx-auto text-center text-xs pt-1 border-t border-slate-300">
                    <div>
                        <div class="text-slate-500 font-semibold mb-8">Bendahara SMKS ISLAM 1 KOTA BLITAR,</div>
                        <div class="font-bold border-b border-slate-900 pb-0.5">{{ $proof->submitted_by ?? $settings->default_submitted_by }}</div>
                    </div>
                    <div>
                        <div class="text-slate-500 font-semibold mb-8">Kajur TPm,</div>
                        <div class="font-bold border-b border-slate-900 pb-0.5">{{ $proof->finance_by ?? $settings->default_finance_by }}</div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection
