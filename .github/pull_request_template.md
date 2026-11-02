## What does this change?

<!-- A short description of the change. Link the related issue, e.g. "Closes #12". -->

## Why?

<!-- The problem it solves or the improvement it brings. -->

## How was it tested?

<!-- The tests you added or updated, and anything you checked by hand. -->

## Screenshots

<!-- For visual changes: light and dark mode, desktop and phone widths. Delete this section otherwise. -->

## Checklist

- [ ] `composer test` passes (Pint, PHPStan and Pest)
- [ ] Tests cover the changed behavior, and AI features use the SDK fakes (no real API calls)
- [ ] No personal information is hardcoded; new settings are in `config/portfolio.php` and documented in `.env.example`
- [ ] No new dependencies, or they were agreed on in an issue first
- [ ] Animations respect `prefers-reduced-motion`
