# Source Root (`src/`)

## Project Overview

The `src/` directory is the root of the WikiProjectMed Translation Dashboard application. It contains the main entry points, page controllers, layout templates, and the central bootstrap file that loads all subsystems.

### Main Features
- **Translation selection interface** -- Language/category picker for initiating translations
- **Leaderboard** -- Translation statistics by user, language, and campaign
- **Missing articles** -- Shows articles not yet translated per language
- **Sitelinks viewer** -- Wikidata sitelink statistics
- **Authentication** -- OAuth-based login via external `/auth/` system
- **Coordinator tools** -- Admin tools via external `/tdc/` system

### Technologies
- PHP 8.2+
- Bootstrap 5 (CSS framework)
- jQuery 3.7.0 + jQuery UI 1.13.2
- DataTables 2.2.2
- Chart.js (graph rendering)
- Font Awesome 5 + Bootstrap Icons
- `defuse/php-encryption` (cookie encryption)

---

## Project Structure

```
src/
├── include_all.php                 # Central bootstrap (autoloaded via composer.json)
├── load_env.php                    # Development environment config
├── index.php                       # Main translation selection page
├── header.php                      # Site-wide navigation bar
├── head.php                        # HTML <head> with CSS/JS includes
├── footer.php                      # Site-wide footer + DataTables init
├── leaderboard.php                 # Leaderboard entry point
├── missing.php                     # Missing articles page (live API)
├── missing_old.php                 # Missing articles page (JSON cache)
├── sitelinks.php                   # Wikidata sitelinks viewer
├── translate.php                   # Redirect to translate_med/
├── auth.php                        # Redirect to /auth/
├── coordinator.php                 # Redirect to /tdc/
├── tools.php                       # Redirect to /tdc/ (duplicate)
├── t.php                           # Debug: APCu cache info
├── 404.php                         # Custom 404 error page
│
├── backend/                        # Business logic, data access, config
│   └── [README.md](backend/README.md)
├── frontend/                       # HTML generation helpers
│   └── [README.md](frontend/README.md)
├── results/                        # Results table presentation
│   └── [README.md](results/README.md)
├── leaderboard/                    # Leaderboard system
│   └── [README.md](leaderboard/README.md)
├── translate/                      # Legacy redirect shim
│   └── [README.md](translate/README.md)
├── translate_med/                  # Translation initiation
│   └── [README.md](translate_med/README.md)
├── oauth/                          # Empty placeholder
│   └── [README.md](oauth/README.md)
├── css/                            # Stylesheets
│   └── [README.md](css/README.md)
└── js/                             # JavaScript files
    └── [README.md](js/README.md)
```

---

## Architecture & Code Quality Review

### Application Architecture

The application follows a **procedural page-controller pattern** with layered modules:

```
[Browser Request]
    |
    v
[Entry Point]       index.php, leaderboard.php, missing.php, etc.
    |
    ├── include_all.php          # Bootstrap: loads ALL modules
    ├── header.php               # Auth + navigation + HTML head
    |   ├── userinfos_wrap.php   # Cookie-based OAuth auth
    |   └── head.php             # CSS/JS includes
    |
    ├── [Page-specific logic]    # Delegates to subdirectory modules
    |
    └── footer.php               # DataTables init + cookie consent
```

### Bootstrap Chain

`include_all.php` is the central bootstrap, autoloaded via `composer.json`:

1. Loads `load_env.php` (development only)
2. Requires Composer autoloader (`vendor/autoload.php`)
3. Includes `frontend/include.php` (HTML helpers)
4. Includes `backend/settings.php` (config singleton)
5. Includes `backend/include_first/include.php` (utilities)
6. **Glob-includes** all PHP from `backend/api_calls/`, `backend/td_api_wrap/`, `backend/api_or_sql/`, `backend/others/`
7. Includes `backend/tables/langcode.php`
8. Includes `leaderboard/include_leaderboards.php`
9. Includes `results/include.php`

### Design Patterns
- **Page Controller** -- Each `.php` file handles one page/route
- **Include-based composition** -- Pages include shared templates (header/footer)
- **Glob-based autoloading** -- Backend modules loaded via `glob()`
- **Global state** -- `$GLOBALS['global_username']`, `$GLOBALS['user_is_coordinator']`

### Maintainability: 4/10
- Glob-based includes make dependency graph implicit
- Heavy use of global state
- Mixed concerns in page files (logic + presentation)
- No routing framework

### Readability: 5/10
- Page files are self-contained but monolithic
- Inconsistent variable naming
- Arabic comments in some files

---

## Strengths

1. **Simple architecture** -- Easy to understand page-per-file structure
2. **Bootstrap 5** -- Modern, responsive UI framework
3. **Comprehensive CDN fallback** -- `get_host()` checks CDN availability
4. **Encrypted authentication** -- OAuth with `defuse/php-encryption`
5. **Security headers** -- `httponly`, `secure`, `samesite=Lax` on cookies
6. **Noindex meta tag** -- Prevents search engine indexing

---

## Weaknesses

1. **Glob-based includes** -- Dependency graph is implicit
2. **Global state** -- `$GLOBALS` used for auth state
3. **Mixed concerns** -- Page files contain logic + HTML
4. **No routing** -- Direct file access, no clean URLs
5. **Duplicate files** -- `coordinator.php` and `tools.php` identical
6. **Hardcoded banner** -- "tool is down" message in `header.php`

---

## Critical Issues

### Hardcoded Credentials (HIGH)
**File**: `load_env.php`
```php
putenv('TOOL_TOOLSDB_PASSWORD=root11');
putenv('COOKIE_KEY=hex_value_here');
putenv('DECRYPT_KEY=hex_value_here');
```
Development credentials committed to repository. Should use `.env` files excluded from version control.

### XSS in Header (HIGH)
**File**: `header.php`, line 54
```php
echo "<a ...>$GLOBALS[global_username]</a>";
```
Username interpolated directly into HTML without `htmlspecialchars()`.

### XSS in Error Messages (HIGH)
**File**: `index.php`, line 186
```php
$error_html .= "<div ...>$err</div>";
```
Error messages echoed without escaping.

### Unprotected Debug Page (MEDIUM)
**File**: `t.php`
```php
echo json_encode(apcu_cache_info(), JSON_PRETTY_PRINT);
```
Exposes internal cache information with no access control.

### Debug Mode via Cookie (MEDIUM)
**File**: `header.php`, multiple others
```php
if ($_COOKIE['test'] ?? $_REQUEST['test'] ?? '') {
    ini_set('display_errors', 1);
}
```
Any user can enable verbose error display.

---

## Areas That Need Attention

- **Remove hardcoded credentials** -- Use `.env` files or environment variables
- **Add `htmlspecialchars()` to all output** -- Especially `header.php` and error messages
- **Restrict debug mode** -- Admin-only or remove from production
- **Add access control to `t.php`** -- Or remove the file
- **Remove duplicate files** -- Consolidate `coordinator.php`/`tools.php`
- **Replace glob includes** -- Use explicit `require_once` statements
- **Add routing** -- Consider a simple router for clean URLs

---

## Improvement Plan

### Quick Fixes
1. Move `load_env.php` credentials to `.env` file (add `.env` to `.gitignore`)
2. Add `htmlspecialchars()` to username output in `header.php`
3. Add `htmlspecialchars()` to error messages in `index.php`
4. Add access check to `t.php` or delete it

### Medium-Term
1. Replace glob includes with explicit requires
2. Implement a simple router for clean URLs
3. Extract HTML from page files into template files
4. Add CSRF tokens to forms

### Long-Term
1. Adopt a micro-framework (Slim, Lumen) for routing and middleware
2. Implement proper MVC separation
3. Add comprehensive test coverage
4. Set up CI/CD pipeline with static analysis

---

## Comprehensive Review

| Metric | Score | Notes |
|--------|-------|-------|
| **Overall Rating** | 5/10 | Functional but has significant security issues |
| **Production Readiness** | Partial | Works but needs security hardening |
| **Security Score** | 4/10 | XSS, hardcoded creds, debug mode |
| **Technical Debt** | High | Glob includes, global state, no routing |
| **Maintainability** | 4/10 | Monolithic files, implicit dependencies |
| **Risk Assessment** | High | XSS and credential exposure |

---

## Setup & Usage

### Installation
```bash
cd src/
composer install
```

### Environment Configuration
Create `.env` file (not tracked in git):
```
APP_ENV=development
DB_HOST_TOOLS=tools.db.svc.eqiad.wmflabs
DB_NAME=mdwiki_td
TOOL_TOOLSDB_USER=username
TOOL_TOOLSDB_PASSWORD=password
CONSUMER_KEY=oauth_key
CONSUMER_SECRET=oauth_secret
COOKIE_KEY=encryption_key
DECRYPT_KEY=decryption_key
JWT_KEY=jwt_secret
TABLES_PATH=/path/to/tables
```

### Local Development
1. Set `APP_ENV=development` in environment
2. Configure database credentials
3. Set up OAuth credentials (see README at project root)
4. Access via web server: `http://localhost/Translation_Dashboard/src/`

### Testing
```bash
# Add ?test=1 to any URL for debug output
# Or set test cookie

# Run PHPUnit tests
vendor/bin/phpunit tests --testdox
```

### Deployment
GitHub Actions workflow (`.github/workflows/d.yaml`) deploys on push to main via SSH.

### Key Entry Points
| URL | Page |
|-----|------|
| `/Translation_Dashboard/src/index.php` | Translation selection |
| `/Translation_Dashboard/src/leaderboard.php` | Leaderboard |
| `/Translation_Dashboard/src/missing.php` | Missing articles |
| `/Translation_Dashboard/src/sitelinks.php` | Wikidata sitelinks |
| `/Translation_Dashboard/src/translate_med/index.php` | Start translation |
