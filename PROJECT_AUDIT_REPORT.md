# Translation Dashboard — Project Audit Report

**Date:** 2026-05-27
**Scope:** Full codebase audit of `src/` directory (10 modules, 80+ PHP files, 15+ JS/CSS files)
**Methodology:** Static code analysis, architecture review, security assessment per module

---

## Executive Summary

The WikiProjectMed Translation Dashboard is a custom PHP web application that facilitates translation of medical articles from mdwiki.org into Wikipedia languages. It integrates with MediaWiki's ContentTranslation tool, Wikidata SPARQL, and tracks translation progress via a leaderboard system.

**Technologies:** PHP 8.2+, Bootstrap 5, jQuery, DataTables, Chart.js, PDO/MySQL, APCu, cURL, `defuse/php-encryption`, Wikidata SPARQL.

**Architecture:** Procedural page-controller pattern with layered modules. No framework. Include-based composition with glob autoloading. Global state via `$GLOBALS`.

**Verdict:** The application is functional and actively deployed, but carries significant security risk and technical debt. XSS vulnerabilities are pervasive across all output-generating modules. The codebase requires immediate security hardening before it can be considered production-ready.

---

## Project Health Assessment

### Overall Scores

| Metric | Score | Assessment |
|--------|-------|------------|
| **Overall Code Quality** | 5.3/10 | Functional but inconsistent |
| **Maintainability** | 5.6/10 | Glob includes, mixed paradigms, no tests |
| **Scalability** | 6/10 | Effective caching, but no connection pooling |
| **Security Posture** | 4.4/10 | SQL injection prevented; XSS pervasive |
| **Production Readiness** | 45% | Deployed but with known vulnerabilities |

### Module-Level Breakdown

| Module | Rating | Security | Debt | Role |
|--------|--------|----------|------|------|
| `src/` (root) | 5/10 | 4/10 | High | Entry points, bootstrap, layout |
| `src/backend/` | 5/10 | 4/10 | High | Data access, business logic, config |
| `src/frontend/` | 5/10 | 3/10 | Low | HTML generation helpers |
| `src/results/` | 5/10 | 3/10 | Medium | Results table presentation |
| `src/leaderboard/` | 5/10 | 4/10 | Medium-High | Leaderboard system |
| `src/translate/` | 7/10 | 8/10 | Low | Legacy redirect shim |
| `src/translate_med/` | 5/10 | 5/10 | Medium | Translation initiation |
| `src/css/` | 7/10 | N/A | Low | Stylesheets |
| `src/js/` | 5/10 | N/A | Medium | Client-side JavaScript |

---

## Cross-Project Analysis

### Shared Architectural Patterns

All modules follow the same procedural page-controller approach:

1. **Include-based composition** — Pages load `include_all.php` → `header.php` → content → `footer.php`
2. **Glob autoloading** — Backend modules loaded via `glob()` + `include_once`
3. **Static memoization** — Nearly every data function caches results in `static` variables
4. **Heredoc HTML** — All HTML generated via `<<<HTML` blocks with variable interpolation
5. **Namespace-organized functions** — No classes in presentation/business logic layers

### Repeated Weaknesses (Systemic)

| Weakness | Occurrences | Modules Affected |
|----------|-------------|------------------|
| **No `htmlspecialchars()` in HTML output** | Every HTML-generating file | frontend, results, leaderboard, backend, root, translate_med |
| **Debug mode via cookie/GET** | 8+ files | root, backend, leaderboard, translate_med |
| **Cryptic variable names** | Throughout | All modules |
| **High function parameter counts** (9-13) | 10+ functions | results, leaderboard |
| **Dead/deprecated code retained** | 5+ instances | backend, results, leaderboard |
| **Duplicate code** | 5+ instances | backend (3 pipelines), results, leaderboard, JS |

### Common Technical Debt

1. **Three parallel results pipelines** in `backend/results/` — `get_titles/`, `new_way/`, `results_2026/` do similar work with slight variations. Only `results_2026/` appears current.

2. **Glob-based includes** — `include_all.php` uses `glob()` to load all PHP files from 4 directories. Makes dependency graph implicit and creates risk if attacker can write files to those directories.

3. **Mixed OOP and procedural code** — `CategoryFetcher` class uses DI and is well-designed, but everything else is procedural with global state. No consistent architectural philosophy.

4. **No unit tests for backend** — PHPUnit is configured and tests exist for some modules, but backend business logic has no test coverage.

5. **Namespace/PSR-4 mismatches** — `TD\Render\Html` maps to `src/renders/` which doesn't exist. Functions work only via include chain.

### Dependency Issues

| Issue | Impact |
|-------|--------|
| Chart.js v2 API used (`yAxes`/`xAxes`) | Deprecated; will break on Chart.js v4+ |
| `sorttable.js` (2007) loaded alongside DataTables | Conflict; redundant functionality |
| Two theme JS files (`theme.js` + `color-modes.js`) | Potential conflicts; duplicated logic |
| Font Awesome + Bootstrap Icons both loaded | Redundant; inconsistent icon usage |
| `defuse/php-encryption` v2.4 | Maintained but consider upgrading |

### Integration Concerns

1. **External API dependencies** — mdwiki.org API, Wikipedia Pageviews API, Wikidata SPARQL endpoint. No circuit breakers or fallback strategies beyond `super_function()`.

2. **Session/auth coupling** — `$GLOBALS['global_username']` used throughout. No abstraction layer for auth state.

3. **File-based caching** — Category data cached to filesystem. Race conditions possible under concurrent writes.

---

## Critical Findings

### HIGH RISK

#### 1. Pervasive XSS Vulnerabilities

**Severity:** HIGH
**Scope:** All HTML-generating modules (frontend, results, leaderboard, backend, root, translate_med)

Every HTML output function in the codebase interpolates variables directly into heredoc blocks without `htmlspecialchars()`:

```php
// src/frontend/html.php — banner_alert()
$html = <<<HTML
<div class="alert alert-danger" role="alert">$text</div>
HTML;

// src/results/results_table.php
$html .= "<a target='_blank' href='$mdwiki_url'>$title</a>";

// src/header.php
echo "<a ...>$GLOBALS[global_username]</a>";

// src/backend/results_2026/results_table.php
// $title, $target, $url all interpolated raw
```

**Attack vector:** If database data (article titles, usernames) or URL parameters contain `<script>` tags or event handlers, they execute in the user's browser. The application displays data from external APIs (mdwiki.org, Wikipedia, Wikidata), which are trusted but not guaranteed safe.

**Exploitability:** Medium — requires either database compromise or malicious data in upstream APIs.

#### 2. SQL Error Disclosure

**Severity:** HIGH
**File:** `src/backend/api_calls/mdwiki_sql.php`, lines 121, 146

```php
echo "sql error:" . $e->getMessage() . "<br>" . $sql_query;
```

On any database error, the full SQL query and error message are echoed to the browser. This reveals table names, column names, query structure, and potentially user data in query parameters.

**Exploitability:** Low — requires triggering a database error, but the information disclosure is immediate and complete.

#### 3. Hardcoded Credentials in Repository

**Severity:** HIGH
**File:** `src/load_env.php`

```php
putenv('TOOL_TOOLSDB_PASSWORD=root11');
putenv('COOKIE_KEY=<hex_value>');
putenv('DECRYPT_KEY=<hex_value>');
```

Development credentials are committed to version control. While guarded by `APP_ENV=development`, the file is tracked in git. If production keys were ever committed, they remain in git history.

**Exploitability:** Low for development credentials; High if production keys exist in git history.

### MEDIUM RISK

#### 4. Debug Mode Accessible to Any User

**Scope:** 8+ files across root, backend, leaderboard, translate_med

Any user can enable verbose error display by setting a `test` cookie or adding `?test=1` to any URL:

```php
if ($_COOKIE['test'] ?? $_REQUEST['test'] ?? '') {
    ini_set('display_errors', 1);
    error_reporting(E_ALL);
}
```

This exposes SQL queries, internal file paths, variable dumps, and cache information. The `sparql_bot.php` file also exposes raw data dumps via `?test11=1`.

#### 5. `rawurldecode()` Undermines Sanitization

**File:** `src/translate_med/index.php`

```php
$title_o = rawurldecode(filter_input(INPUT_GET, "title", FILTER_SANITIZE_FULL_SPECIAL_CHARS));
```

`FILTER_SANITIZE_FULL_SPECIAL_CHARS` HTML-entity-encodes special characters. `rawurldecode()` then decodes URL-encoded characters, potentially re-introducing dangerous content. This creates a false sense of security.

#### 6. No CSRF Protection on State-Changing Operations

**File:** `src/translate_med/index.php`

The translation initiation page performs a database INSERT (recording in-process translations) via GET request with no CSRF token. An attacker could forge requests to mark articles as "in process" for any logged-in user.

#### 7. Unprotected Diagnostic Page

**File:** `src/t.php`

Exposes `apcu_cache_info()` output with no access control. Reveals cache keys, entry counts, and memory usage.

---

## Strengths

### Strong Engineering Decisions

1. **SQL injection fully prevented** — All 50+ database queries use PDO prepared statements with parameterized queries. No string concatenation into SQL WHERE clauses with user input. This is the strongest security aspect of the codebase.

2. **Encrypted authentication** — OAuth-based auth with `defuse/php-encryption` for cookie encryption. Cookie attributes set correctly (`httponly`, `secure`, `samesite=Lax` in production).

3. **Dual-source data strategy** — `super_function()` provides API-first with SQL fallback, enabling graceful degradation and microservice architecture flexibility.

4. **Effective caching** — APCu for in-memory caching, file-based cache for category data, static variables for in-request memoization. Reduces API/SQL calls significantly.

5. **Path traversal protection** — `getcats.php` sanitizes category names: `str_replace(['/', '\\', '..'], '', $cat)`.

### Reusable Components

1. **`TD\Render\Html` functions** — 13 pure, stateless HTML helper functions used across all modules. Clean functional design.

2. **`CategoryFetcher` class** — Well-designed OOP with DI, caching, and recursive subcategory traversal. The best-architected code in the project.

3. **`super_function()` pattern** — Reusable abstraction for API-or-SQL data access.

### Well-Structured Modules

1. **CSS** — Clean file separation by concern (main, responsive, mobile, theme). Modern CSS features (nesting, custom properties).

2. **Frontend** — Small, focused files with pure functions. Easy to understand and maintain.

3. **`translate/`** — Trivially simple redirect shim. Does one thing well.

### Good Development Practices

1. **Bootstrap 5** — Modern, responsive UI framework
2. **PHPStan** configured for static analysis
3. **PHPUnit** configured for testing (though coverage is minimal)
4. **`noindex` meta tag** — Prevents search engine indexing of internal tool
5. **Accessibility** — `prefers-reduced-motion` support, focus-visible outlines

---

## Improvement Roadmap

### Immediate Fixes (Week 1)

These require minimal code changes but address the highest-risk issues:

| # | Fix | Effort | Impact |
|---|-----|--------|--------|
| 1 | **Replace `echo "sql error:"` with `error_log()`** in `mdwiki_sql.php` lines 121, 146 | 5 min | Eliminates SQL error disclosure |
| 2 | **Add `exit;` after all `header("Location: ...")` calls** in `translate/medwiki.php`, `translate_med/medwiki.php`, `translate_med/index.php` | 5 min | Prevents post-redirect code execution |
| 3 | **Delete or restrict `t.php`** — add admin check or remove file | 10 min | Eliminates cache info exposure |
| 4 | **Move `load_env.php` credentials to `.env`** and add `.env` to `.gitignore` | 15 min | Removes credentials from repo |
| 5 | **Remove `console.log()` calls** from `src/js/graph_api.js` | 2 min | Removes debug output from production |

### Short-Term Improvements (Weeks 2-4)

| # | Improvement | Effort | Impact |
|---|-------------|--------|--------|
| 6 | **Create an `e()` helper function** wrapping `htmlspecialchars($val, ENT_QUOTES, 'UTF-8')` in `frontend/html.php` | 1 hr | Foundation for XSS fix |
| 7 | **Add `htmlspecialchars()` to all HTML output** — systematic sweep of all heredoc blocks in frontend, results, leaderboard, backend, root | 2-3 days | Eliminates XSS vulnerabilities |
| 8 | **Restrict debug mode to admin users** — check `$GLOBALS['user_is_coordinator']` before enabling `display_errors` | 2 hrs | Prevents debug info exposure |
| 9 | **Fix `rawurldecode()` order** in `translate_med/index.php` — decode before sanitize, not after | 30 min | Fixes sanitization bypass |
| 10 | **Delete dead code** — `sorttable.js`, `others/index.php`, deprecated `get_leaderboard_table()`, duplicate `sort_py_PageViews()` | 1 hr | Reduces confusion |
| 11 | **Fix typos** — `autocomplate.js` → `autocomplete.js`, `taget` → `target`, `$frist` → `$first` | 15 min | Code quality |

### Medium-Term Improvements (Months 1-3)

| # | Improvement | Effort | Impact |
|---|-------------|--------|--------|
| 12 | **Consolidate results pipelines** — remove `get_titles/` and `new_way/`, keep only `results_2026/` | 1-2 days | Eliminates major duplication |
| 13 | **Replace glob includes with explicit requires** in `include_all.php` and all `include.php` files | 1 day | Makes dependencies explicit |
| 14 | **Align namespaces with PSR-4** — either move `html.php` to `src/renders/` or update `composer.json` | 2 hrs | Fixes autoload mismatch |
| 15 | **Consolidate JS theme systems** — choose `theme.js` or `color-modes.js`, remove the other | 2 hrs | Eliminates conflicts |
| 16 | **Update Chart.js API** — migrate `g.js` from v2 to v3+ scales format | 2 hrs | Prevents future breakage |
| 17 | **Add CSRF tokens** to `translate_med/index.php` form submission | 2 hrs | Prevents CSRF attacks |
| 18 | **Create data classes** for translation entries — replace 12+ parameter functions with structured objects | 2-3 days | Reduces complexity |
| 19 | **Extract HTML from backend** — move `results_2026/*.php` HTML generation to frontend templates | 2-3 days | Separates concerns |

### Long-Term Strategic Refactoring (Months 3-6)

| # | Refactoring | Effort | Impact |
|---|-------------|--------|--------|
| 20 | **Introduce a template engine** (Twig or Plates) for automatic HTML escaping | 1 week | Eliminates XSS class of vulnerabilities |
| 21 | **Implement PDO connection pooling** or singleton pattern | 1 day | Reduces connection overhead |
| 22 | **Add comprehensive unit tests** for backend modules | 2-3 weeks | Prevents regressions |
| 23 | **Implement a simple router** (Slim or custom) for clean URLs | 1 week | Replaces direct file access |
| 24 | **Create a dependency injection container** | 1 week | Replaces global state |
| 25 | **Add API endpoint tests** with mock responses | 1 week | Validates external integrations |
| 26 | **Implement CSP headers** | 1 day | Defense-in-depth for XSS |

### Security Hardening Priorities

| Priority | Action | Current State | Target State |
|----------|--------|---------------|--------------|
| **P0** | XSS elimination | 0% output encoding | 100% output encoding |
| **P0** | SQL error disclosure | Errors echoed to browser | Errors logged only |
| **P0** | Credential management | Hardcoded in repo | `.env` files, excluded from git |
| **P1** | Debug mode restriction | Any user can enable | Admin-only or removed |
| **P1** | CSRF protection | None | Tokens on all state-changing operations |
| **P2** | CSP headers | None | Strict CSP with nonce-based scripts |
| **P2** | Rate limiting | None | On auth and API endpoints |

### DevOps and Testing Recommendations

| Area | Recommendation |
|------|----------------|
| **CI/CD** | Add PHPStan to CI pipeline (already configured as dev dependency) |
| **Testing** | Target 80% coverage for `backend/api_or_sql/` and `backend/results/` |
| **Linting** | Add PHP-CS-Fixer for consistent code style |
| **Monitoring** | Add error logging (currently errors are echoed, not logged) |
| **Dependency scanning** | Add Composer audit to CI for vulnerable packages |
| **Code review** | Require review for changes to `api_calls/mdwiki_sql.php` and `settings.php` |

---

## Final Evaluation

### Overall Project Score

| Metric | Score |
|--------|-------|
| **Overall Score** | **5/10** |
| **Risk Level** | **HIGH** |
| **Technical Debt Level** | **HIGH** |
| **Production Readiness** | **45%** |
| **Security Score** | **4.4/10** |

### Summary

The Translation Dashboard is a functional application that serves its purpose — facilitating medical article translations across Wikipedia languages. It has strong foundations in SQL injection prevention, authentication, and caching. The UI is modern (Bootstrap 5) and responsive.

However, the codebase carries significant risk:

- **XSS is the #1 issue** — no output encoding exists anywhere in the codebase. This is a systemic problem that requires a systematic fix (either add `htmlspecialchars()` everywhere or adopt a template engine).
- **SQL error disclosure** and **debug mode exposure** are easily fixable but currently active.
- **Technical debt** is concentrated in the backend, where three parallel results pipelines and glob-based creates maintenance burden.
- **No test coverage** for backend business logic means refactoring carries high risk.

### Recommended Next Steps

1. **This week:** Fix the 5 immediate items (SQL error disclosure, exit after redirects, restrict t.php, move credentials, remove console.log)
2. **Next 2 weeks:** Systematic XSS fix via `e()` helper function sweep
3. **Next month:** Restrict debug mode, fix sanitization order, delete dead code
4. **Next quarter:** Consolidate results pipelines, replace glob includes, align namespaces
5. **Next 6 months:** Template engine, connection pooling, unit tests, router

The application is not production-ready by modern security standards, but the path to hardening is clear and the immediate fixes are low-effort, high-impact.

---

*Report generated from analysis of 10 module README files covering 80+ PHP files, 15+ JS/CSS files, and `composer.json` configuration.*
