<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CompanySetting;
use App\Models\PurchaseProof;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Inisialisasi Pengaturan Perusahaan & Format Dokumen A4
        CompanySetting::updateOrCreate(
            ['id' => 1],
            [
                'company_name' => 'PT NUSANTARA TECH UTAMA',
                'doc_title' => 'LAMPIRAN BUKTI PEMBELIAN & NOTA',
                'company_address' => 'Gedung Cyber 2 Lantai 15, Jl. H.R. Rasuna Said Kav. X-5 No. 13, Jakarta Selatan',
                'company_phone' => '(021) 555-0199 / info@nusantaratech.co.id',
                'default_submitted_by' => 'Budi Santoso (Admin Operasional)',
                'default_verified_by' => 'Siti Rahma (Finance Manager)',
                'default_finance_by' => 'Hendro Wijaya (Kasir Utama)',
            ]
        );

        // 2. Transaksi Demo 1: ATK Kantor
        PurchaseProof::updateOrCreate(
            ['proof_number' => 'TRX-202611-0001'],
            [
                'transaction_date' => '2026-11-12',
                'vendor_name' => 'Gramedia Matraman',
                'amount' => 485000,
                'category' => 'ATK & Perlengkapan Kantor',
                'description' => 'Pembelian Kertas HVS PaperOne A4 80gr 5 Rim dan Snowman Whiteboard Marker 1 Lusin untuk operasional meeting room.',
                'receipt_photo' => 'proofs/receipts/sample_nota1.svg',
                'item_photo' => 'proofs/items/sample_barang1.svg',
                'submitted_by' => 'Budi Santoso',
                'verified_by' => 'Siti Rahma',
                'finance_by' => 'Hendro Wijaya',
                'status' => 'verified',
            ]
        );

        // 3. Transaksi Demo 2: Sparepart Hardware
        PurchaseProof::updateOrCreate(
            ['proof_number' => 'TRX-202611-0002'],
            [
                'transaction_date' => '2026-11-10',
                'vendor_name' => 'Toko Sparepart Jaya Motor',
                'amount' => 1250000,
                'category' => 'Peralatan & Hardware',
                'description' => 'Penggantian Aki Heavy Duty GS MF 12V dan 1 set kabel peremajaan kendaraan operasional kantor.',
                'receipt_photo' => 'proofs/receipts/sample_nota2.svg',
                'item_photo' => 'proofs/items/sample_barang2.svg',
                'submitted_by' => 'Agus Priyono (Driver)',
                'verified_by' => 'Siti Rahma',
                'finance_by' => 'Hendro Wijaya',
                'status' => 'verified',
            ]
        );
    }
}
