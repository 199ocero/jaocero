@props(['seo' => null])

@php
    $seo ??= new \App\Support\Seo\SeoData(
        title: config('portfolio.owner_name'),
        description: config('portfolio.tagline'),
    );
@endphp

<!DOCTYPE html>
<html
    lang="{{ str_replace('_', '-', app()->getLocale()) }}"
    data-default-theme="{{ config('portfolio.default_theme') }}"
    @class(['dark' => config('portfolio.default_theme') === 'dark'])
>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <x-seo :seo="$seo" />

        <link rel="icon" href="/favicon.ico" sizes="any">
        <link rel="icon" href="/favicon.svg" type="image/svg+xml">
        <link rel="apple-touch-icon" href="/apple-touch-icon.png">
        <meta name="theme-color" content="#161514" media="(prefers-color-scheme: dark)">
        <meta name="theme-color" content="#faf9f6" media="(prefers-color-scheme: light)">

        {{-- Apply the visitor's saved theme (or the configured default) before paint to avoid a flash. --}}
        <script>
            (() => {
                const applyTheme = () => {
                    let theme = null;
                    try { theme = localStorage.getItem('theme'); } catch (error) {}
                    theme ??= document.documentElement.dataset.defaultTheme;
                    document.documentElement.classList.toggle('dark', theme === 'dark');
                    // Lets CSS hide not-yet-revealed elements only when JavaScript can reveal them.
                    document.documentElement.classList.add('js');
                };
                applyTheme();
                document.addEventListener('livewire:navigated', applyTheme);
            })();
        </script>

        @fonts
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="flex min-h-dvh flex-col bg-canvas font-sans text-fg">
        <a href="#main" class="sr-only focus:not-sr-only focus:fixed focus:top-3 focus:left-3 focus:z-50 focus:rounded-lg focus:bg-accent focus:px-4 focus:py-2 focus:text-on-accent">
            Skip to content
        </a>

        @include('partials.navbar')

        <main id="main" class="flex-1">
            {{ $slot }}
        </main>

        <x-site-footer />

        @include('partials.chat-widget')
    </body>
</html>
