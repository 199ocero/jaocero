<title>{{ $seo->fullTitle() }}</title>
<meta name="description" content="{{ $seo->description }}">
<link rel="canonical" href="{{ $seo->canonicalUrl() }}">
<meta name="robots" content="index, follow, max-image-preview:large">
@if ($markdownUrl())
    <link rel="alternate" type="text/markdown" href="{{ $markdownUrl() }}">
@endif
<link rel="alternate" type="application/atom+xml" title="{{ config('portfolio.owner_name') }}" href="{{ route('feed') }}">

<meta property="og:type" content="{{ $seo->type }}">
<meta property="og:site_name" content="{{ config('portfolio.owner_name') }}">
<meta property="og:title" content="{{ $seo->fullTitle() }}">
<meta property="og:description" content="{{ $seo->description }}">
<meta property="og:url" content="{{ $seo->canonicalUrl() }}">
<meta property="og:image" content="{{ $imageUrl() }}">
<meta property="og:locale" content="{{ str_replace('-', '_', app()->getLocale()) }}">
@if ($seo->publishedAt)
    <meta property="article:published_time" content="{{ $seo->publishedAt->toIso8601String() }}">
@endif
@if ($seo->modifiedAt)
    <meta property="article:modified_time" content="{{ $seo->modifiedAt->toIso8601String() }}">
@endif

<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="{{ $seo->fullTitle() }}">
<meta name="twitter:description" content="{{ $seo->description }}">
<meta name="twitter:image" content="{{ $imageUrl() }}">

<script type="application/ld+json">{!! $jsonLd() !!}</script>
