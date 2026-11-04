<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mDepartment', function (Blueprint $table) {
            $table->id('intDepartment_ID');
            $table->string('txtDepartmentCode', 50)->unique();
            $table->string('txtDepartmentName', 150);

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
        Schema::dropIfExists('mDepartment');
    }
};

