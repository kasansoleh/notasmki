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
            'vendor_name' => 'Gramedia Matraman',
            'amount' => 485000,
            'category' => 'ATK & Perlengkapan Kantor',
            'description' => 'Beli kertas HVS dan spidol',
        ]);

        $response = $this->get(route('proofs.index'));
        $response->assertStatus(200);
        $response->assertSee('TRX-202611-0001');
        $response->assertSee('Gramedia Matraman');
    }

    public function test_can_create_new_purchase_proof_with_photos(): void
    {
        Storage::fake('public');

        $receipt = UploadedFile::fake()->image('nota.jpg', 600, 800);
        $item = UploadedFile::fake()->image('barang.jpg', 800, 600);

        $payload = [
            'proof_number' => 'TRX-202611-0099',
            'transaction_date' => '2026-11-12',
            'vendor_name' => 'Toko Komputer Prima',
            'amount' => 750000,
            'category' => 'Peralatan & Hardware',
            'description' => 'Keyboard mekanik dan mouse wireless kantor',
            'submitted_by' => 'Staff IT',
            'verified_by' => 'Manager IT',
            'finance_by' => 'Finance Dept',
            'receipt_photo' => $receipt,
            'item_photo' => $item,
        ];

        $response = $this->post(route('proofs.store'), $payload);

        $this->assertDatabaseHas('purchase_proofs', [
            'proof_number' => 'TRX-202611-0099',
            'vendor_name' => 'Toko Komputer Prima',
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
            'vendor_name' => 'SPBU Pertamina',
            'amount' => 300000,
            'category' => 'Biaya Operasional & Bensin',
        ]);

        $response = $this->get(route('proofs.print', $proof));
        $response->assertStatus(200);
        $response->assertSee('TRX-202611-0002');
        $response->assertSee('SPBU Pertamina');
        $response->assertSee('BAGIAN 1: FOTO NOTA');
        $response->assertSee('BAGIAN 2: FOTO FISIK BARANG');
    }

    public function test_can_export_csv(): void
    {
        PurchaseProof::create([
            'proof_number' => 'TRX-202611-0003',
            'transaction_date' => '2026-11-09',
            'vendor_name' => 'Restoran Sederhana',
            'amount' => 250000,
            'category' => 'Jamuan & Konsumsi Client',
        ]);

        $response = $this->get(route('proofs.export.csv'));
        $response->assertStatus(200);
        $this->assertTrue($response->headers->get('content-type') === 'text/csv; charset=UTF-8');
    }

    public function test_can_update_company_settings(): void
    {
        $payload = [
            'company_name' => 'PT KREATIF JAYA MANDIRI',
            'doc_title' => 'BUKTI REIMBURSEMENT BIAYA',
            'company_address' => 'Jl. Sudirman Kav 20, Jakarta',
            'company_phone' => '021-998877',
            'default_submitted_by' => 'User Staff',
            'default_verified_by' => 'Lead Team',
            'default_finance_by' => 'Finance Lead',
        ];

        $response = $this->put(route('settings.update'), $payload);
        $response->assertRedirect(route('settings.index'));

        $this->assertDatabaseHas('company_settings', [
            'company_name' => 'PT KREATIF JAYA MANDIRI',
        ]);
    }
}
