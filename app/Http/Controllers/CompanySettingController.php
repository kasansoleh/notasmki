<?php

namespace App\Http\Controllers;

use App\Models\CompanySetting;
use Illuminate\Http\Request;

class CompanySettingController extends Controller
{
    public function index()
    {
        $settings = CompanySetting::getSettings();
        return view('settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'company_name' => 'required|string|max:255',
            'doc_title' => 'required|string|max:255',
            'company_address' => 'nullable|string|max:255',
            'company_phone' => 'nullable|string|max:100',
            'default_submitted_by' => 'nullable|string|max:100',
            'default_verified_by' => 'nullable|string|max:100',
            'default_finance_by' => 'nullable|string|max:100',
        ]);

        $settings = CompanySetting::getSettings();
        $settings->update($validated);

        return redirect()->route('settings.index')
            ->with('success', 'Pengaturan dokumen dan profil instansi berhasil diperbarui.');
    }
}
