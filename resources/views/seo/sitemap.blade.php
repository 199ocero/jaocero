<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
@foreach ($pages as $url)
    <url>
        <loc>{{ $url }}</loc>
    </url>
@endforeach
@foreach ($projects as $project)
    <url>
        <loc>{{ route('projects.show', $project) }}</loc>
        @if ($project->updated_at)
        <lastmod>{{ $project->updated_at->toAtomString() }}</lastmod>
        @endif
    </url>
@endforeach
@foreach ($posts as $post)
    <url>
        <loc>{{ route('blog.show', $post) }}</loc>
        @if ($post->updated_at)
        <lastmod>{{ $post->updated_at->toAtomString() }}</lastmod>
        @endif
    </url>
@endforeach
</urlset>
