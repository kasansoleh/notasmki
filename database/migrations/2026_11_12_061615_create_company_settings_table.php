<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('company_settings', function (Blueprint $table) {
            $table->id();
            $table->string('company_name')->default('SMKS ISLAM 1 KOTA BLITAR');
            $table->string('doc_title')->default('LAMPIRAN BUKTI PEMBELIAN & NOTA');
            $table->string('company_address')->nullable();
            $table->string('company_phone')->nullable();
            $table->string('default_submitted_by')->default('Bendahara SMKS Islam 1 Blitar');
            $table->string('default_verified_by')->nullable();
            $table->string('default_finance_by')->default('Kajur TPm');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('company_settings');
    }
};
