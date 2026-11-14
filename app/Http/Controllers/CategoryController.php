<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\PurchaseProof;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::withCount('purchaseProofs')
            ->orderBy('name', 'asc')
            ->get();

        return view('categories.index', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100|unique:categories,name',
            'code' => 'nullable|string|max:20',
            'description' => 'nullable|string|max:255',
        ], [
            'name.required' => 'Nama kategori wajib diisi.',
            'name.unique' => 'Nama kategori sudah terdaftar.',
        ]);

        Category::create($validated);

        return redirect()->route('categories.index')
            ->with('success', 'Kategori baru berhasil ditambahkan.');
    }

    public function update(Request $request, Category $category)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100|unique:categories,name,' . $category->id,
            'code' => 'nullable|string|max:20',
            'description' => 'nullable|string|max:255',
        ], [
            'name.required' => 'Nama kategori wajib diisi.',
            'name.unique' => 'Nama kategori sudah digunakan.',
        ]);

        $oldName = $category->name;
        $category->update($validated);

        // Jika nama kategori diubah, perbarui juga data kategori di transaksi lama
        if ($oldName !== $validated['name']) {
            PurchaseProof::where('category', $oldName)->update(['category' => $validated['name']]);
        }

        return redirect()->route('categories.index')
            ->with('success', 'Kategori berhasil diperbarui.');
    }

    public function destroy(Category $category)
    {
        $count = $category->purchaseProofs()->count();
        if ($count > 0) {
            return redirect()->route('categories.index')
                ->with('error', "Kategori '{$category->name}' tidak dapat dihapus karena sedang digunakan oleh {$count} transaksi bukti pembelian. Silakan ubah kategori transaksi tersebut terlebih dahulu.");
        }

        $category->delete();

        return redirect()->route('categories.index')
            ->with('success', "Kategori '{$category->name}' berhasil dihapus.");
    }
}
