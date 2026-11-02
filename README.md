# My corner of the internet: a portfolio you can talk to. Ask my AI anything about my work.

An open-source developer portfolio with a built-in AI assistant. Visitors can browse projects and posts the usual way, or simply ask: *"What has he built with Laravel?"*, *"Is she open to work?"*, *"Show me the latest blog post."* The assistant answers from the portfolio's own content and streams its replies in real time.

**Live site:** [jaocero.dev](https://jaocero.dev)

Built with Laravel, Livewire, Filament and the Laravel AI SDK. Fork it, edit your `.env`, fill in your content at `/admin`, and you have your own.

---

## Features

- **Chat with the portfolio.** A streaming AI assistant on the home page (and a floating chat on every other page) that answers questions using tools that read your projects, posts, skills, experience and profile. It never invents facts, stays on topic, and links to the right pages.
- **Lead capture.** When a visitor wants to get in touch, the assistant can save their message straight to your inbox.
- **Everything editable in an admin panel.** Projects, blog posts, skills, experience, your profile, contact messages and chat logs live in a [Filament](https://filamentphp.com) panel at `/admin`.
- **Built-in limits.** Per-visitor rate limits (per minute and per day), a message length cap and a maximum conversation length keep costs predictable. API keys never reach the browser.
- **SEO, GEO and AI discoverability out of the box.** JSON-LD structured data, a sitemap, an Atom feed, `robots.txt`, `llms.txt`, `llms-full.txt` and a markdown version of every page. See [SEO and AI discoverability](#seo-geo-and-ai-discoverability).
- **A design with personality.** A warm "paper" theme with hand-drawn doodles, scroll reveals and a dark mode, all respecting `prefers-reduced-motion`.
- **Fork-friendly.** No personal information is hardcoded. Your identity comes from `.env`, everything else from the admin panel.

## Tech stack

| | |
| --- | --- |
| Framework | Laravel 13, PHP 8.4 |
| Frontend | Livewire 4 (with `wire:stream`), Alpine.js, Tailwind CSS 4, Vite |
| Admin panel | Filament 5 |
| AI | [Laravel AI SDK](https://laravel.com/docs/ai) (`laravel/ai`). Anthropic by default, any supported provider works |
| Testing | Pest, with AI SDK fakes (no real API calls) |
| Quality | Pint, PHPStan / Larastan |

## Quick start

Requirements: PHP 8.4, Composer, Node 22+, and SQLite (or MySQL / PostgreSQL).

```bash
git clone https://github.com/199ocero/jaocero.git my-portfolio
cd my-portfolio

composer setup            # installs dependencies, creates .env, generates a key, migrates, builds assets
php artisan storage:link  # serves uploaded images and your resume
php artisan db:seed       # optional: demo content + an admin@example.com / "password" login (not in production)

composer dev              # starts the local dev processes, including Vite
```

Then:

1. Add your AI provider key to `.env` (`ANTHROPIC_API_KEY=...`). Without it the site works, but the chat shows a friendly error.
2. Open `/admin` and sign in. Only emails listed in `PORTFOLIO_ADMIN_EMAILS` can access the panel.
3. Fill in your profile under **About**, then add projects, posts, skills and experience.

## Making it yours

### 1. Your identity (`.env`)

Every variable is documented in [`.env.example`](.env.example). The ones you will want to change first:

| Variable | What it does |
| --- | --- |
| `APP_URL` | Your public URL. Canonical URLs, the sitemap, `llms.txt` and structured data are built from it. |
| `PORTFOLIO_OWNER_NAME` | Your name, used across the site, the AI's instructions and structured data. |
| `PORTFOLIO_HEADLINE` | The line after "Hi, I'm {name} —". Wrap a phrase in `*asterisks*` to give it a hand-drawn marker highlight, e.g. `a Laravel developer who builds with *AI*`. |
| `PORTFOLIO_TAGLINE` | The home page intro and its meta description. |
| `PORTFOLIO_DEFAULT_THEME` | `light` or `dark` for first-time visitors. Visitors can switch with the navbar toggle. |
| `PORTFOLIO_ADMIN_EMAILS` | Comma-separated emails allowed into `/admin`. |
| `PORTFOLIO_SOURCE_URL` | Optional link to your fork, shown in the footer. |

To create an admin account on a server, run `php artisan make:filament-user` with an email that is in `PORTFOLIO_ADMIN_EMAILS`.

### 2. Your content (`/admin`)

| Section | Contents |
| --- | --- |
| **About** | Headline, bio, location, availability, avatar, resume, share image, email and social links |
| **Projects** | Write-ups in markdown, tech stack, links, thumbnail, featured flag, display order, SEO overrides, publish date |
| **Posts** | Markdown posts with tags, cover image, SEO overrides and a publish date (schedule posts by picking a future date) |
| **Skills** | Grouped by category, drag to reorder |
| **Experience** | Roles, companies and dates (leave the end date empty for your current role) |
| **Messages** | Contact form submissions and leads saved by the AI assistant |
| **Chat logs** | Read-only transcripts of visitor conversations |

### 3. Your look

- **Colors:** the semantic tokens at the top of [`resources/css/app.css`](resources/css/app.css) (`--canvas`, `--fg`, `--accent`...) define both the light and dark themes.
- **Fonts:** Inter and Caveat are loaded in [`vite.config.js`](vite.config.js).
- **Brand assets:** replace `public/favicon.svg`, `public/favicon.ico`, `public/apple-touch-icon.png` and `public/og-image.png` (1200×630, the fallback social image).

## The AI assistant

The chat is a Livewire component ([`app/Livewire/Chat.php`](app/Livewire/Chat.php)) that streams answers from `PortfolioAgent` ([`app/Ai/Agents/PortfolioAgent.php`](app/Ai/Agents/PortfolioAgent.php)). The agent has tools to read your profile, projects, project details, skills, experience, blog posts and contact details, plus one tool to save a lead. It only sees published content.

**Choosing a provider and model**

```dotenv
AI_PROVIDER=anthropic
ANTHROPIC_API_KEY=sk-...
PORTFOLIO_CHAT_MODEL=claude-haiku-5-5
```

Any text provider supported by the Laravel AI SDK works (OpenAI, Gemini, Groq, Mistral, Ollama, OpenRouter, any OpenAI-compatible endpoint...). Set `AI_PROVIDER`, that provider's API key and a matching `PORTFOLIO_CHAT_MODEL`. Provider settings live in [`config/ai.php`](config/ai.php).

**Limits** (all configurable in `.env`)

| Variable | Default |
| --- | --- |
| `PORTFOLIO_CHAT_RATE_LIMIT_PER_MINUTE` | 10 messages per visitor |
| `PORTFOLIO_CHAT_RATE_LIMIT_PER_DAY` | 50 messages per visitor |
| `PORTFOLIO_CHAT_MAX_MESSAGE_LENGTH` | 500 characters |
| `PORTFOLIO_CHAT_MAX_TURNS` | 20 questions per conversation |
| `PORTFOLIO_CHAT_TIMEOUT` | 30 seconds |

**Privacy.** Conversations are stored so you can read them under **Chat logs**, and visitors are told so under the chat box. Visitor IPs are never stored in plain text: they are hashed with your app key.

## SEO, GEO and AI discoverability

Everything below is generated from your content. There is nothing to configure beyond `APP_URL`.

**Search engines**

- Title, description, canonical URL, Open Graph and Twitter tags on every page. Projects and posts can override their SEO title and description in the admin panel.
- **JSON-LD structured data:** every page describes the site (`WebSite`) and you (`Person`, with your skills, current employer, location and social profiles). Pages add `ProfilePage`, `BlogPosting`, `SoftwareSourceCode` / `CreativeWork`, `CollectionPage` and `BreadcrumbList` as appropriate.
- `/sitemap.xml` and an Atom feed at `/feed.xml`.

**AI search and assistants (GEO)**

- **Quick answers:** the About page answers the questions people actually ask ("What does {name} do?", "Is {name} available for hire?"...) in short, direct passages that AI search engines can quote. They are built from your admin content.
- **`/llms.txt`:** an index of your site for LLMs, following the [llms.txt](https://llmstxt.org) format. **`/llms-full.txt`** contains all your content in a single markdown file.
- **Markdown versions of every page:** `/index.md`, `/about.md`, `/projects.md`, `/projects/{slug}.md`, `/blog.md` and `/blog/{slug}.md`. Each one declares the HTML page as canonical, so they don't count as duplicate content.
- **Content negotiation:** clients that prefer markdown (such as coding agents) get it from the normal URL. Every page also advertises its markdown version and `llms.txt` in `Link` headers. Try it:

  ```bash
  curl -H "Accept: text/markdown" https://your-site.test/about
  ```

**`robots.txt`**

AI *search* bots (`OAI-SearchBot`, `ChatGPT-User`, `Claude-SearchBot`, `Claude-User`, `PerplexityBot`...) are always allowed, because that is how your site gets cited and linked in AI answers.

AI *training* bots (`GPTBot`, `ClaudeBot`, `Google-Extended`, `CCBot`...) are allowed by default, since a portfolio usually wants maximum visibility. Set `PORTFOLIO_ALLOW_AI_TRAINING=false` to block them. Both bot lists are in [`config/portfolio.php`](config/portfolio.php), so you can update them as vendors add new crawlers.

`robots.txt` is generated by a route, so don't add a static `public/robots.txt` (it would take precedence).

**Caching.** `llms.txt`, the sitemap, the feed and your structured data are cached for up to an hour, and the cache is cleared whenever you save content in the admin panel.

## Testing

```bash
composer test   # Pint (check), PHPStan and the Pest suite
```

The suite never calls a real AI provider: it uses the AI SDK's agent fakes, and `phpunit.xml` sets a dummy API key so a missing fake can't spend credits. GitHub Actions runs the same checks on every push and pull request.

## Deployment

Any standard Laravel host works ([Laravel Cloud](https://cloud.laravel.com), Forge, a VPS...). A few things to know:

- Run `php artisan storage:link` so uploads are served.
- Set `APP_ENV=production`, `APP_DEBUG=false` and the correct `APP_URL`.
- **Streaming:** the chat uses Livewire's `wire:stream`, which streams the response body. It is **not compatible with Laravel Octane**, and anything in front of PHP must not buffer responses. Livewire already sends `X-Accel-Buffering: no`, which nginx respects; check your CDN or load balancer settings if answers arrive all at once instead of word by word.
- The database seeder only creates the demo admin outside production. Use `php artisan make:filament-user` on your server.

## Project structure

```
app/
├── Ai/Agents/PortfolioAgent.php   # The assistant: instructions, history, tools
├── Ai/Tools/                      # What the assistant can read (and SaveLead)
├── Filament/                      # Admin panel resources and the About page
├── Http/Controllers/              # robots.txt, sitemap, feed, llms.txt, markdown pages
├── Livewire/Chat.php              # The streaming chat
├── Livewire/Pages/                # Public pages
└── Support/Seo/                   # Structured data, markdown presenters, llms.txt, caching
config/portfolio.php               # Everything you can configure from .env
resources/views/                   # Blade views, doodles and illustrations
```

## Contributing

Contributions are welcome. Please read [CONTRIBUTING.md](CONTRIBUTING.md) first.

## Security

Please don't open public issues for security problems. See [SECURITY.md](SECURITY.md).

## License

Open-sourced under the [MIT license](LICENSE). The code is yours to fork. The content on [jaocero.dev](https://jaocero.dev) is not part of this repository.
