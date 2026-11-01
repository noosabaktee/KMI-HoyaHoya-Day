<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('logWhatsApp', function (Blueprint $table) {
            $table->id('intWhatsApp_ID');
            $table->string('txtRecipientNumber', 30);
            $table->text('txtMessage');
            $table->string('txtDirection', 20)->default('OUTGOING'); // OUTGOING, INCOMING
            $table->string('txtMediaUrl', 255)->nullable();
            $table->string('txtStatus', 30)->default('SENT'); // SENT, DELIVERED, FAILED, RECEIVED
            $table->text('txtApiResponse')->nullable();
            $table->unsignedBigInteger('intEmployee_ID')->nullable();
            $table->unsignedBigInteger('intVoucher_ID')->nullable();

            // KMI Log Mandatory Columns
            $table->string('txtInsertedBy', 100)->default('SYSTEM');
            $table->timestamp('dtmInserted')->useCurrent();
            $table->string('txtUpdatedBy', 100)->default('SYSTEM');
            $table->timestamp('dtmUpdated')->useCurrent();

            $table->foreign('intEmployee_ID')->references('intEmployee_ID')->on('mEmployee')->onDelete('set null');
            $table->foreign('intVoucher_ID')->references('intVoucher_ID')->on('mVoucher')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('logWhatsApp');
    }
};

