# Backend Module

## Project Overview

The `src/backend/` directory is the core business logic layer of the WikiProjectMed Translation Dashboard. It handles all data access, authentication, configuration, category fetching, results processing, and translation pipeline orchestration.

### Main Features
- **Database abstraction** via PDO with dual-source strategy (API-first, SQL fallback)
- **OAuth-based authentication** with encrypted cookie sessions
- **Category member fetching** from mdwiki.org with APCu and file-based caching
- **Results pipelines** for determining missing, existing, and in-process translations
- **Wikidata SPARQL integration** for cross-wiki existence checking
- **Translation URL building** for MediaWiki ContentTranslation tool
- **Language code mapping** between codes, names, and autonyms

### Technologies
- PHP 8.2+ (strict types in config classes)
- PDO (MySQL driver)
- cURL (HTTP client for external APIs)
- `defuse/php-encryption` v2.4+ (symmetric encryption for cookies/keys)
- APCu (in-memory caching)
- Wikidata SPARQL endpoint

---

## Project Structure

```
src/backend/
├── settings.php                    # Singleton configuration (OAuth\Settings)
├── userinfos_wrap.php              # Authentication bootstrap
├── api_calls/                      # Infrastructure layer (DB, HTTP)
│   ├── mdwiki_sql.php              # PDO database wrapper
│   └── wiki_api.php                # Wikipedia pageview URL builder
├── api_or_sql/                     # Data orchestration layer
│   ├── index.php                   # Bootstrap + super_function()
│   ├── data_tab.php                # Core data table accessors
│   ├── funcs.php                   # Domain query functions
│   ├── new_sql_tables.php          # Extended queries (missing/exists)
│   ├── process_data.php            # In-process translation data
│   ├── top.php                     # Aggregation/statistics
│   └── get_lead.php                # Leaderboard data (deprecated)
├── include_first/                  # Cross-cutting utilities
│   ├── include.php                 # Bootstrap
│   ├── tables_dir.php              # File-based cache reader
│   ├── test_print.php              # Debug output helper
│   └── text_admin.php              # Admin-only content filter
├── loaders/
│   └── load_request.php            # GET parameter parsing/validation
├── others/
│   └── db_insert.php               # Database insert functions
├── results/                        # Business logic layer
│   ├── getcats.php                 # Category fetcher (API + cache)
│   ├── helps.php                   # Results helper functions
│   ├── tr_link.php                 # Translation link builders
│   ├── include.php                 # Bootstrap
│   ├── get_titles/                 # Original results pipeline
│   ├── new_way/                    # Optimized results pipeline
│   └── sparql_bots/                # SPARQL-based existence checking
├── results_2026/                   # Latest results pipeline
│   ├── index.php                   # Orchestrator
│   ├── get_results_2026.php        # Data pipeline
│   ├── results_table.php           # Missing articles table
│   ├── results_table_exists.php    # Existing translations table
│   └── results_table_inprocess.php # In-process translations table
├── tables/                         # Static data
│   ├── langcode.php                # Language code mappings
│   └── lang_names.json             # Language name data
└── td_api_wrap/
    └── td_api.php                  # Internal API HTTP client
```

### Architecture Layers

| Layer | Purpose | Key Files |
|-------|---------|-----------|
| **Infrastructure** | Database, HTTP, SPARQL | `api_calls/`, `td_api_wrap/` |
| **Data Orchestration** | API-or-SQL abstraction | `api_or_sql/` |
| **Business Logic** | Results pipelines, category fetching | `results/`, `results_2026/` |
| **Configuration** | Settings, auth, utilities | `settings.php`, `userinfos_wrap.php`, `include_first/` |

### Key Architectural Pattern: `super_function()`

The central pattern is `super_function()` in `api_or_sql/index.php`:
1. First tries the Translation Dashboard API (`get_td_api()`)
2. Falls back to direct SQL query (`fetch_query()`) if API returns empty

This enables dual-mode operation: standalone database app or microservice with separate API.

---

## Architecture & Code Quality Review

### Code Organization
The backend follows a layered architecture with clear separation between infrastructure, data orchestration, and business logic. However, three parallel results pipelines (`get_titles/`, `new_way/`, `results_2026/`) indicate iterative development without cleanup.

### Design Patterns
- **Singleton**: `OAuth\Settings\Settings` for configuration
- **Strategy**: `super_function()` chooses API vs SQL at runtime
- **Repository**: `api_or_sql/` functions abstract data sources
- **Static Caching**: Nearly every data function uses `static` variables for in-request memoization
- **Template Method**: `CategoryFetcher` class with DI for testability

### SOLID Principles
- **SRP**: Mostly adhered to, though `results_2026/` files mix business logic with HTML generation
- **OCP**: The `super_function()` pattern allows extending data sources
- **LSP**: Not applicable (minimal inheritance)
- **ISP**: Not applicable (procedural functions, not interfaces)
- **DIP**: Partially applied -- `CategoryFetcher` uses DI, but most code relies on global state

### Maintainability: 5/10
- Three parallel results pipelines create confusion
- Duplicate namespace declarations (`Results\GetResults` in two files)
- Glob-based includes make dependency graph implicit
- Mixed OOP and procedural code without consistent philosophy

### Readability: 6/10
- Function names are generally descriptive
- Namespace organization mirrors directory structure
- Variable naming is inconsistent (`$dd`, `$tabb`, `$yhu`)

### Scalability: 6/10
- Static caching reduces redundant API/SQL calls
- No database connection pooling (new PDO per query)
- APCu caching for category data is effective

---

## Strengths

1. **Consistent SQL injection prevention** -- All database queries use PDO prepared statements with parameterized queries
2. **Dual-source data strategy** -- `super_function()` provides graceful API-to-SQL fallback
3. **Effective caching** -- APCu + file-based caching for category data, static variables for in-request memoization
4. **Singleton configuration** -- Clean `Settings` class with environment-aware behavior
5. **Path traversal protection** -- `getcats.php` sanitizes category names for file paths
6. **Encrypted authentication** -- Cookies use `defuse/php-encryption` with httponly/secure/samesite flags

---

## Weaknesses

1. **Three parallel results pipelines** -- `get_titles/`, `new_way/`, `results_2026/` do similar work with variations
2. **Duplicate namespace declarations** -- `Results\GetResults` declared in two different files
3. **No connection pooling** -- New PDO instance created per query call
4. **Glob-based includes** -- Makes dependency graph implicit and hard to trace
5. **HTML generation in backend** -- `results_2026/` files generate raw HTML, mixing concerns
6. **Mixed paradigms** -- OOP (`CategoryFetcher`) coexists with purely procedural code
7. **Dead/deprecated code** -- `get_leaderboard_table()` marked deprecated, old implementations kept alongside optimized ones
8. **Large static arrays** -- `$L_code_to_wikiname` (380 lines) duplicates data in `lang_names.json` and database

---

## Critical Issues

### SQL Error Disclosure (HIGH)
**File**: `api_calls/mdwiki_sql.php`, lines 121, 146
```php
echo "sql error:" . $e->getMessage() . "<br>" . $sql_query;
```
Full SQL errors including query text are echoed to the browser. This reveals table structures, column names, and query logic.

### Pervasive XSS in HTML Rendering (HIGH)
**Files**: All `results_2026/*.php`, `results/helps.php`, `api_calls/wiki_api.php`

Article titles, targets, usernames, and URLs are directly interpolated into HTML templates without `htmlspecialchars()`:
```php
// results_2026/results_table.php
$html .= "<a target='_blank' href='$url'>$title</a>";
```

### Debug Mode via Cookie/GET (MEDIUM)
**Files**: `include_first/test_print.php`, `api_calls/mdwiki_sql.php`

Any user can enable verbose debug output by setting a `test` cookie or `?test=1` parameter:
```php
if ($_COOKIE['test'] ?? $_REQUEST['test'] ?? '') {
    ini_set('display_errors', 1);
    error_reporting(E_ALL);
}
```

### Hardcoded Admin Username (LOW)
**File**: `include_first/text_admin.php`
```php
if ($global_username === 'Mr. Ibrahem') { return $text; }
```

---

## Areas That Need Attention

- **Missing tests** -- No unit tests exist for backend modules (only integration-level tests in `tests/`)
- **Error handling** -- SQL errors should be logged, not echoed to browser
- **Debug mode** -- Should be restricted to admin users or disabled in production
- **Connection pooling** -- PDO instances are created and destroyed per query
- **Dead code cleanup** -- Remove deprecated `get_leaderboard_table()`, old pipeline implementations
- **HTML escaping** -- All HTML output should use `htmlspecialchars()` or a template engine
- **Documentation** -- No inline PHPDoc for most functions

---

## Improvement Plan

### Quick Fixes
1. Replace `echo "sql error:"` with `error_log()` in `mdwiki_sql.php`
2. Add `htmlspecialchars()` to all HTML interpolation in `results_2026/` files
3. Remove `?test=1` debug mode from production or restrict to admin users
4. Fix duplicate `Results\GetResults` namespace declarations

### Medium-Term
1. Consolidate three results pipelines into one (keep `results_2026/` as canonical)
2. Implement PDO connection pooling or singleton pattern
3. Move HTML generation out of backend into frontend templates
4. Add PHPDoc annotations to all public functions
5. Replace glob-based includes with explicit `require_once` statements

### Long-Term
1. Introduce a proper dependency injection container
2. Create interfaces for data access layer to enable mocking/testing
3. Implement a template engine (Twig or Plates) for HTML rendering
4. Add comprehensive unit test coverage for all backend modules
5. Migrate from procedural functions to service classes

### Security Hardening
1. Implement CSP headers
2. Restrict debug mode to authenticated admin users only
3. Add rate limiting on authentication endpoints
4. Sanitize all HTML output with `htmlspecialchars()`

---

## Comprehensive Review

| Metric | Score | Notes |
|--------|-------|-------|
| **Overall Rating** | 5/10 | Functional but has significant security and maintainability issues |
| **Production Readiness** | Partial | Works in production but with known XSS and error disclosure risks |
| **Security Score** | 4/10 | SQL injection protected, but XSS pervasive and error disclosure active |
| **Technical Debt** | High | Three parallel pipelines, dead code, mixed paradigms |
| **Maintainability** | 5/10 | Glob includes, duplicate namespaces, no tests |
| **Risk Assessment** | Medium | XSS could be exploited if database data is compromised |

---

## Setup & Usage

### Dependencies
```bash
composer install
```

### Environment Variables (via `load_env.php` or system env)
```
APP_ENV=development|production
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

### Database
Schema is defined in `td.sql` at the project root. Key tables:
- `settings` -- Core system settings
- `categories` -- Category and campaign mappings
- `pages` -- Translation page records
- `in_process` -- Active translation records
- `langs` -- Language definitions
- `qids` -- Wikidata QID mappings

### Testing
```bash
vendor/bin/phpunit tests --testdox
```
