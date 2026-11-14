@extends('layouts.app')
@section('title', 'Input Bukti Pembelian Baru')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-extrabold text-white tracking-tight">Input Bukti Pembelian Baru</h1>
            <p class="text-sm text-slate-400 mt-1">Upload foto nota dan foto barang untuk disusun otomatis ke lembar A4.</p>
        </div>
        <a href="{{ route('proofs.index') }}" class="text-sm text-slate-400 hover:text-white flex items-center gap-1">
            &larr; Kembali ke Daftar
        </a>
    </div>

    <!-- Split Screen: Form di Kiri, Live A4 Preview di Kanan -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
        <!-- Form Sisi Kiri -->
        <div class="lg:col-span-5 bg-slate-800/80 border border-slate-700 rounded-2xl p-6 shadow-xl space-y-5">
            <form action="{{ route('proofs.store') }}" method="POST" enctype="multipart/form-data" id="proofForm">
                @csrf

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1.5">No. Bukti / Transaksi *</label>
                        <input type="text" name="proof_number" id="formNoBukti" value="{{ old('proof_number', $nextProofNumber) }}" required class="w-full bg-slate-900 border border-slate-700 rounded-lg px-3.5 py-2 text-sm text-slate-100 font-mono focus:border-blue-500 focus:outline-none" oninput="syncPreview()">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1.5">Tanggal Transaksi *</label>
                        <input type="date" name="transaction_date" id="formDate" value="{{ old('transaction_date', date('Y-m-d')) }}" required class="w-full bg-slate-900 border border-slate-700 rounded-lg px-3.5 py-2 text-sm text-slate-100 focus:border-blue-500 focus:outline-none" oninput="syncPreview()">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1.5">Nama Toko / Vendor *</label>
                        <input type="text" name="vendor_name" id="formVendor" value="{{ old('vendor_name') }}" placeholder="Contoh: Toko Buku Makmur" required class="w-full bg-slate-900 border border-slate-700 rounded-lg px-3.5 py-2 text-sm text-slate-100 focus:border-blue-500 focus:outline-none" oninput="syncPreview()">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1.5">Nominal (Rp) *</label>
                        <input type="number" name="amount" id="formAmount" value="{{ old('amount') }}" placeholder="0" required class="w-full bg-slate-900 border border-slate-700 rounded-lg px-3.5 py-2 text-sm text-slate-100 font-mono focus:border-blue-500 focus:outline-none" oninput="syncPreview()">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1.5">Kategori Pengeluaran *</label>
                    <select name="category" id="formCategory" class="w-full bg-slate-900 border border-slate-700 rounded-lg px-3.5 py-2 text-sm text-slate-100 focus:border-blue-500 focus:outline-none" onchange="syncPreview()">
                        @foreach($categories as $cat)
                            <option value="{{ $cat }}" {{ old('category') == $cat ? 'selected' : '' }}>{{ $cat }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1.5">Rincian / Keterangan Barang</label>
                    <textarea name="description" id="formDesc" rows="2" placeholder="Tuliskan nama barang atau tujuan pembelian..." class="w-full bg-slate-900 border border-slate-700 rounded-lg px-3.5 py-2 text-sm text-slate-100 focus:border-blue-500 focus:outline-none" oninput="syncPreview()">{{ old('description') }}</textarea>
                </div>

                <!-- Dropzone 1: Foto Nota (Setengah Atas) -->
                <div class="p-4 bg-slate-900/60 border border-amber-500/30 rounded-xl space-y-2">
                    <div class="flex items-center justify-between">
                        <label class="text-xs font-bold text-amber-400 uppercase tracking-wide">🧾 1. Foto Nota / Struk (Setengah Atas A4)</label>
                        <span class="text-[10px] text-slate-400">JPG/PNG/WEBP</span>
                    </div>
                    <input type="file" name="receipt_photo" id="fileReceipt" accept="image/*" class="w-full text-xs text-slate-400 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-amber-600 file:text-white hover:file:bg-amber-500 cursor-pointer" onchange="previewImage(this, 'prevReceipt')">
                </div>

                <!-- Dropzone 2: Foto Barang (Setengah Bawah) -->
                <div class="p-4 bg-slate-900/60 border border-blue-500/30 rounded-xl space-y-2">
                    <div class="flex items-center justify-between">
                        <label class="text-xs font-bold text-blue-400 uppercase tracking-wide">📦 2. Foto Fisik Barang (Setengah Bawah A4)</label>
                        <span class="text-[10px] text-slate-400">JPG/PNG/WEBP</span>
                    </div>
                    <input type="file" name="item_photo" id="fileItem" accept="image/*" class="w-full text-xs text-slate-400 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-blue-600 file:text-white hover:file:bg-blue-500 cursor-pointer" onchange="previewImage(this, 'prevItem')">
                </div>

                <!-- Penandatangan -->
                <div class="grid grid-cols-2 gap-4 pt-2 border-t border-slate-700">
                    <div>
                        <label class="block text-[11px] font-semibold text-slate-400 mb-1">Bendahara SMKS ISLAM 1 KOTA BLITAR</label>
                        <input type="text" name="submitted_by" id="formSubmitter" value="{{ old('submitted_by', $settings->default_submitted_by) }}" class="w-full bg-slate-900 border border-slate-700 rounded-lg px-3 py-1.5 text-xs text-slate-200" oninput="syncPreview()">
                    </div>
                    <div>
                        <label class="block text-[11px] font-semibold text-slate-400 mb-1">Kajur TPm</label>
                        <input type="text" name="finance_by" id="formFinance" value="{{ old('finance_by', $settings->default_finance_by) }}" class="w-full bg-slate-900 border border-slate-700 rounded-lg px-3 py-1.5 text-xs text-slate-200" oninput="syncPreview()">
                    </div>
                </div>

                <div class="pt-4 flex items-center gap-3">
                    <button type="submit" class="flex-1 py-3 px-4 bg-blue-600 hover:bg-blue-500 text-white font-bold text-sm rounded-xl shadow-lg shadow-blue-600/30 transition">
                        💾 Simpan Bukti Pembelian
                    </button>
                    <a href="{{ route('proofs.index') }}" class="py-3 px-4 bg-slate-700 hover:bg-slate-600 text-slate-300 font-semibold text-sm rounded-xl transition">
                        Batal
                    </a>
                </div>
            </form>
        </div>

        <!-- Live Preview A4 Sisi Kanan -->
        <div class="lg:col-span-7 flex flex-col items-center">
            <div class="w-full flex items-center justify-between mb-3 px-2">
                <div class="text-xs font-semibold text-slate-400 flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                    <span>Pratinjau Kertas A4 (Live Render)</span>
                </div>
                <div class="text-[11px] text-slate-500">Skala Tampilan 1:1 Lembar Standar</div>
            </div>

            <!-- LEMBAR A4 SIMULATOR -->
            <div class="w-full bg-white text-slate-900 rounded-lg shadow-2xl p-6 sm:p-8 flex flex-col justify-between border border-slate-300" style="min-height: 840px; aspect-ratio: 210/297;">
                
                <!-- TOP HALF: NOTA (132mm) -->
                <div class="flex-1 flex flex-col border-b-2 border-dashed border-slate-400 pb-4 mb-4">
                    <!-- Header -->
                    <div class="flex justify-between items-start border-b-2 border-slate-900 pb-2 mb-2">
                        <div>
                            <div class="font-extrabold text-sm sm:text-base tracking-tight uppercase text-slate-900">{{ $settings->company_name }}</div>
                            <div class="text-xs font-bold text-blue-700 uppercase">{{ $settings->doc_title }}</div>
                        </div>
                        <div class="text-right text-[11px]">
                            <div class="text-slate-500 font-medium">Standard A4 Format</div>
                            <div class="font-bold text-slate-900" id="pvNoBukti">{{ $nextProofNumber }}</div>
                        </div>
                    </div>

                    <!-- Meta Table -->
                    <div class="grid grid-cols-4 gap-2 bg-slate-100 p-2 rounded text-[11px] mb-2 border border-slate-300">
                        <div>
                            <span class="block text-[9px] uppercase font-bold text-slate-500">TANGGAL</span>
                            <span class="font-bold text-slate-900" id="pvDate">{{ date('d/m/Y') }}</span>
                        </div>
                        <div>
                            <span class="block text-[9px] uppercase font-bold text-slate-500">TOKO / VENDOR</span>
                            <span class="font-bold text-slate-900 truncate block" id="pvVendor">-</span>
                        </div>
                        <div>
                            <span class="block text-[9px] uppercase font-bold text-slate-500">KATEGORI</span>
                            <span class="font-bold text-slate-900 truncate block" id="pvCategory">ATK & Perlengkapan</span>
                        </div>
                        <div>
                            <span class="block text-[9px] uppercase font-bold text-slate-500">NOMINAL</span>
                            <span class="font-bold text-emerald-700 font-mono" id="pvAmount">Rp 0</span>
                        </div>
                    </div>

                    <!-- Label Atas -->
                    <div class="text-[10px] font-bold text-slate-700 bg-slate-200 px-2 py-1 border-l-4 border-amber-600 mb-2">
                        BAGIAN 1: FOTO NOTA / STRUK ASLI (SETENGAH LEMBAR ATAS A4)
                    </div>

                    <!-- Box Foto Nota -->
                    <div class="flex-1 bg-slate-50 border border-slate-300 rounded flex items-center justify-center overflow-hidden min-h-[220px]">
                        <img id="prevReceipt" class="max-h-[240px] w-auto max-w-full object-contain hidden">
                        <div id="phReceipt" class="text-center text-slate-400 text-xs p-4">
                            🧾 <span class="font-medium">Belum ada foto nota dipilih</span><br>
                            <small class="text-slate-400">Pilih file foto nota di form sebelah kiri</small>
                        </div>
                    </div>
                </div>

                <!-- BOTTOM HALF: BARANG (132mm) -->
                <div class="flex-1 flex flex-col justify-between">
                    <!-- Label Bawah -->
                    <div class="text-[10px] font-bold text-slate-700 bg-slate-200 px-2 py-1 border-l-4 border-blue-600 mb-2">
                        BAGIAN 2: FOTO FISIK BARANG / HASIL PEMBELIAN (SETENGAH LEMBAR BAWAH A4)
                    </div>

                    <!-- Box Foto Barang -->
                    <div class="flex-1 bg-slate-50 border border-slate-300 rounded flex items-center justify-center overflow-hidden min-h-[220px] mb-2">
                        <img id="prevItem" class="max-h-[240px] w-auto max-w-full object-contain hidden">
                        <div id="phItem" class="text-center text-slate-400 text-xs p-4">
                            📦 <span class="font-medium">Belum ada foto barang dipilih</span><br>
                            <small class="text-slate-400">Pilih file foto fisik barang di form sebelah kiri</small>
                        </div>
                    </div>

                    <!-- Catatan / Rincian -->
                    <div class="text-[10px] bg-slate-100 p-2 rounded border border-slate-200 mb-2">
                        <strong class="text-slate-800">Rincian Barang:</strong> <span id="pvDesc" class="text-slate-700">-</span>
                    </div>

                    <!-- Tanda Tangan -->
                    <div class="grid grid-cols-2 gap-8 max-w-md mx-auto text-center text-[10px] pt-1 border-t border-slate-300">
                        <div>
                            <div class="text-slate-500 font-semibold mb-6">Bendahara SMKS ISLAM 1 KOTA BLITAR,</div>
                            <div class="font-bold border-b border-slate-900 pb-0.5" id="pvSubmitter">{{ $settings->default_submitted_by }}</div>
                        </div>
                        <div>
                            <div class="text-slate-500 font-semibold mb-6">Kajur TPm,</div>
                            <div class="font-bold border-b border-slate-900 pb-0.5" id="pvFinance">{{ $settings->default_finance_by }}</div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function previewImage(input, previewId) {
        const file = input.files[0];
        const previewEl = document.getElementById(previewId);
        const placeholderId = previewId === 'prevReceipt' ? 'phReceipt' : 'phItem';
        const placeholderEl = document.getElementById(placeholderId);

        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                previewEl.src = e.target.result;
                previewEl.classList.remove('hidden');
                placeholderEl.classList.add('hidden');
            }
            reader.readAsDataURL(file);
        }
    }

    function syncPreview() {
        document.getElementById('pvNoBukti').innerText = document.getElementById('formNoBukti').value || '-';
        
        const dateVal = document.getElementById('formDate').value;
        if (dateVal) {
            const [y, m, d] = dateVal.split('-');
            document.getElementById('pvDate').innerText = `${d}/${m}/${y}`;
        }
        
        document.getElementById('pvVendor').innerText = document.getElementById('formVendor').value || '-';
        
        const amt = document.getElementById('formAmount').value || 0;
        document.getElementById('pvAmount').innerText = 'Rp ' + Number(amt).toLocaleString('id-ID');
        
        document.getElementById('pvCategory').innerText = document.getElementById('formCategory').value;
        document.getElementById('pvDesc').innerText = document.getElementById('formDesc').value || '-';
        document.getElementById('pvSubmitter').innerText = document.getElementById('formSubmitter').value || '-';
        document.getElementById('pvFinance').innerText = document.getElementById('formFinance').value || '-';
    }

    syncPreview();
</script>
@endpush
