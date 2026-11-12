<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class PurchaseProof extends Model
{
    protected $fillable = [
        'proof_number',
        'transaction_date',
        'vendor_name',
        'amount',
        'category',
        'description',
        'receipt_photo',
        'item_photo',
        'submitted_by',
        'verified_by',
        'finance_by',
        'status',
    ];

    protected $casts = [
        'transaction_date' => 'date',
        'amount' => 'decimal:2',
    ];

    public function getFormattedAmountAttribute(): string
    {
        return 'Rp ' . number_format($this->amount, 0, ',', '.');
    }

    public function getReceiptPhotoUrlAttribute(): ?string
    {
        if (!$this->receipt_photo) return null;
        if (str_starts_with($this->receipt_photo, 'http') || str_starts_with($this->receipt_photo, 'data:')) {
            return $this->receipt_photo;
        }
        return asset('storage/' . $this->receipt_photo);
    }

    public function getItemPhotoUrlAttribute(): ?string
    {
        if (!$this->item_photo) return null;
        if (str_starts_with($this->item_photo, 'http') || str_starts_with($this->item_photo, 'data:')) {
            return $this->item_photo;
        }
        return asset('storage/' . $this->item_photo);
    }

    public static function generateNextProofNumber(): string
    {
        $year = date('Y');
        $month = date('m');
        $prefix = "TRX-{$year}{$month}-";
        
        $last = self::where('proof_number', 'LIKE', "{$prefix}%")
            ->orderBy('id', 'desc')
            ->first();

        if ($last) {
            $lastNumber = intval(substr($last->proof_number, strlen($prefix)));
            $nextNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $nextNumber = '0001';
        }

        return $prefix . $nextNumber;
    }
}
