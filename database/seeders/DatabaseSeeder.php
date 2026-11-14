<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CompanySetting;
use App\Models\PurchaseProof;
use App\Models\Category;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Kategori Pengeluaran
        $defaultCategories = [
            ['name' => 'Bahan Praktik Siswa TPm', 'code' => 'PRAKTIK-TPM', 'description' => 'Besi silinder, plat, pahat bubut, insert milling, coolant, dan amplas'],
            ['name' => 'Sparepart & Hardware Mesin', 'code' => 'SPAREPART', 'description' => 'Komponen mesin bubut, milling, bor frais, belt, aki dan bearing'],
            ['name' => 'ATK & Perlengkapan Kantor', 'code' => 'ATK', 'description' => 'Kertas, map, spidol whiteboard, tinta printer, dan ordner'],
            ['name' => 'Biaya Operasional & Bensin', 'code' => 'OPERASIONAL', 'description' => 'BBM kendaraan jemput/antar, transportasi dinas ke industri'],
            ['name' => 'Pemeliharaan & Servis', 'code' => 'MAINTENANCE', 'description' => 'Perawatan rutin berkala mesin perkakas dan kelistrikan'],
            ['name' => 'Konsumsi Tamu & Kegiatan', 'code' => 'KONSUMSI', 'description' => 'Konsumsi uji kompetensi kejuruan dan kunjungan industri'],
            ['name' => 'Lain-lain', 'code' => 'LAINNYA', 'description' => 'Pengeluaran darurat atau belanja umum lainnya'],
        ];

        foreach ($defaultCategories as $cat) {
            Category::updateOrCreate(['name' => $cat['name']], $cat);
        }

        // 2. Inisialisasi Pengaturan Perusahaan & Format Dokumen A4
        CompanySetting::updateOrCreate(
            ['id' => 1],
            [
                'company_name' => 'SMKS ISLAM 1 KOTA BLITAR',
                'doc_title' => 'LAMPIRAN BUKTI PEMBELIAN & NOTA',
                'company_address' => 'Jl. Musi No. 4, Kota Blitar, Jawa Timur - Telp: (0342) 801642',
                'company_phone' => '(021) 555-0199 / info@nusantaratech.co.id',
                'default_submitted_by' => 'Bendahara Sekolah',
                'default_verified_by' => null,
                'default_finance_by' => 'Kajur TPm',
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
                'verified_by' => null,
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
                'verified_by' => null,
                'finance_by' => 'Hendro Wijaya',
                'status' => 'verified',
            ]
        );
    }
}
