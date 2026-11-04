<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mUnit', function (Blueprint $table) {
            $table->id('intUnit_ID');
            $table->string('txtUnitCode', 30)->unique();
            $table->string('txtUnitName', 50);

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
        Schema::dropIfExists('mUnit');
    }
};

