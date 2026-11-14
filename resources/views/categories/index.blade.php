@extends('layouts.app')
@section('title', 'Kelola Kategori Pengeluaran')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-white tracking-tight">Kelola Kategori Pengeluaran</h1>
            <p class="text-sm text-slate-400 mt-1">Tambah, perbarui, atau hapus kategori untuk pengelompokan bukti pembelian & nota A4.</p>
        </div>
        <div>
            <button onclick="openCreateModal()" class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-500 text-white text-sm font-semibold rounded-lg shadow-md shadow-blue-600/30 transition hover:-translate-y-0.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path></svg>
                Tambah Kategori Baru
            </button>
        </div>
    </div>

    <!-- Layout Grid: Form Cepat & Tabel Kategori -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
        
        <!-- Form Tambah Cepat di Kiri -->
        <div class="lg:col-span-4 bg-slate-800/80 border border-slate-700/80 rounded-2xl p-6 shadow-xl space-y-4">
            <div class="flex items-center gap-2 border-b border-slate-700 pb-3">
                <span class="text-lg">🏷️</span>
                <h3 class="font-bold text-white text-base">Tambah Kategori Baru</h3>
            </div>

            <form action="{{ route('categories.store') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1.5">Nama Kategori *</label>
                    <input type="text" name="name" required placeholder="Contoh: Bahan Praktik Bubut & Frais" value="{{ old('name') }}" class="w-full bg-slate-900 border border-slate-700 rounded-lg px-3.5 py-2 text-sm text-slate-100 focus:border-blue-500 focus:outline-none">
                </div>

                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1.5">Kode / Singkatan</label>
                    <input type="text" name="code" placeholder="Contoh: PRAKTIK-TPM" value="{{ old('code') }}" class="w-full bg-slate-900 border border-slate-700 rounded-lg px-3.5 py-2 text-sm text-slate-100 font-mono uppercase focus:border-blue-500 focus:outline-none">
                </div>

                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1.5">Deskripsi / Catatan</label>
                    <textarea name="description" rows="2" placeholder="Keterangan peruntukan kategori ini..." class="w-full bg-slate-900 border border-slate-700 rounded-lg px-3.5 py-2 text-sm text-slate-100 focus:border-blue-500 focus:outline-none">{{ old('description') }}</textarea>
                </div>

                <button type="submit" class="w-full py-2.5 px-4 bg-blue-600 hover:bg-blue-500 text-white font-bold text-sm rounded-xl shadow-lg shadow-blue-600/30 transition">
                    ➕ Simpan Kategori
                </button>
            </form>
        </div>

        <!-- Tabel Daftar Kategori di Kanan -->
        <div class="lg:col-span-8 bg-slate-800/80 border border-slate-700/80 rounded-2xl overflow-hidden shadow-xl">
            <div class="p-4 border-b border-slate-700/80 flex items-center justify-between">
                <div class="font-bold text-white text-sm">Daftar Kategori Terdaftar ({{ $categories->count() }})</div>
                <div class="text-xs text-slate-400">Digunakan pada pemilihan form nota & filter laporan</div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-sm">
                    <thead>
                        <tr class="bg-slate-900/80 text-xs font-semibold text-slate-400 uppercase tracking-wider border-b border-slate-700">
                            <th class="py-3 px-4">Nama Kategori</th>
                            <th class="py-3 px-4">Kode</th>
                            <th class="py-3 px-4">Deskripsi</th>
                            <th class="py-3 px-4 text-center">Jumlah Bukti A4</th>
                            <th class="py-3 px-4 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-700/60 text-slate-200">
                        @forelse($categories as $category)
                            <tr class="hover:bg-slate-700/30 transition">
                                <td class="py-3.5 px-4 font-semibold text-white">
                                    {{ $category->name }}
                                </td>
                                <td class="py-3.5 px-4">
                                    @if($category->code)
                                        <span class="font-mono text-xs px-2 py-0.5 rounded bg-slate-900 text-blue-400 border border-slate-700">
                                            {{ $category->code }}
                                        </span>
                                    @else
                                        <span class="text-xs text-slate-500">-</span>
                                    @endif
                                </td>
                                <td class="py-3.5 px-4 text-xs text-slate-300 max-w-xs truncate" title="{{ $category->description }}">
                                    {{ $category->description ?? '-' }}
                                </td>
                                <td class="py-3.5 px-4 text-center">
                                    @if($category->purchase_proofs_count > 0)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-bold bg-blue-900/60 text-blue-300 border border-blue-700">
                                            {{ $category->purchase_proofs_count }} transaksi
                                        </span>
                                    @else
                                        <span class="text-xs text-slate-500">0 transaksi</span>
                                    @endif
                                </td>
                                <td class="py-3.5 px-4 text-center">
                                    <div class="flex items-center justify-center gap-2">
                                        <!-- Tombol Edit -->
                                        <button onclick="openEditModal({{ $category->id }}, '{{ addslashes($category->name) }}', '{{ addslashes($category->code ?? '') }}', '{{ addslashes($category->description ?? '') }}')" class="p-1.5 bg-slate-700 hover:bg-slate-600 text-amber-400 rounded-lg text-xs" title="Edit Kategori">
                                            ✏️ Edit
                                        </button>

                                        <!-- Tombol Hapus -->
                                        <form action="{{ route('categories.destroy', $category) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus kategori \'{{ $category->name }}\'?')" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-1.5 bg-slate-700 hover:bg-rose-900 text-rose-400 rounded-lg text-xs" title="Hapus Kategori">
                                                🗑️ Hapus
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-12 text-slate-400">
                                    <div class="text-3xl mb-2">🏷️</div>
                                    <div class="font-semibold">Belum ada kategori pengeluaran.</div>
                                    <div class="text-xs text-slate-500 mt-1">Gunakan form di sebelah kiri untuk menambahkan kategori pertama.</div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</div>

<!-- Modal Edit Kategori -->
<div id="editCategoryModal" class="fixed inset-0 z-50 bg-slate-950/80 backdrop-blur-sm hidden items-center justify-center p-4">
    <div class="bg-slate-800 border border-slate-700 rounded-2xl w-full max-w-md p-6 shadow-2xl space-y-4">
        <div class="flex items-center justify-between border-b border-slate-700 pb-3">
            <h3 class="font-bold text-white text-base">✏️ Edit Kategori Pengeluaran</h3>
            <button onclick="closeEditModal()" class="text-slate-400 hover:text-white text-xl">&times;</button>
        </div>

        <form id="editCategoryForm" method="POST" class="space-y-4">
            @csrf
            @method('PUT')

            <div>
                <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1.5">Nama Kategori *</label>
                <input type="text" name="name" id="editName" required class="w-full bg-slate-900 border border-slate-700 rounded-lg px-3.5 py-2 text-sm text-slate-100 focus:border-blue-500 focus:outline-none">
            </div>

            <div>
                <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1.5">Kode / Singkatan</label>
                <input type="text" name="code" id="editCode" class="w-full bg-slate-900 border border-slate-700 rounded-lg px-3.5 py-2 text-sm text-slate-100 font-mono uppercase focus:border-blue-500 focus:outline-none">
            </div>

            <div>
                <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1.5">Deskripsi / Catatan</label>
                <textarea name="description" id="editDescription" rows="2" class="w-full bg-slate-900 border border-slate-700 rounded-lg px-3.5 py-2 text-sm text-slate-100 focus:border-blue-500 focus:outline-none"></textarea>
            </div>

            <div class="flex items-center gap-3 pt-2">
                <button type="submit" class="flex-1 py-2.5 px-4 bg-blue-600 hover:bg-blue-500 text-white font-bold text-sm rounded-xl shadow-lg shadow-blue-600/30 transition">
                    💾 Simpan Perubahan
                </button>
                <button type="button" onclick="closeEditModal()" class="py-2.5 px-4 bg-slate-700 hover:bg-slate-600 text-slate-300 font-semibold text-sm rounded-xl transition">
                    Batal
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function openEditModal(id, name, code, desc) {
        const form = document.getElementById('editCategoryForm');
        form.action = `/categories/${id}`;

        document.getElementById('editName').value = name;
        document.getElementById('editCode').value = code;
        document.getElementById('editDescription').value = desc;

        const modal = document.getElementById('editCategoryModal');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }

    function closeEditModal() {
        const modal = document.getElementById('editCategoryModal');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }

    function openCreateModal() {
        window.scrollTo({ top: 0, behavior: 'smooth' });
        const inputName = document.querySelector('input[name="name"]');
        if (inputName) inputName.focus();
    }
</script>
@endpush
