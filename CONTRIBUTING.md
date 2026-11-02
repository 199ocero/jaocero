# Contributing

Thanks for wanting to improve this project! It is a personal portfolio that is also meant to be a solid starting point for anyone who forks it, so fixes and improvements that help every fork are especially welcome.

## Before you start

- **Bugs:** open an issue using the bug report template, with steps to reproduce.
- **Features:** open a feature request first so we can agree on the approach before you write code. Changes that only make sense for one person's portfolio are better kept in your fork.
- **Security issues:** don't open an issue. Follow [SECURITY.md](SECURITY.md).

## Local setup

```bash
composer setup
php artisan storage:link
php artisan db:seed   # demo content and an admin@example.com / "password" login
composer dev
```

You don't need an AI provider key to work on most of the site. The test suite never calls a real provider.

## Making changes

- **Keep it fork-friendly.** Never hardcode personal information (names, links, bios). Identity belongs in `config/portfolio.php` (backed by `.env`), content belongs in the database.
- **Follow the existing conventions.** Look at sibling files before adding new ones: class-based Livewire components in `app/Livewire`, semantic Tailwind color tokens (`bg-canvas`, `text-muted`, `text-accent`...) rather than raw palette colors, and the `php artisan make:*` commands for new classes.
- **New configuration** goes in `config/portfolio.php` with a documented entry in `.env.example`.
- **No new dependencies** without discussing them in an issue first.
- **Respect reduced motion and accessibility:** animations must be disabled under `prefers-reduced-motion`, and interactive elements need visible focus states and labels.

## Tests and code quality

Add or update Pest tests for any behavior you change. AI features must be tested with the AI SDK fakes (`PortfolioAgent::fake()`), never with real API calls.

Before opening a pull request, run:

```bash
composer lint   # format with Pint
composer test   # Pint check, PHPStan and the full test suite
```

CI runs the same checks and must pass.

## Pull requests

- Keep each pull request focused on one change.
- Fill in the pull request template: what changed, why, and how you tested it.
- Include screenshots for visual changes, in both light and dark mode, at desktop and phone widths.

By contributing, you agree that your contributions are licensed under the [MIT license](LICENSE).
