<?php

namespace Tests\Feature;

use App\Models\CompanySetting;
use App\Models\PurchaseProof;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PurchaseProofTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        CompanySetting::getSettings();
    }

    public function test_can_view_proofs_index(): void
    {
        PurchaseProof::create([
            'proof_number' => 'TRX-202611-0001',
            'transaction_date' => '2026-11-12',
            'vendor_name' => 'Toko Alat Teknik Maju',
            'amount' => 485000,
            'category' => 'Peralatan & Hardware',
            'description' => 'Pembelian mata pahat bubut dan coolant mesin CNC',
        ]);

        $response = $this->get(route('proofs.index'));
        $response->assertStatus(200);
        $response->assertSee('TRX-202611-0001');
        $response->assertSee('Toko Alat Teknik Maju');
    }

    public function test_can_create_new_purchase_proof_with_photos(): void
    {
        Storage::fake('public');

        $receipt = UploadedFile::fake()->image('nota.jpg', 600, 800);
        $item = UploadedFile::fake()->image('barang.jpg', 800, 600);

        $payload = [
            'proof_number' => 'TRX-202611-0099',
            'transaction_date' => '2026-11-12',
            'vendor_name' => 'CV Sumber Teknik Blitar',
            'amount' => 750000,
            'category' => 'Peralatan & Hardware',
            'penyetor' => 'Budi Santoso (Guru Praktik TPm)',
            'description' => 'Sparepart mesin bubut bengkel TPm',
            'submitted_by' => 'Bendahara SMKS Islam 1',
            'finance_by' => 'Kajur TPm',
            'receipt_photo' => $receipt,
            'item_photo' => $item,
        ];

        $response = $this->post(route('proofs.store'), $payload);

        $this->assertDatabaseHas('purchase_proofs', [
            'proof_number' => 'TRX-202611-0099',
            'vendor_name' => 'CV Sumber Teknik Blitar',
            'penyetor' => 'Budi Santoso (Guru Praktik TPm)',
        ]);

        $proof = PurchaseProof::where('proof_number', 'TRX-202611-0099')->first();
        $response->assertRedirect(route('proofs.show', $proof));

        Storage::disk('public')->assertExists($proof->receipt_photo);
        Storage::disk('public')->assertExists($proof->item_photo);
    }

    public function test_can_view_a4_print_template(): void
    {
        $proof = PurchaseProof::create([
            'proof_number' => 'TRX-202611-0002',
            'transaction_date' => '2026-11-10',
            'vendor_name' => 'Toko Besi Sentosa',
            'amount' => 300000,
            'category' => 'Peralatan & Hardware',
            'penyetor' => 'Agus Priyono',
            'submitted_by' => 'Hj. Siti (Bendahara)',
            'finance_by' => 'Bpk. Ahmad (Kajur TPm)',
        ]);

        $response = $this->get(route('proofs.print', $proof));
        $response->assertStatus(200);
        $response->assertSee('TRX-202611-0002');
        $response->assertSee('Toko Besi Sentosa');
        $response->assertSee('Penyetor');
        $response->assertSee('Agus Priyono');
        $response->assertSee('BAGIAN 1: FOTO NOTA');
        $response->assertSee('BAGIAN 2: FOTO FISIK BARANG');
        $response->assertSee('Bendahara SMKS ISLAM 1 KOTA BLITAR');
        $response->assertSee('Kajur TPm');
        $response->assertDontSee('Pemeriksa / Verifikator');

        // Test view show juga menampilkan PENYETOR
        $showResponse = $this->get(route('proofs.show', $proof));
        $showResponse->assertStatus(200);
        $showResponse->assertSee('PENYETOR');
        $showResponse->assertSee('Agus Priyono');
    }

    public function test_can_export_csv(): void
    {
        PurchaseProof::create([
            'proof_number' => 'TRX-202611-0003',
            'transaction_date' => '2026-11-09',
            'vendor_name' => 'Gramedia Matraman',
            'amount' => 250000,
            'category' => 'ATK & Perlengkapan Kantor',
        ]);

        $response = $this->get(route('proofs.export.csv'));
        $response->assertStatus(200);
        $this->assertTrue($response->headers->get('content-type') === 'text/csv; charset=UTF-8');
    }

    public function test_can_update_company_settings(): void
    {
        $payload = [
            'company_name' => 'SMKS ISLAM 1 KOTA BLITAR',
            'doc_title' => 'LAMPIRAN BUKTI PEMBELIAN TPm',
            'company_address' => 'Jl. Musi No. 4 Blitar',
            'company_phone' => '0342-801642',
            'default_submitted_by' => 'Bendahara Sekolah',
            'default_finance_by' => 'Kajur TPm',
        ];

        $response = $this->put(route('settings.update'), $payload);
        $response->assertRedirect(route('settings.index'));

        $this->assertDatabaseHas('company_settings', [
            'company_name' => 'SMKS ISLAM 1 KOTA BLITAR',
        ]);
    }
}
