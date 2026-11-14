<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\PurchaseProof;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CategoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_category_index_screen_can_be_rendered(): void
    {
        Category::create([
            'name' => 'Bahan Praktik Bubut',
            'code' => 'BUBUT',
            'description' => 'Besi silinder dan mata pahat',
        ]);

        $response = $this->get(route('categories.index'));

        $response->assertStatus(200);
        $response->assertSee('Kelola Kategori Pengeluaran');
        $response->assertSee('Bahan Praktik Bubut');
        $response->assertSee('BUBUT');
    }

    public function test_new_category_can_be_created(): void
    {
        $response = $this->post(route('categories.store'), [
            'name' => 'Alat Pelindung Diri (APD)',
            'code' => 'APD-K3',
            'description' => 'Kacamata gerinda, sarung tangan las, dan earplug',
        ]);

        $response->assertRedirect(route('categories.index'));
        $this->assertDatabaseHas('categories', [
            'name' => 'Alat Pelindung Diri (APD)',
            'code' => 'APD-K3',
        ]);
    }

    public function test_category_can_be_updated_and_syncs_existing_transactions(): void
    {
        $category = Category::create([
            'name' => 'Kategori Lama',
            'code' => 'OLD',
            'description' => 'Keterangan lama',
        ]);

        $proof = PurchaseProof::create([
            'proof_number' => 'TRX-TEST-001',
            'transaction_date' => '2026-11-14',
            'vendor_name' => 'Vendor Test',
            'amount' => 150000,
            'category' => 'Kategori Lama',
            'description' => 'Pembelian test',
            'receipt_photo' => 'proofs/receipts/test.jpg',
            'item_photo' => 'proofs/items/test.jpg',
            'submitted_by' => 'Bendahara SMKS ISLAM 1 KOTA BLITAR',
            'finance_by' => 'Kajur TPm',
            'status' => 'verified',
        ]);

        $response = $this->put(route('categories.update', $category), [
            'name' => 'Kategori Baru TPm',
            'code' => 'NEW-TPM',
            'description' => 'Keterangan yang sudah diperbarui',
        ]);

        $response->assertRedirect(route('categories.index'));

        $this->assertDatabaseHas('categories', [
            'id' => $category->id,
            'name' => 'Kategori Baru TPm',
            'code' => 'NEW-TPM',
        ]);

        // Verifikasi bahwa relasi nama pada transaksi lama otomatis ikut terbarui
        $proof->refresh();
        $this->assertEquals('Kategori Baru TPm', $proof->category);
    }

    public function test_category_can_be_deleted_when_not_in_use(): void
    {
        $category = Category::create([
            'name' => 'Kategori Kosong',
            'code' => 'EMPTY',
        ]);

        $response = $this->delete(route('categories.destroy', $category));

        $response->assertRedirect(route('categories.index'));
        $this->assertDatabaseMissing('categories', [
            'id' => $category->id,
        ]);
    }

    public function test_category_cannot_be_deleted_when_used_by_purchase_proof(): void
    {
        $category = Category::create([
            'name' => 'Bahan Bubut Sedang Dipakai',
            'code' => 'BB-USE',
        ]);

        PurchaseProof::create([
            'proof_number' => 'TRX-TEST-002',
            'transaction_date' => '2026-11-14',
            'vendor_name' => 'Vendor Mesin',
            'amount' => 500000,
            'category' => 'Bahan Bubut Sedang Dipakai',
            'description' => 'Pembelian besi as',
            'receipt_photo' => 'proofs/receipts/test.jpg',
            'item_photo' => 'proofs/items/test.jpg',
            'submitted_by' => 'Bendahara SMKS ISLAM 1 KOTA BLITAR',
            'finance_by' => 'Kajur TPm',
            'status' => 'verified',
        ]);

        $response = $this->delete(route('categories.destroy', $category));

        $response->assertRedirect(route('categories.index'));
        $response->assertSessionHas('error');

        // Pastikan kategori tetap ada di database dan tidak terhapus
        $this->assertDatabaseHas('categories', [
            'id' => $category->id,
            'name' => 'Bahan Bubut Sedang Dipakai',
        ]);
    }
}
