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
        Schema::table('site_settings', function (Blueprint $table) {
            $table->boolean('stickers_enabled')->nullable()->after('hero_cursor_label');
            $table->json('stickers')->nullable()->after('stickers_enabled');
            $table->string('stickers_breakpoint')->nullable()->after('stickers');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('site_settings', function (Blueprint $table) {
            $table->dropColumn(['stickers_enabled', 'stickers', 'stickers_breakpoint']);
        });
    }
};
