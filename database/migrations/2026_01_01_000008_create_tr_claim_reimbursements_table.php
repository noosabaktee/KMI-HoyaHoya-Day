<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('trClaimReimbursement', function (Blueprint $table) {
            $table->id('intClaim_ID');
            $table->string('txtClaimNumber', 50)->unique();
            $table->unsignedBigInteger('intMerchant_ID');
            $table->decimal('floatTotalClaimAmount', 14, 2)->default(0);
            $table->integer('intTotalTransactions')->default(0);
            $table->string('txtBankName', 50)->nullable();
            $table->string('txtBankAccountNumber', 50)->nullable();
            $table->string('txtBankAccountHolder', 150)->nullable();
            $table->string('txtStatus', 30)->default('Diajukan'); // Diajukan, Disetujui, Dibayar, Ditolak
            $table->text('txtRejectionReason')->nullable();
            $table->string('txtReceiptFile', 255)->nullable();
            $table->timestamp('dtmSubmittedAt')->useCurrent();
            $table->timestamp('dtmApprovedAt')->nullable();
            $table->timestamp('dtmPaidAt')->nullable();
            $table->text('txtNotes')->nullable();

            // KMI Transaction Mandatory Columns
            $table->string('txtInsertedBy', 100)->default('SYSTEM');
            $table->timestamp('dtmInserted')->useCurrent();

            $table->foreign('intMerchant_ID')->references('intMerchant_ID')->on('mMerchant')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('trClaimReimbursement');
    }
};

