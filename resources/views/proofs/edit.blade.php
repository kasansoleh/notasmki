@extends('layouts.app')
@section('title', 'Edit Bukti Pembelian - ' . $proof->proof_number)

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-extrabold text-white tracking-tight">Edit Bukti Pembelian</h1>
            <p class="text-sm text-slate-400 mt-1">Perbarui data atau ganti foto nota / fisik barang.</p>
        </div>
        <a href="{{ route('proofs.show', $proof) }}" class="text-sm text-slate-400 hover:text-white flex items-center gap-1">
            &larr; Batal & Kembali
        </a>
    </div>

    <div class="bg-slate-800/80 border border-slate-700 rounded-2xl p-6 shadow-xl">
        <form action="{{ route('proofs.update', $proof) }}" method="POST" enctype="multipart/form-data" class="space-y-5">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1.5">No. Bukti / Transaksi *</label>
                    <input type="text" name="proof_number" value="{{ old('proof_number', $proof->proof_number) }}" required class="w-full bg-slate-900 border border-slate-700 rounded-lg px-3.5 py-2 text-sm text-slate-100 font-mono focus:border-blue-500 focus:outline-none">
                </div>
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1.5">Tanggal Transaksi *</label>
                    <input type="date" name="transaction_date" value="{{ old('transaction_date', $proof->transaction_date->format('Y-m-d')) }}" required class="w-full bg-slate-900 border border-slate-700 rounded-lg px-3.5 py-2 text-sm text-slate-100 focus:border-blue-500 focus:outline-none">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1.5">Nama Toko / Vendor *</label>
                    <input type="text" name="vendor_name" value="{{ old('vendor_name', $proof->vendor_name) }}" required class="w-full bg-slate-900 border border-slate-700 rounded-lg px-3.5 py-2 text-sm text-slate-100 focus:border-blue-500 focus:outline-none">
                </div>
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1.5">Nominal (Rp) *</label>
                    <input type="number" name="amount" value="{{ old('amount', $proof->amount) }}" required class="w-full bg-slate-900 border border-slate-700 rounded-lg px-3.5 py-2 text-sm text-slate-100 font-mono focus:border-blue-500 focus:outline-none">
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1.5">Kategori Pengeluaran *</label>
                <select name="category" class="w-full bg-slate-900 border border-slate-700 rounded-lg px-3.5 py-2 text-sm text-slate-100 focus:border-blue-500 focus:outline-none">
                    @foreach($categories as $cat)
                        <option value="{{ $cat }}" {{ old('category', $proof->category) == $cat ? 'selected' : '' }}>{{ $cat }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1.5">Rincian / Keterangan Barang</label>
                <textarea name="description" rows="2" class="w-full bg-slate-900 border border-slate-700 rounded-lg px-3.5 py-2 text-sm text-slate-100 focus:border-blue-500 focus:outline-none">{{ old('description', $proof->description) }}</textarea>
            </div>

            <!-- Upload Nota -->
            <div class="p-4 bg-slate-900/60 border border-amber-500/30 rounded-xl space-y-3">
                <div class="flex items-center justify-between">
                    <label class="text-xs font-bold text-amber-400 uppercase tracking-wide">🧾 Foto Nota / Struk (Setengah Atas A4)</label>
                    <span class="text-[10px] text-slate-400">Kosongkan bila tidak diganti</span>
                </div>
                @if($proof->receipt_photo_url)
                    <div class="flex items-center gap-3">
                        <img src="{{ $proof->receipt_photo_url }}" class="h-16 w-24 object-cover rounded border border-slate-700">
                        <span class="text-xs text-slate-400">Foto nota saat ini terpasang</span>
                    </div>
                @endif
                <input type="file" name="receipt_photo" accept="image/*" class="w-full text-xs text-slate-400 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-amber-600 file:text-white hover:file:bg-amber-500 cursor-pointer">
            </div>

            <!-- Upload Barang -->
            <div class="p-4 bg-slate-900/60 border border-blue-500/30 rounded-xl space-y-3">
                <div class="flex items-center justify-between">
                    <label class="text-xs font-bold text-blue-400 uppercase tracking-wide">📦 Foto Fisik Barang (Setengah Bawah A4)</label>
                    <span class="text-[10px] text-slate-400">Kosongkan bila tidak diganti</span>
                </div>
                @if($proof->item_photo_url)
                    <div class="flex items-center gap-3">
                        <img src="{{ $proof->item_photo_url }}" class="h-16 w-24 object-cover rounded border border-slate-700">
                        <span class="text-xs text-slate-400">Foto barang saat ini terpasang</span>
                    </div>
                @endif
                <input type="file" name="item_photo" accept="image/*" class="w-full text-xs text-slate-400 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-blue-600 file:text-white hover:file:bg-blue-500 cursor-pointer">
            </div>

            <!-- Penandatangan -->
            <div class="grid grid-cols-2 gap-4 pt-2 border-t border-slate-700">
                <div>
                    <label class="block text-[11px] font-semibold text-slate-400 mb-1">Diajukan Oleh</label>
                    <input type="text" name="submitted_by" value="{{ old('submitted_by', $proof->submitted_by) }}" class="w-full bg-slate-900 border border-slate-700 rounded-lg px-3 py-1.5 text-xs text-slate-200">
                </div>
                <div>
                    <label class="block text-[11px] font-semibold text-slate-400 mb-1">Diverifikasi Oleh</label>
                    <input type="text" name="verified_by" value="{{ old('verified_by', $proof->verified_by) }}" class="w-full bg-slate-900 border border-slate-700 rounded-lg px-3 py-1.5 text-xs text-slate-200">
                </div>
            </div>

            <div class="pt-4 flex items-center gap-3">
                <button type="submit" class="flex-1 py-3 px-4 bg-blue-600 hover:bg-blue-500 text-white font-bold text-sm rounded-xl shadow-lg shadow-blue-600/30 transition">
                    💾 Perbarui Bukti Pembelian
                </button>
                <a href="{{ route('proofs.show', $proof) }}" class="py-3 px-4 bg-slate-700 hover:bg-slate-600 text-slate-300 font-semibold text-sm rounded-xl transition">
                    Batal
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
