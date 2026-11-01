<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('trPenjualan_Details', function (Blueprint $table) {
            $table->id('intPenjualan_Detail_ID');
            $table->unsignedBigInteger('intPenjualan_ID');
            $table->unsignedBigInteger('intProduct_ID')->nullable();
            $table->string('txtProductName', 150);
            $table->decimal('floatPrice', 14, 2)->default(0);
            $table->integer('intQuantity')->default(1);
            $table->decimal('floatSubtotal', 14, 2)->default(0);

            // KMI Transaction Detail Mandatory Columns
            $table->string('txtInsertedBy', 100)->default('SYSTEM');
            $table->timestamp('dtmInserted')->useCurrent();

            $table->foreign('intPenjualan_ID')->references('intPenjualan_ID')->on('trPenjualan')->onDelete('cascade');
            $table->foreign('intProduct_ID')->references('intProduct_ID')->on('mProduct')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('trPenjualan_Details');
    }
};

