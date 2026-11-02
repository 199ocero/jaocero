<?php

namespace App\Support\Seo;

use App\Models\About;
use App\Models\Experience;
use App\Models\Post;
use App\Models\Project;
use App\Models\Skill;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;

/**
 * Markdown versions of the public pages, for AI agents and LLM tools.
 *
 * Each page named in PAGES is served as markdown at its "markdown.{route}" route
 * (e.g. /projects/{slug}.md) and to requests that prefer `Accept: text/markdown`.
 */
class PageMarkdown
{
    /**
     * Route names of the HTML pages that have a markdown version.
     */
    public const array PAGES = ['home', 'about', 'projects.index', 'projects.show', 'blog.index', 'blog.show'];

    /**
     * The markdown URL for a page, or null when the page has no markdown version.
     *
     * @param  array<string, mixed>  $parameters
     */
    public static function url(?string $routeName, array $parameters = []): ?string
    {
        return in_array($routeName, self::PAGES, true) ? route('markdown.'.$routeName, $parameters) : null;
    }

    /**
     * Render a page as markdown. Missing or unpublished projects and posts abort with a 404.
     *
     * @param  array<string, mixed>  $parameters  Route parameters, as models or slugs
     */
    public static function render(string $routeName, array $parameters = []): string
    {
        return match ($routeName) {
            'home' => self::home(),
            'about' => self::about(),
            'projects.index' => self::projects(),
            'projects.show' => ProjectMarkdown::render(self::publishedProject($parameters['project'] ?? null)),
            'blog.index' => self::blog(),
            'blog.show' => PostMarkdown::render(self::publishedPost($parameters['post'] ?? null)),
            default => abort(404),
        };
    }

    public static function home(): string
    {
        $about = About::current();

        return MarkdownDocument::make()
            ->heading((string) config('portfolio.owner_name'))
            ->quote((string) config('portfolio.tagline'))
            ->paragraph(implode("\n\n", $about->bioParagraphs()))
            ->section('Featured projects', self::projectLinks(Project::published()->featured()->limit(3)->get()))
            ->section('Latest writing', self::postLinks(Post::published()->limit(3)->get()))
            ->section('More', implode("\n", [
                '- '.MarkdownDocument::link('About', route('markdown.about'), 'Bio, quick answers, skills and experience'),
                '- '.MarkdownDocument::link('Contact', route('contact'), 'Send a message'),
                '- '.MarkdownDocument::link('llms.txt', route('llms.index'), 'An index of this site for LLMs'),
            ]))
            ->toString();
    }

    /**
     * The owner's profile, quick answers, skills and experience.
     */
    public static function about(): string
    {
        $quickAnswers = array_map(
            fn (array $item): string => "### {$item['question']}\n\n{$item['answer']}",
            QuickAnswers::all(),
        );

        $skills = Skill::groupedByCategory()
            ->map(fn (EloquentCollection $skills, int|string $category): string => "- {$category}: ".$skills->pluck('name')->join(', '))
            ->implode("\n");

        $experience = Experience::latestFirst()->get()
            ->map(fn (Experience $experience): string => MarkdownDocument::make()
                ->heading("{$experience->role} at {$experience->company}", 3)
                ->paragraph($experience->period())
                ->paragraph($experience->description)
                ->toString())
            ->implode("\n");

        return MarkdownDocument::make()
            ->paragraph(AboutMarkdown::render(About::current()))
            ->section('Quick answers', implode("\n\n", $quickAnswers))
            ->section('Skills', $skills)
            ->section('Experience', $experience)
            ->toString();
    }

    public static function projects(): string
    {
        return MarkdownDocument::make()
            ->heading('Projects')
            ->quote('Projects built by '.config('portfolio.owner_name').', with write-ups, tech stacks and links.')
            ->paragraph(self::projectLinks(Project::published()->get()))
            ->toString();
    }

    public static function blog(): string
    {
        return MarkdownDocument::make()
            ->heading('Blog')
            ->quote('Articles by '.config('portfolio.owner_name').'.')
            ->paragraph(self::postLinks(Post::published()->get()))
            ->toString();
    }

    /**
     * @param  iterable<Project>  $projects
     */
    public static function projectLinks(iterable $projects): string
    {
        $links = [];

        foreach ($projects as $project) {
            $links[] = '- '.MarkdownDocument::link($project->title, route('markdown.projects.show', $project), $project->summary);
        }

        return implode("\n", $links);
    }

    /**
     * @param  iterable<Post>  $posts
     */
    public static function postLinks(iterable $posts): string
    {
        $links = [];

        foreach ($posts as $post) {
            $links[] = '- '.MarkdownDocument::link($post->title, route('markdown.blog.show', $post), $post->excerpt);
        }

        return implode("\n", $links);
    }

    private static function publishedProject(mixed $project): Project
    {
        if (! $project instanceof Project) {
            $project = Project::query()->where('slug', $project)->first();
        }

        abort_unless($project?->isPublished(), 404);

        return $project;
    }

    private static function publishedPost(mixed $post): Post
    {
        if (! $post instanceof Post) {
            $post = Post::query()->where('slug', $post)->first();
        }

        abort_unless($post?->isPublished(), 404);

        return $post;
    }
}
