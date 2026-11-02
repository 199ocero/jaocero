<feed xmlns="http://www.w3.org/2005/Atom">
    <id>{{ route('feed') }}</id>
    <title>{{ config('portfolio.owner_name') }}</title>
    <subtitle>{{ config('portfolio.tagline') }}</subtitle>
    <link rel="self" type="application/atom+xml" href="{{ route('feed') }}" />
    <link rel="alternate" type="text/html" href="{{ route('blog.index') }}" />
    <updated>{{ ($posts->max('updated_at') ?? now())->toAtomString() }}</updated>
    <author>
        <name>{{ config('portfolio.owner_name') }}</name>
        <uri>{{ route('about') }}</uri>
    </author>
@foreach ($posts as $post)
    <entry>
        <id>{{ route('blog.show', $post) }}</id>
        <title>{{ $post->title }}</title>
        <link rel="alternate" type="text/html" href="{{ route('blog.show', $post) }}" />
        <published>{{ $post->published_at?->toAtomString() }}</published>
        <updated>{{ ($post->updated_at ?? $post->published_at)?->toAtomString() }}</updated>
        <summary>{{ $post->excerpt }}</summary>
        <content type="html">{{ (string) \App\Support\Markdown::render($post->body) }}</content>
        @foreach ($post->tags ?? [] as $tag)
        <category term="{{ $tag }}" />
        @endforeach
    </entry>
@endforeach
</feed>
