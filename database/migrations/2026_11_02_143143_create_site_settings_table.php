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
        Schema::create('site_settings', function (Blueprint $table) {
            $table->id();

            $table->string('owner_name')->nullable();
            $table->string('headline')->nullable();
            $table->text('tagline')->nullable();
            $table->string('default_theme')->nullable();
            $table->string('source_url')->nullable();

            $table->string('assistant_name')->nullable();
            $table->string('chat_title')->nullable();
            $table->string('chat_subtitle')->nullable();
            $table->string('chat_model')->nullable();
            $table->unsignedInteger('chat_timeout')->nullable();
            $table->unsignedInteger('chat_max_message_length')->nullable();
            $table->unsignedInteger('chat_max_turns')->nullable();
            $table->unsignedInteger('chat_rate_limit_per_minute')->nullable();
            $table->unsignedInteger('chat_rate_limit_per_day')->nullable();

            $table->string('robot_shape')->nullable();
            $table->string('robot_color')->nullable();
            $table->string('robot_trim_color')->nullable();
            $table->string('robot_screen_color')->nullable();

            $table->boolean('widget_enabled')->nullable();
            $table->string('widget_position')->nullable();
            $table->string('widget_label')->nullable();
            $table->string('widget_note')->nullable();

            $table->unsignedInteger('contact_rate_limit')->nullable();
            $table->unsignedInteger('contact_rate_limit_decay_minutes')->nullable();
            $table->boolean('allow_ai_training')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('site_settings');
    }
};
