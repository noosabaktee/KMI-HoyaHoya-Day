<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mMerchant', function (Blueprint $table) {
            $table->id('intMerchant_ID');
            $table->string('txtMerchantCode', 50)->unique();
            $table->string('txtMerchantName', 150);
            $table->string('txtOwnerName', 150);
            $table->string('txtPhone', 30);
            $table->string('txtBoothNumber', 50)->nullable();
            $table->string('txtCategory', 100)->nullable();
            $table->text('txtDescription')->nullable();
            $table->string('txtBankName', 50)->nullable();
            $table->string('txtBankAccountNumber', 50)->nullable();
            $table->string('txtBankAccountHolder', 150)->nullable();

            // KMI Master Mandatory Columns
            $table->string('txtInsertedBy', 100)->default('SYSTEM');
            $table->timestamp('dtmInserted')->useCurrent();
            $table->string('txtUpdatedBy', 100)->default('SYSTEM');
            $table->timestamp('dtmUpdated')->useCurrent();
            $table->smallInteger('bitActive')->default(1);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mMerchant');
    }
};

