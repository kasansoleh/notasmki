<?php

namespace App\Http\Controllers;

use App\Models\PurchaseProof;
use App\Models\CompanySetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class PurchaseProofController extends Controller
{
    public function index(Request $request)
    {
        $settings = CompanySetting::getSettings();
        $query = PurchaseProof::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('proof_number', 'like', "%{$search}%")
                  ->orWhere('vendor_name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        if ($request->filled('month')) {
            $query->whereMonth('transaction_date', date('m', strtotime($request->month)))
                  ->whereYear('transaction_date', date('Y', strtotime($request->month)));
        }

        $proofs = $query->orderBy('transaction_date', 'desc')
                        ->orderBy('id', 'desc')
                        ->paginate(12)
                        ->withQueryString();

        $totalExpense = PurchaseProof::sum('amount');
        $totalCount = PurchaseProof::count();
        $thisMonthExpense = PurchaseProof::whereMonth('transaction_date', now()->month)
                                        ->whereYear('transaction_date', now()->year)
                                        ->sum('amount');

        $categories = [
            'ATK & Perlengkapan Kantor',
            'Peralatan & Hardware',
            'Biaya Operasional & Bensin',
            'Jamuan & Konsumsi Client',
            'Pemeliharaan & Servis',
            'Lain-lain',
        ];

        return view('proofs.index', compact(
            'proofs',
            'settings',
            'totalExpense',
            'totalCount',
            'thisMonthExpense',
            'categories'
        ));
    }

    public function create()
    {
        $settings = CompanySetting::getSettings();
        $nextProofNumber = PurchaseProof::generateNextProofNumber();
        $categories = [
            'ATK & Perlengkapan Kantor',
            'Peralatan & Hardware',
            'Biaya Operasional & Bensin',
            'Jamuan & Konsumsi Client',
            'Pemeliharaan & Servis',
            'Lain-lain',
        ];

        return view('proofs.create', compact('settings', 'nextProofNumber', 'categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'proof_number' => 'required|string|unique:purchase_proofs,proof_number',
            'transaction_date' => 'required|date',
            'vendor_name' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'category' => 'required|string|max:100',
            'description' => 'nullable|string',
            'submitted_by' => 'nullable|string|max:100',
            'verified_by' => 'nullable|string|max:100',
            'finance_by' => 'nullable|string|max:100',
            'receipt_photo' => 'nullable|image|max:10240',
            'item_photo' => 'nullable|image|max:10240',
        ]);

        if ($request->hasFile('receipt_photo')) {
            $validated['receipt_photo'] = $request->file('receipt_photo')->store('proofs/receipts', 'public');
        }

        if ($request->hasFile('item_photo')) {
            $validated['item_photo'] = $request->file('item_photo')->store('proofs/items', 'public');
        }

        $proof = PurchaseProof::create($validated);

        return redirect()->route('proofs.show', $proof)
            ->with('success', 'Bukti pembelian berhasil disimpan dan siap dicetak dalam format A4!');
    }

    public function show(PurchaseProof $proof)
    {
        $settings = CompanySetting::getSettings();
        return view('proofs.show', compact('proof', 'settings'));
    }

    public function edit(PurchaseProof $proof)
    {
        $settings = CompanySetting::getSettings();
        $categories = [
            'ATK & Perlengkapan Kantor',
            'Peralatan & Hardware',
            'Biaya Operasional & Bensin',
            'Jamuan & Konsumsi Client',
            'Pemeliharaan & Servis',
            'Lain-lain',
        ];

        return view('proofs.edit', compact('proof', 'settings', 'categories'));
    }

    public function update(Request $request, PurchaseProof $proof)
    {
        $validated = $request->validate([
            'proof_number' => 'required|string|unique:purchase_proofs,proof_number,' . $proof->id,
            'transaction_date' => 'required|date',
            'vendor_name' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'category' => 'required|string|max:100',
            'description' => 'nullable|string',
            'submitted_by' => 'nullable|string|max:100',
            'verified_by' => 'nullable|string|max:100',
            'finance_by' => 'nullable|string|max:100',
            'receipt_photo' => 'nullable|image|max:10240',
            'item_photo' => 'nullable|image|max:10240',
        ]);

        if ($request->hasFile('receipt_photo')) {
            if ($proof->receipt_photo && Storage::disk('public')->exists($proof->receipt_photo)) {
                Storage::disk('public')->delete($proof->receipt_photo);
            }
            $validated['receipt_photo'] = $request->file('receipt_photo')->store('proofs/receipts', 'public');
        }

        if ($request->hasFile('item_photo')) {
            if ($proof->item_photo && Storage::disk('public')->exists($proof->item_photo)) {
                Storage::disk('public')->delete($proof->item_photo);
            }
            $validated['item_photo'] = $request->file('item_photo')->store('proofs/items', 'public');
        }

        $proof->update($validated);

        return redirect()->route('proofs.show', $proof)
            ->with('success', 'Data bukti pembelian berhasil diperbarui.');
    }

    public function destroy(PurchaseProof $proof)
    {
        if ($proof->receipt_photo && Storage::disk('public')->exists($proof->receipt_photo)) {
            Storage::disk('public')->delete($proof->receipt_photo);
        }
        if ($proof->item_photo && Storage::disk('public')->exists($proof->item_photo)) {
            Storage::disk('public')->delete($proof->item_photo);
        }

        $proof->delete();

        return redirect()->route('proofs.index')
            ->with('success', 'Bukti pembelian berhasil dihapus.');
    }

    public function print(PurchaseProof $proof)
    {
        $settings = CompanySetting::getSettings();
        return view('proofs.print', compact('proof', 'settings'));
    }

    public function batchPrint(Request $request)
    {
        $settings = CompanySetting::getSettings();
        $query = PurchaseProof::query();

        if ($request->filled('ids')) {
            $ids = explode(',', $request->ids);
            $query->whereIn('id', $ids);
        }

        if ($request->filled('month')) {
            $query->whereMonth('transaction_date', date('m', strtotime($request->month)))
                  ->whereYear('transaction_date', date('Y', strtotime($request->month)));
        }

        $proofs = $query->orderBy('transaction_date', 'asc')->get();

        return view('proofs.batch-print', compact('proofs', 'settings'));
    }

    public function exportCsv(Request $request): StreamedResponse
    {
        $proofs = PurchaseProof::orderBy('transaction_date', 'asc')->get();

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="rekap_bukti_pembelian_' . date('Ymd_His') . '.csv"',
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        return response()->stream(function () use ($proofs) {
            $output = fopen('php://output', 'w');
            fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF)); // BOM for Excel UTF-8
            
            fputcsv($output, ['No. Bukti', 'Tanggal', 'Vendor/Toko', 'Kategori', 'Nominal (Rp)', 'Keterangan', 'Diajukan Oleh', 'Diverifikasi Oleh', 'Status']);

            foreach ($proofs as $p) {
                fputcsv($output, [
                    $p->proof_number,
                    $p->transaction_date->format('d/m/Y'),
                    $p->vendor_name,
                    $p->category,
                    $p->amount,
                    $p->description,
                    $p->submitted_by,
                    $p->verified_by,
                    $p->status,
                ]);
            }
            fclose($output);
        }, 200, $headers);
    }
}
