<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mCategory', function (Blueprint $table) {
            $table->id('intCategory_ID');
            $table->unsignedBigInteger('intMerchant_ID')->nullable();
            $table->string('txtCategoryName', 100);
            $table->string('txtCategorySlug', 100)->nullable();

            // KMI Master Mandatory Columns
            $table->string('txtInsertedBy', 100)->default('SYSTEM');
            $table->timestamp('dtmInserted')->useCurrent();
            $table->string('txtUpdatedBy', 100)->default('SYSTEM');
            $table->timestamp('dtmUpdated')->useCurrent();
            $table->smallInteger('bitActive')->default(1);

            $table->foreign('intMerchant_ID')->references('intMerchant_ID')->on('mMerchant')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mCategory');
    }
};

