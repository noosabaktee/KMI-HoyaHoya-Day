<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('trVoucherUsage', function (Blueprint $table) {
            $table->id('intVoucherUsage_ID');
            $table->unsignedBigInteger('intVoucher_ID');
            $table->unsignedBigInteger('intPenjualan_ID');
            $table->decimal('floatAmountUsed', 14, 2);
            $table->decimal('floatBalanceBefore', 14, 2);
            $table->decimal('floatBalanceAfter', 14, 2);

            // KMI Transaction Mandatory Columns
            $table->string('txtInsertedBy', 100)->default('SYSTEM');
            $table->timestamp('dtmInserted')->useCurrent();

            $table->foreign('intVoucher_ID')->references('intVoucher_ID')->on('mVoucher')->onDelete('cascade');
            $table->foreign('intPenjualan_ID')->references('intPenjualan_ID')->on('trPenjualan')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('trVoucherUsage');
    }
};

