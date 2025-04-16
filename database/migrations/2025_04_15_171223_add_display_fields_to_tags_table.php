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
        Schema::table('tags', function (Blueprint $table) {
            $table->string('color')->default('#6B7280')->after('slug'); // Default gray color
            $table->unsignedTinyInteger('priority')->default(0)->after('color'); // Higher number = higher priority
            $table->boolean('is_featured')->default(false)->after('priority');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tags', function (Blueprint $table) {
            $table->dropColumn(['color', 'priority', 'is_featured']);
        });
    }
};
