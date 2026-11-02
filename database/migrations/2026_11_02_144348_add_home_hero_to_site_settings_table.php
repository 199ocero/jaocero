<?php

use App\Support\Highlight;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('site_settings', function (Blueprint $table) {
            $table->string('hero_greeting')->nullable()->after('owner_name');
            $table->string('headline_highlight')->nullable()->after('headline');
            $table->string('hero_note')->nullable()->after('tagline');
            $table->string('hero_chat_note')->nullable()->after('hero_note');
            $table->string('hero_cursor_label')->nullable()->after('hero_chat_note');
        });

        // Existing settings keep the hero text the site showed before these became editable.
        DB::table('site_settings')->update([
            'hero_greeting' => "Hi, I'm {name} —",
            'hero_note' => 'hi there!',
            'hero_chat_note' => 'psst… ask my AI anything',
            'hero_cursor_label' => 'you?',
        ]);

        // Move a phrase marked with *asterisks* in an existing headline into the new highlight field.
        DB::table('site_settings')->whereLike('headline', '%*%*%')->get(['id', 'headline'])
            ->each(function (object $settings): void {
                preg_match('/\*([^*]+)\*/', (string) $settings->headline, $match);

                DB::table('site_settings')->where('id', $settings->id)->update([
                    'headline' => Highlight::plain($settings->headline),
                    'headline_highlight' => $match[1] ?? null,
                ]);
            });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('site_settings', function (Blueprint $table) {
            $table->dropColumn(['hero_greeting', 'headline_highlight', 'hero_note', 'hero_chat_note', 'hero_cursor_label']);
        });
    }
};
