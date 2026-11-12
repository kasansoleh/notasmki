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
        Schema::create('purchase_proofs', function (Blueprint $table) {
            $table->id();
            $table->string('proof_number')->unique();
            $table->date('transaction_date');
            $table->string('vendor_name');
            $table->decimal('amount', 15, 2);
            $table->string('category')->default('Operasional');
            $table->text('description')->nullable();
            $table->string('receipt_photo')->nullable(); // Foto Nota (Setengah Atas A4)
            $table->string('item_photo')->nullable();    // Foto Barang (Setengah Bawah A4)
            $table->string('submitted_by')->nullable();
            $table->string('verified_by')->nullable();
            $table->string('finance_by')->nullable();
            $table->string('status')->default('verified'); // draft, verified, approved
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchase_proofs');
    }
};
