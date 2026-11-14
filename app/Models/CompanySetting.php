<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CompanySetting extends Model
{
    protected $fillable = [
        'company_name',
        'doc_title',
        'company_address',
        'company_phone',
        'default_submitted_by',
        'default_verified_by',
        'default_finance_by',
    ];

    public static function getSettings(): self
    {
        return self::firstOrCreate(
            ['id' => 1],
            [
                'company_name' => 'SMKS ISLAM 1 KOTA BLITAR',
                'doc_title' => 'LAMPIRAN BUKTI PEMBELIAN & NOTA',
                'company_address' => 'Gedung Cyber 2 Lantai 15, Jl. H.R. Rasuna Said, Jakarta Selatan',
                'company_phone' => '(021) 555-0199',
                'default_submitted_by' => 'Bendahara SMKS Islam 1 Blitar',
                'default_verified_by' => 'Supervisor / Manager',
                'default_finance_by' => 'Kajur Teknik Pemesinan (TPm)',
            ]
        );
    }
}
