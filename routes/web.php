<?php

use App\Http\Controllers\FeedController;
use App\Http\Controllers\LlmsTxtController;
use App\Http\Controllers\MarkdownPageController;
use App\Http\Controllers\RobotsTxtController;
use App\Http\Controllers\SitemapController;
use App\Http\Middleware\ServeMarkdownToAgents;
use App\Livewire\Pages;
use Illuminate\Support\Facades\Route;

/*
 * SEO and AI discoverability.
 */
Route::get('/robots.txt', RobotsTxtController::class)->name('robots');
Route::get('/sitemap.xml', SitemapController::class)->name('sitemap');
Route::get('/feed.xml', FeedController::class)->name('feed');
Route::get('/llms.txt', [LlmsTxtController::class, 'index'])->name('llms.index');
Route::get('/llms-full.txt', [LlmsTxtController::class, 'full'])->name('llms.full');

/*
 * Markdown versions of the public pages, for AI agents. Registered before the HTML
 * routes so "/projects/{slug}.md" isn't captured by "/projects/{project}".
 */
Route::name('markdown.')->group(function (): void {
    Route::get('/index.md', MarkdownPageController::class)->name('home');
    Route::get('/about.md', MarkdownPageController::class)->name('about');
    Route::get('/projects.md', MarkdownPageController::class)->name('projects.index');
    Route::get('/projects/{project}.md', MarkdownPageController::class)->name('projects.show');
    Route::get('/blog.md', MarkdownPageController::class)->name('blog.index');
    Route::get('/blog/{post}.md', MarkdownPageController::class)->name('blog.show');
});

Route::middleware(ServeMarkdownToAgents::class)->group(function (): void {
    Route::livewire('/', Pages\Home::class)->name('home');

    Route::livewire('/projects', Pages\Projects\Index::class)->name('projects.index');
    Route::livewire('/projects/{project}', Pages\Projects\Show::class)->name('projects.show');

    Route::livewire('/blog', Pages\Blog\Index::class)->name('blog.index');
    Route::livewire('/blog/{post}', Pages\Blog\Show::class)->name('blog.show');

    Route::livewire('/about', Pages\About::class)->name('about');
    Route::livewire('/contact', Pages\Contact::class)->name('contact');
});
