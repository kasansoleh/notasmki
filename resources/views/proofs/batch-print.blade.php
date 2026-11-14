<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Gabungan Rekap Bukti A4 ({{ count($proofs) }} Lembar)</title>
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: Arial, Helvetica, sans-serif;
        }

        body {
            background-color: #f1f5f9;
            color: #0f172a;
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 20px 0;
        }

        .toolbar-no-print {
            width: 210mm;
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 16px;
            background: #1e293b;
            color: #f8fafc;
            padding: 10px 16px;
            border-radius: 8px;
            font-size: 13px;
        }

        .btn-print {
            background-color: #2563eb;
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 6px;
            font-weight: bold;
            cursor: pointer;
            font-size: 13px;
        }

        .btn-back {
            background-color: #475569;
            color: white;
            border: none;
            padding: 8px 14px;
            border-radius: 6px;
            text-decoration: none;
            font-size: 13px;
        }

        .a4-sheet {
            width: 210mm;
            height: 297mm;
            background-color: #ffffff;
            box-shadow: 0 10px 25px rgba(0,0,0,0.15);
            padding: 10mm 14mm;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            box-sizing: border-box;
            position: relative;
            margin-bottom: 20px;
        }

        .section-top {
            height: 134mm;
            display: flex;
            flex-direction: column;
            border-bottom: 2px dashed #94a3b8;
            padding-bottom: 4mm;
            box-sizing: border-box;
        }

        .section-bottom {
            height: 134mm;
            display: flex;
            flex-direction: column;
            padding-top: 4mm;
            box-sizing: border-box;
        }

        .header-doc {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            border-bottom: 2px solid #0f172a;
            padding-bottom: 4px;
            margin-bottom: 6px;
        }

        .company-name {
            font-size: 15px;
            font-weight: 800;
            color: #0f172a;
            letter-spacing: 0.5px;
            text-transform: uppercase;
        }

        .company-sub {
            font-size: 9px;
            color: #475569;
        }

        .doc-title {
            font-size: 12px;
            font-weight: 800;
            color: #1d4ed8;
            text-transform: uppercase;
            margin-top: 2px;
        }

        .meta-box {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 4px;
            background-color: #f8fafc;
            border: 1px solid #cbd5e1;
            padding: 5px 8px;
            border-radius: 4px;
            margin-bottom: 6px;
            font-size: 10px;
        }

        .meta-col {
            display: flex;
            flex-direction: column;
        }

        .meta-title {
            font-size: 8px;
            text-transform: uppercase;
            color: #64748b;
            font-weight: bold;
        }

        .meta-content {
            font-size: 10.5px;
            font-weight: bold;
            color: #0f172a;
        }

        .meta-amount {
            color: #15803d;
            font-family: monospace;
            font-size: 11.5px;
        }

        .section-label {
            font-size: 9px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            background-color: #f1f5f9;
            padding: 2px 6px;
            border-left: 3px solid #2563eb;
            margin-bottom: 5px;
            color: #1e293b;
        }

        .photo-wrapper {
            flex: 1;
            border: 1px solid #cbd5e1;
            background-color: #fcfcfc;
            border-radius: 4px;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            position: relative;
        }

        .photo-wrapper img {
            max-width: 100%;
            max-height: 100%;
            object-fit: contain;
        }

        .desc-strip {
            margin-top: 4px;
            font-size: 9.5px;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            padding: 4px 6px;
            border-radius: 4px;
        }

        .signatures {
            display: grid;
            grid-template-columns: 1fr 1fr; max-width: 450px; margin-left: auto; margin-right: auto;
            gap: 8px;
            margin-top: 4px;
            padding-top: 4px;
            border-top: 1px solid #cbd5e1;
            text-align: center;
            font-size: 9px;
        }

        .sig-role {
            font-weight: 600;
            color: #475569;
            margin-bottom: 22px;
        }

        .sig-name {
            font-weight: bold;
            border-bottom: 1px solid #0f172a;
            padding-bottom: 2px;
        }

        @media print {
            body {
                background: white;
                color: black;
                padding: 0;
            }

            .toolbar-no-print {
                display: none !important;
            }

            .a4-sheet {
                box-shadow: none;
                margin: 0;
                width: 210mm;
                height: 297mm;
                page-break-after: always;
            }

            @page {
                size: A4 portrait;
                margin: 0;
            }
        }
    </style>
</head>
<body>

    <div class="toolbar-no-print">
        <div>
            <strong>Cetak Multi-Halaman A4:</strong> Total {{ count($proofs) }} Transaksi ({{ count($proofs) }} Lembar A4)
        </div>
        <div style="display:flex; gap:8px;">
            <a href="{{ route('proofs.index') }}" class="btn-back">&larr; Kembali</a>
            <button class="btn-print" onclick="window.print()">🖨️ Cetak Semua ({{ count($proofs) }} Lembar PDF)</button>
        </div>
    </div>

    @foreach($proofs as $proof)
        <div class="a4-sheet">
            <!-- TOP HALF: FOTO NOTA -->
            <div class="section-top">
                <div class="header-doc">
                    <div>
                        <div class="company-name">{{ $settings->company_name }}</div>
                        <div class="company-sub">{{ $settings->company_address }}</div>
                        <div class="doc-title">{{ $settings->doc_title }}</div>
                    </div>
                    <div style="text-align: right; font-size: 9.5px;">
                        <div style="color: #64748b;">Standar Format: 1 Lembar A4</div>
                        <div style="font-weight: bold; font-size: 11px; color:#0f172a;">No: {{ $proof->proof_number }}</div>
                    </div>
                </div>

                <div class="meta-box">
                    <div class="meta-col">
                        <span class="meta-title">Tanggal Pembelian</span>
                        <span class="meta-content">{{ $proof->transaction_date->format('d/m/Y') }}</span>
                    </div>
                    <div class="meta-col">
                        <span class="meta-title">Toko / Vendor</span>
                        <span class="meta-content">{{ $proof->vendor_name }}</span>
                    </div>
                    <div class="meta-col">
                        <span class="meta-title">Kategori Biaya</span>
                        <span class="meta-content">{{ $proof->category }}</span>
                    </div>
                    <div class="meta-col">
                        <span class="meta-title">Nominal Total</span>
                        <span class="meta-content meta-amount">{{ $proof->formatted_amount }}</span>
                    </div>
                </div>

                <div class="section-label">
                    🧾 BAGIAN 1: FOTO NOTA / STRUK PEMBELIAN ASLI (SETENGAH LEMBAR ATAS A4)
                </div>

                <div class="photo-wrapper">
                    @if($proof->receipt_photo_url)
                        <img src="{{ $proof->receipt_photo_url }}" alt="Foto Nota Pembelian">
                    @else
                        <div style="text-align: center; color: #94a3b8; font-size: 11px;">
                            [ Tidak Ada Foto Nota Terlampir ]
                        </div>
                    @endif
                </div>
            </div>

            <!-- BOTTOM HALF: FOTO FISIK BARANG -->
            <div class="section-bottom">
                <div class="section-label" style="border-left-color: #059669;">
                    📦 BAGIAN 2: FOTO FISIK BARANG / HASIL PEMBELIAN (SETENGAH LEMBAR BAWAH A4)
                </div>

                <div class="photo-wrapper">
                    @if($proof->item_photo_url)
                        <img src="{{ $proof->item_photo_url }}" alt="Foto Fisik Barang">
                    @else
                        <div style="text-align: center; color: #94a3b8; font-size: 11px;">
                            [ Tidak Ada Foto Barang Terlampir ]
                        </div>
                    @endif
                </div>

                <div class="desc-strip">
                    <strong>Catatan/Rincian:</strong> {{ $proof->description ?? 'Pembelian operasional sesuai kebutuhan unit kerja.' }}
                </div>

                <div class="signatures">
                    <div>
                        <div class="sig-role">Yang Mengajukan / Pembeli,</div>
                        <div class="sig-name">{{ $proof->submitted_by ?? $settings->default_submitted_by }}</div>
                    </div>
                    <div>
                        <div class="sig-role">Pemeriksa / Verifikator,</div>
                        <div class="sig-name">{{ $proof->verified_by ?? $settings->default_verified_by }}</div>
                    </div>
                    <div>
                        <div class="sig-role">Finance & Kasir,</div>
                        <div class="sig-name">{{ $proof->finance_by ?? $settings->default_finance_by }}</div>
                    </div>
                </div>
            </div>
        </div>
    @endforeach

</body>
</html>
