@extends('layouts.app')
@section('title', 'Pengaturan Kop Dokumen A4')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    <div>
        <h1 class="text-2xl font-extrabold text-white tracking-tight">Pengaturan Kop & Format Lembar A4</h1>
        <p class="text-sm text-slate-400 mt-1">Konfigurasi kop surat resmi dan penandatangan otomatis yang tercetak di kertas A4.</p>
    </div>

    <div class="bg-slate-800/80 border border-slate-700 rounded-2xl p-6 shadow-xl">
        <form action="{{ route('settings.update') }}" method="POST" class="space-y-5">
            @csrf
            @method('PUT')

            <div>
                <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1.5">Nama Perusahaan / Organisasi *</label>
                <input type="text" name="company_name" value="{{ old('company_name', $settings->company_name) }}" required class="w-full bg-slate-900 border border-slate-700 rounded-lg px-3.5 py-2 text-sm text-slate-100 focus:border-blue-500 focus:outline-none font-bold">
            </div>

            <div>
                <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1.5">Judul Dokumen Lampiran *</label>
                <input type="text" name="doc_title" value="{{ old('doc_title', $settings->doc_title) }}" required class="w-full bg-slate-900 border border-slate-700 rounded-lg px-3.5 py-2 text-sm text-slate-100 focus:border-blue-500 focus:outline-none">
            </div>

            <div>
                <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1.5">Alamat Lengkap Perusahaan</label>
                <input type="text" name="company_address" value="{{ old('company_address', $settings->company_address) }}" class="w-full bg-slate-900 border border-slate-700 rounded-lg px-3.5 py-2 text-sm text-slate-100 focus:border-blue-500 focus:outline-none">
            </div>

            <div>
                <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1.5">Kontak / Telepon / Email</label>
                <input type="text" name="company_phone" value="{{ old('company_phone', $settings->company_phone) }}" class="w-full bg-slate-900 border border-slate-700 rounded-lg px-3.5 py-2 text-sm text-slate-100 focus:border-blue-500 focus:outline-none">
            </div>

            <div class="pt-4 border-t border-slate-700 space-y-4">
                <h3 class="text-sm font-bold text-slate-200">Default Penandatangan di Lembar A4 (2 Kolom)</h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-[11px] font-semibold text-slate-400 mb-1">Bendahara SMKS ISLAM 1 KOTA BLITAR (Kiri)</label>
                        <input type="text" name="default_submitted_by" value="{{ old('default_submitted_by', $settings->default_submitted_by) }}" class="w-full bg-slate-900 border border-slate-700 rounded-lg px-3 py-1.5 text-xs text-slate-200">
                    </div>
                    <div>
                        <label class="block text-[11px] font-semibold text-slate-400 mb-1">Kajur TPm (Kanan)</label>
                        <input type="text" name="default_finance_by" value="{{ old('default_finance_by', $settings->default_finance_by) }}" class="w-full bg-slate-900 border border-slate-700 rounded-lg px-3 py-1.5 text-xs text-slate-200">
                    </div>
                </div>
            </div>

            <div class="pt-4">
                <button type="submit" class="w-full py-3 px-4 bg-blue-600 hover:bg-blue-500 text-white font-bold text-sm rounded-xl shadow-lg shadow-blue-600/30 transition">
                    💾 Simpan Pengaturan Dokumen
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
