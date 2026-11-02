<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Portfolio Owner
    |--------------------------------------------------------------------------
    |
    | The basic identity of the person this portfolio belongs to. Everything
    | else (bio, projects, skills, experience...) is managed in the admin
    | panel, so forking this project only requires editing your `.env`.
    |
    */

    'owner_name' => env('PORTFOLIO_OWNER_NAME', 'Alex Morgan'),

    // Wrap a phrase in *asterisks* to give it a hand-drawn marker highlight on the home page.
    'headline' => env('PORTFOLIO_HEADLINE', 'a Laravel developer who builds with *AI*'),

    'tagline' => env(
        'PORTFOLIO_TAGLINE',
        'My corner of the internet: a portfolio you can talk to. Ask my AI anything about my work.',
    ),

    'site_url' => env('PORTFOLIO_SITE_URL', env('APP_URL', 'http://localhost')),

    'assistant_name' => env('PORTFOLIO_ASSISTANT_NAME', 'AI assistant'),

    /*
     * The theme first-time visitors see: "light" (warm paper) or "dark".
     * Visitors can switch with the toggle in the navbar.
     */
    'default_theme' => env('PORTFOLIO_DEFAULT_THEME', 'light') === 'dark' ? 'dark' : 'light',

    /*
     * Optional link to this site's source code, shown in the footer.
     */
    'source_url' => env('PORTFOLIO_SOURCE_URL'),

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
    | Provider and model used by the portfolio assistant, plus the safety and
    | cost limits applied to every visitor. Providers are configured in
    | config/ai.php; API keys never leave the server.
    |
    */

    'chat' => [
        'provider' => env('PORTFOLIO_CHAT_PROVIDER', env('AI_PROVIDER', 'anthropic')),
        'model' => env('PORTFOLIO_CHAT_MODEL', 'claude-haiku-5-5'),
        'timeout' => (int) env('PORTFOLIO_CHAT_TIMEOUT', 30),
        'max_message_length' => (int) env('PORTFOLIO_CHAT_MAX_MESSAGE_LENGTH', 500),
        'max_turns' => (int) env('PORTFOLIO_CHAT_MAX_TURNS', 20),
        'rate_limit' => [
            'per_minute' => (int) env('PORTFOLIO_CHAT_RATE_LIMIT_PER_MINUTE', 10),
            'per_day' => (int) env('PORTFOLIO_CHAT_RATE_LIMIT_PER_DAY', 50),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Contact Form
    |--------------------------------------------------------------------------
    */

    'contact' => [
        'rate_limit' => [
            'max_attempts' => (int) env('PORTFOLIO_CONTACT_RATE_LIMIT', 3),
            'decay_minutes' => (int) env('PORTFOLIO_CONTACT_RATE_LIMIT_DECAY_MINUTES', 10),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | SEO & AI Discoverability
    |--------------------------------------------------------------------------
    |
    | AI search bots (which cite and link to your site) are always allowed.
    | AI training bots can be opted out of via PORTFOLIO_ALLOW_AI_TRAINING.
    | Update these lists as vendors introduce new crawler user agents.
    |
    */

    'seo' => [
        'allow_ai_training' => (bool) env('PORTFOLIO_ALLOW_AI_TRAINING', true),

        'default_og_image' => env('PORTFOLIO_DEFAULT_OG_IMAGE', '/og-image.png'),

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
