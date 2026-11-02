<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Portfolio Owner
    |--------------------------------------------------------------------------
    |
    | Defaults for the site settings edited in the admin panel (Settings ›
    | Site settings). Once those are saved, the stored values override
    | everything in this file that has a matching settings field.
    |
    */

    'owner_name' => 'Alex Morgan',

    /*
     * The home page hero reads "{greeting} {headline}". The highlighted phrase (or a phrase
     * wrapped in *asterisks* in the headline) gets a hand-drawn marker highlight.
     */
    'headline' => 'a Laravel developer who builds with AI',

    'headline_highlight' => 'AI',

    /*
     * The rest of the home page hero. {name} is replaced with the owner's name.
     * Leave a note or the cursor label empty to hide it.
     */
    'hero' => [
        'greeting' => "Hi, I'm {name} —",
        'note' => 'hi there!',
        'chat_note' => 'psst… ask my AI anything',
        'cursor_label' => 'you?',

        /*
         * Tappable stickers floating around the chat on wide screens ("xl" = 1280px+, "2xl" = 1536px+).
         * Up to four items of ['text' => ..., 'color' => sky|amber|lime|rose]. Empty: your first four skills.
         */
        'stickers' => [
            'enabled' => true,
            'items' => [],
            'breakpoint' => 'xl',
        ],
    ],

    'tagline' => 'My corner of the internet: a portfolio you can talk to. Ask my AI anything about my work.',

    'site_url' => env('APP_URL', 'http://localhost'),

    'assistant_name' => 'AI assistant',

    /*
     * The theme first-time visitors see: "light" (warm paper) or "dark".
     * Visitors can switch with the toggle in the navbar.
     */
    'default_theme' => 'light',

    /*
     * Optional link to this site's source code, shown in the footer.
     */
    'source_url' => null,

    /*
    |--------------------------------------------------------------------------
    | Admin Access
    |--------------------------------------------------------------------------
    |
    | Comma-separated list of email addresses allowed to sign in to the
    | Filament admin panel at /admin. Anyone else is denied access.
    |
    */

    'admin_emails' => array_values(array_filter(array_map(
        'trim',
        explode(',', (string) env('PORTFOLIO_ADMIN_EMAILS', '')),
    ))),

    /*
    |--------------------------------------------------------------------------
    | AI Chat
    |--------------------------------------------------------------------------
    |
    | The model used by the portfolio assistant, plus the safety and cost
    | limits applied to every visitor. The provider and its API key stay in
    | .env (config/ai.php), so secrets never reach the database.
    |
    */

    'chat' => [
        'provider' => env('PORTFOLIO_CHAT_PROVIDER', env('AI_PROVIDER', 'anthropic')),
        'model' => 'claude-haiku-5-5',
        'timeout' => 30,
        'max_message_length' => 500,
        'max_turns' => 20,
        'rate_limit' => [
            'per_minute' => 10,
            'per_day' => 50,
        ],

        // The chat header. {name} is replaced with the owner's name.
        'title' => "{name}'s AI assistant",
        'subtitle' => "Answers from {name}'s real projects and experience",

        // The assistant's robot-heads avatar (https://www.npmjs.com/package/robot-heads).
        'avatar' => [
            'shape' => 'rectangle',
            'color' => '#2b49a3',
            'trim_color' => '#93a6c8',
            'screen_color' => '#e8f2ff',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Floating Chat Widget
    |--------------------------------------------------------------------------
    |
    | The chat button shown on every page except home. Leave the note empty
    | to hide the hand-written nudge next to the button.
    |
    */

    'chat_widget' => [
        'enabled' => true,
        'position' => 'bottom-right',
        'label' => 'Ask my AI',
        'note' => 'psst… still here',
    ],

    /*
    |--------------------------------------------------------------------------
    | Contact Form
    |--------------------------------------------------------------------------
    */

    'contact' => [
        'rate_limit' => [
            'max_attempts' => 3,
            'decay_minutes' => 10,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | SEO & AI Discoverability
    |--------------------------------------------------------------------------
    |
    | AI search bots (which cite and link to your site) are always allowed.
    | AI training bots can be opted out of in the admin panel's site settings.
    | Update these lists as vendors introduce new crawler user agents.
    |
    */

    'seo' => [
        'allow_ai_training' => true,

        // Fallback social share image. Uploading a share image in the admin's About page overrides it.
        'default_og_image' => '/og-image.png',

        'ai_search_bots' => [
            'OAI-SearchBot',
            'ChatGPT-User',
            'Claude-SearchBot',
            'Claude-User',
            'PerplexityBot',
            'Perplexity-User',
        ],

        'ai_training_bots' => [
            'GPTBot',
            'ClaudeBot',
            'Google-Extended',
            'Applebot-Extended',
            'CCBot',
            'meta-externalagent',
            'Bytespider',
            'Amazonbot',
        ],
    ],

];
