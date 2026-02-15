# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

WikiProjectMed Translation Dashboard is a PHP web application that facilitates translation of medical articles from mdwiki.org into various Wikipedia languages. It integrates with MediaWiki's Content Translation tool and tracks translation progress via a leaderboard system.

## Commands

### Install dependencies
```bash
composer install
```

### Run tests
```bash
# Run all tests
vendor/bin/phpunit tests --testdox --colors=always -c phpunit.xml

# Or using the shell script
bash run_tests.sh

# Run via Composer
composer test
```

### Run a single test file
```bash
vendor/bin/phpunit tests/CategoryFetcherTest.php --testdox
```

### Run a specific test method
```bash
vendor/bin/phpunit tests/CategoryFetcherTest.php --filter testGetMdwikiCatMembersDepth1UsesCacheAndFilters
```

## Architecture

### Namespace Structure (PSR-4)
The application uses these autoloaded namespaces defined in `composer.json`:
- `TD\` → `src/` (root namespace)
- `TD\Render\` → `src/renders/`
- `Actions\` → `src/actions/` and `src/td_api_wrap/`
- `SQLorAPI\` → `src/api_or_sql/`
- `Tables\` → `src/Tables/`
- `Results\` → `src/results/`
- `TranslateMed\` → `src/translate_med/`

### Key Entry Points
- `src/index.php` - Main interface for selecting languages/categories for translation
- `src/translate_med/index.php` - Handles translation requests and redirects to MediaWiki Content Translation
- `src/leaderboard.php` - Displays translation statistics by user/language/campaign
- `src/missing.php` - Shows articles missing in different languages

### Module Structure
- **`src/backend/`** - Core business logic
  - `api_calls/` - MediaWiki API interactions (wiki_api.php, mdwiki_sql.php)
  - `api_or_sql/` - Data retrieval from database or API fallbacks
  - `tables/` - Database table definitions and SQL queries
  - `results/` - Results processing (category fetching, SPARQL queries)
  - `loaders/` - Request loading utilities
- **`src/frontend/`** - UI components (forms, HTML rendering, results tables)
- **`src/results/`** - Results display logic
- **`src/leaderboard/`** - Leaderboard tables and graphs

### Include System
`src/include_all.php` is the central include file that loads all required PHP files using glob patterns. It's referenced in both the main application and the test bootstrap (`tests/bootstrap.php`).

### Authentication
Authentication is handled via an external `/auth/` directory (see README for setup). User info is loaded via `src/backend/userinfos_wrap.php` which sets `$GLOBALS['global_username']`.

## Database
Schema is in `td.sql` (mentioned in README). Key tables:
- `settings` - Core system settings
- `translate_type` - Translation types
- `categories` - Category and campaign mappings
- `views` - View statistics by language

## Testing
Tests use PHPUnit 11.x with bootstrap at `tests/bootstrap.php`. The test suite:
- Loads `src/include_all.php` for access to application code
- Uses namespaces like `Results\GetCats\CategoryFetcher`

## Deployment
GitHub Actions workflow (`.github/workflows/d.yaml`) triggers on push to main branch and deploys via SSH using `appleboy/ssh-action`.

## Development Notes
- PHP 8.2+ required
- Add `?test=1` or set `test` cookie to enable error reporting for debugging
- The application uses Bootstrap 5 for frontend styling
- Coordinator tools are in a separate repository (tdc)
