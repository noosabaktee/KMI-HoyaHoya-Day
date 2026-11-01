<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mUser', function (Blueprint $table) {
            $table->id('intUser_ID');
            $table->string('txtUsername', 50)->unique();
            $table->string('txtFullName', 150);
            $table->string('txtEmail', 150)->nullable()->unique();
            $table->string('txtPassword', 255);
            $table->string('txtRole', 30)->default('umkm'); // admin, umkm
            $table->unsignedBigInteger('intMerchant_ID')->nullable();
            $table->string('txtPhone', 30)->nullable();
            $table->rememberToken('txtRememberToken');

            // KMI Master Mandatory Columns
            $table->string('txtInsertedBy', 100)->default('SYSTEM');
            $table->timestamp('dtmInserted')->useCurrent();
            $table->string('txtUpdatedBy', 100)->default('SYSTEM');
            $table->timestamp('dtmUpdated')->useCurrent();
            $table->smallInteger('bitActive')->default(1);

            $table->foreign('intMerchant_ID')->references('intMerchant_ID')->on('mMerchant')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mUser');
    }
};

