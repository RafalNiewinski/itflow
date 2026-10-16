# Repository Guidelines

## Project Structure & Module Organization

ITFlow is a procedural PHP 8.x application backed by MySQL/MariaDB, without a framework or asset build step. Technician features live in `agent/`; administration in `admin/`; customer pages in `client/`; public key-based flows in `guest/`; and JSON endpoints in `api/v1/`. Shared helpers are loaded from `functions.php` and organized under `functions/`. Root-level `includes/`, `post/`, `modals/`, `js/`, and `css/` are shared across portals. Scheduled tasks live in `cron/`, CLI utilities in `scripts/`, installation code in `setup/`, and vendored dependencies in `libs/` (do not edit them directly).

## Build, Test, and Development Commands

There is no compile or dependency-install step; dependencies are committed under `libs/`.

- `php -S localhost:8000` — quick local server (Apache is preferred for `.htaccess` behavior).
- `find . -path ./libs -prune -o -name '*.php' -exec php -l {} \;` — syntax-check project PHP.
- `mysql -u USER -p DATABASE < db.sql` — validate/import the fresh-install schema.
- `php cron/cron.php` — run the cron dispatcher manually.

For a full development setup, serve the repository with Apache/PHP and `mysqli`, then visit `/setup/` or import `db.sql`.

## Coding Style & Naming Conventions

Follow `.editorconfig`: UTF-8, LF endings, final newline, and four-space indentation. Use procedural PHP and match nearby code; avoid unrelated formatting. Use English for identifiers and comments. Database columns use entity prefixes, such as `ticket_id` and `client_name`. Select only needed columns instead of `SELECT *` (except API read endpoints). POST handlers must not use the reserved `*_model.php` suffix unless they are shared field models.

Sanitize every SQL value with `intval()` or `escapeSql()`, escape displayed values with `escapeHtml()`, validate CSRF for state changes, enforce permissions/client scope, and never introduce `eval` or shell execution.

## Testing Guidelines

No automated application test suite exists. PHP lint and `db.sql` import checks run in CI. Exercise changes on a real install, including the relevant portal, permission boundaries, and bulk equivalents. Schema changes require both `db.sql` and a new sequential `admin/database_updates/<version>.php`; test fresh install and upgrade paths.

## Commit & Pull Request Guidelines

Recent commits use short, imperative summaries such as `Fix calendar event Delete bug`. Keep commits and PRs focused on one fix or feature. PRs should explain what changed and why, link the issue, highlight schema/security effects, and include screenshots for UI changes. Ensure CI is green and read `CONTRIBUTING.md` before submitting; larger features should be discussed in an issue first.
