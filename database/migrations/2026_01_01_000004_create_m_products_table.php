<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mProduct', function (Blueprint $table) {
            $table->id('intProduct_ID');
            $table->unsignedBigInteger('intMerchant_ID');
            $table->unsignedBigInteger('intCategory_ID')->nullable();
            $table->string('txtProductCode', 50)->nullable();
            $table->string('txtProductName', 150);
            $table->decimal('floatPrice', 14, 2)->default(0);
            $table->integer('intStock')->default(0);
            $table->string('txtUnit', 30)->default('Pcs');
            $table->string('txtProductImage', 255)->nullable();
            $table->text('txtDescription')->nullable();

            // KMI Master Mandatory Columns
            $table->string('txtInsertedBy', 100)->default('SYSTEM');
            $table->timestamp('dtmInserted')->useCurrent();
            $table->string('txtUpdatedBy', 100)->default('SYSTEM');
            $table->timestamp('dtmUpdated')->useCurrent();
            $table->smallInteger('bitActive')->default(1);

            $table->foreign('intMerchant_ID')->references('intMerchant_ID')->on('mMerchant')->onDelete('cascade');
            $table->foreign('intCategory_ID')->references('intCategory_ID')->on('mCategory')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mProduct');
    }
};

