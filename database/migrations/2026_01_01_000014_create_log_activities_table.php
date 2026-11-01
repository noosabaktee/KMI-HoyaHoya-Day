<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('logActivity', function (Blueprint $table) {
            $table->id('intActivity_ID');
            $table->string('txtActivityType', 50);
            $table->text('txtDescription');
            $table->string('txtIpAddress', 45)->nullable();
            $table->unsignedBigInteger('intUser_ID')->nullable();

            // KMI Log Mandatory Columns
            $table->string('txtInsertedBy', 100)->default('SYSTEM');
            $table->timestamp('dtmInserted')->useCurrent();
            $table->string('txtUpdatedBy', 100)->default('SYSTEM');
            $table->timestamp('dtmUpdated')->useCurrent();

            $table->foreign('intUser_ID')->references('intUser_ID')->on('mUser')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('logActivity');
    }
};

