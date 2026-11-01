<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mVoucher', function (Blueprint $table) {
            $table->id('intVoucher_ID');
            $table->unsignedBigInteger('intEmployee_ID');
            $table->string('txtVoucherCode', 100)->unique();
            $table->decimal('floatInitialBalance', 14, 2)->default(50000);
            $table->decimal('floatCurrentBalance', 14, 2)->default(50000);
            $table->string('txtStatus', 30)->default('Dibuat'); // Dibuat, Terkirim, Aktif, Habis
            $table->string('txtBarcodeCardPath', 255)->nullable();
            $table->timestamp('dtmSentAt')->nullable();
            $table->timestamp('dtmActivatedAt')->nullable();

            // KMI Master Mandatory Columns
            $table->string('txtInsertedBy', 100)->default('SYSTEM');
            $table->timestamp('dtmInserted')->useCurrent();
            $table->string('txtUpdatedBy', 100)->default('SYSTEM');
            $table->timestamp('dtmUpdated')->useCurrent();
            $table->smallInteger('bitActive')->default(1);

            $table->foreign('intEmployee_ID')->references('intEmployee_ID')->on('mEmployee')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mVoucher');
    }
};

