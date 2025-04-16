<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('cars', function (Blueprint $table) {
            $table->string('fuel_type')->nullable()->after('weight');
            $table->integer('engine_capacity')->nullable()->after('fuel_type');
            $table->integer('power_kw')->nullable()->after('engine_capacity');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cars', function (Blueprint $table) {
            $table->dropColumn(['fuel_type', 'engine_capacity', 'power_kw']);
        });
    }
};
