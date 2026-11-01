<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mSetting', function (Blueprint $table) {
            $table->id('intSetting_ID');
            $table->string('txtSettingKey', 100)->unique();
            $table->text('txtSettingValue')->nullable();
            $table->text('txtDescription')->nullable();

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
        Schema::dropIfExists('mSetting');
    }
};

