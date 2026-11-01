<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('trPenjualan', function (Blueprint $table) {
            $table->id('intPenjualan_ID');
            $table->string('txtInvoiceNumber', 50)->unique();
            $table->unsignedBigInteger('intMerchant_ID');
            $table->unsignedBigInteger('intUser_ID');
            $table->unsignedBigInteger('intEmployee_ID')->nullable();
            $table->unsignedBigInteger('intVoucher_ID')->nullable();
            $table->decimal('floatTotalAmount', 14, 2)->default(0);
            $table->decimal('floatVoucherAmount', 14, 2)->default(0);
            $table->decimal('floatCashAmount', 14, 2)->default(0);
            $table->decimal('floatChangeAmount', 14, 2)->default(0);
            $table->string('txtPaymentType', 30)->default('VOUCHER'); // VOUCHER, CASH, SPLIT
            $table->text('txtNotes')->nullable();
            $table->smallInteger('bitIsClaimed')->default(0);
            $table->unsignedBigInteger('intClaim_ID')->nullable();

            // KMI Transaction Mandatory Columns
            $table->string('txtInsertedBy', 100)->default('SYSTEM');
            $table->timestamp('dtmInserted')->useCurrent();

            $table->foreign('intMerchant_ID')->references('intMerchant_ID')->on('mMerchant')->onDelete('cascade');
            $table->foreign('intUser_ID')->references('intUser_ID')->on('mUser')->onDelete('cascade');
            $table->foreign('intEmployee_ID')->references('intEmployee_ID')->on('mEmployee')->onDelete('set null');
            $table->foreign('intVoucher_ID')->references('intVoucher_ID')->on('mVoucher')->onDelete('set null');
            $table->foreign('intClaim_ID')->references('intClaim_ID')->on('trClaimReimbursement')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('trPenjualan');
    }
};

