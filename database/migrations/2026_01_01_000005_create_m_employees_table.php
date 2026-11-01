<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mEmployee', function (Blueprint $table) {
            $table->id('intEmployee_ID');
            $table->string('txtEmployeeNik', 50)->unique();
            $table->string('txtEmployeeName', 150);
            $table->string('txtEmployeePhone', 30);
            $table->string('txtDepartment', 100)->nullable();

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
        Schema::dropIfExists('mEmployee');
    }
};

