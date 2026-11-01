<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('trClaimReimbursement_Details', function (Blueprint $table) {
            $table->id('intClaim_Detail_ID');
            $table->unsignedBigInteger('intClaim_ID');
            $table->unsignedBigInteger('intPenjualan_ID');
            $table->decimal('floatVoucherAmount', 14, 2);

            // KMI Transaction Detail Mandatory Columns
            $table->string('txtInsertedBy', 100)->default('SYSTEM');
            $table->timestamp('dtmInserted')->useCurrent();

            $table->foreign('intClaim_ID')->references('intClaim_ID')->on('trClaimReimbursement')->onDelete('cascade');
            $table->foreign('intPenjualan_ID')->references('intPenjualan_ID')->on('trPenjualan')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('trClaimReimbursement_Details');
    }
};

