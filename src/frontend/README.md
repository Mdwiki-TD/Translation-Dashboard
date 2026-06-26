# Frontend Module

## Project Overview

The `src/frontend/` directory is the HTML generation layer (presentation layer) of the Translation Dashboard. It provides reusable, pure functions that output Bootstrap 5 HTML strings for cards, modals, dropdowns, links, and table structures.

### Main Features
- Bootstrap 5 component generators (alerts, modals, dropdowns, cards, columns)
- URL builders for mdwiki.org, Wikipedia, and Wikidata links
- Results table HTML scaffold generation
- Font Awesome and Bootstrap Icons integration

### Technologies
- PHP 8.2+
- Bootstrap 5 (CSS framework)
- Font Awesome 5 (icons)
- Bootstrap Icons (icons)
- DataTables (table plugin)

---

## Project Structure

```
src/frontend/
├── include.php                     # Aggregator bootstrap
├── html.php                        # Core HTML helper functions (TD\Render\Html)
└── results_rows/
    └── results_table_html.php      # Table scaffold generator (Results\ResultsTableHtml)
```

### File Roles

| File | Namespace | Purpose |
|------|-----------|---------|
| `include.php` | None | Loads all frontend files via `include_once` |
| `html.php` | `TD\Render\Html` | 13 utility functions for HTML generation |
| `results_rows/results_table_html.php` | `Results\ResultsTableHtml` | Table `<table>` + `<thead>` skeleton |

### Functions in `html.php`

| Function | Purpose |
|----------|---------|
| `banner_alert($text)` | Bootstrap alert-danger div |
| `make_modal_fade(...)` | Bootstrap modal dialog |
| `makeDropdown(...)` | `<select>` dropdown with options |
| `makeColSm4(...)` | Bootstrap column with card + table |
| `makeCol(...)` | Narrower Bootstrap column |
| `make_drop(...)` | `<option>` elements for dropdowns |
| `make_mdwiki_href($title)` | Raw mdwiki.org URL |
| `make_mdwiki_article_url_blank(...)` | `<a>` tag to mdwiki article |
| `make_mdwiki_cat_url(...)` | `<a>` tag to mdwiki category |
| `make_mdwiki_user_url($user)` | `<a>` tag to mdwiki user page |
| `make_wikipedia_url_blank(...)` | `<a>` tag to Wikipedia article |
| `make_wikidata_url_blank(...)` | `<a>` tag to Wikidata entity |

---

## Architecture & Code Quality Review

### Code Organization
Clean separation -- all HTML helpers are in one file, table scaffolding in another. The `include.php` aggregator provides a single entry point.

### Design Patterns
- **Pure functions** -- Every function is stateless, side-effect-free (input -> HTML string)
- **Facade via include** -- Single `include.php` loads everything

### SOLID Principles
- Functions are simple and focused (good SRP)
- No inheritance to evaluate

### Maintainability: 7/10
- Small, focused files
- Clear function names
- Stale documentation in comments (8 phantom function references)

### Readability: 7/10
- Heredoc syntax is readable for HTML generation
- Inconsistent variable naming (`$cdcdc`, `$uxutable`)

### Scalability: 8/10
- Pure functions scale well
- No state to manage

---

## Strengths

1. **Pure functional design** -- All functions are stateless and side-effect-free
2. **Bootstrap 5 integration** -- Modern, responsive UI components
3. **Consistent URL builders** -- Separate functions for each external service
4. **Single entry point** -- `include.php` aggregates all frontend code

---

## Weaknesses

1. **No output encoding** -- None of the functions apply `htmlspecialchars()` to parameters
2. **Stale documentation** -- Comments list 8 functions that don't exist
3. **Mixed icon frameworks** -- Both Font Awesome and Bootstrap Icons used inconsistently
4. **Typo in `make_mdwiki_user_url()`** -- `taget='_blank'` instead of `target='_blank'`
5. **Namespace/PSR-4 mismatch** -- `TD\Render\Html` doesn't match autoload path `src/renders/`

---

## Critical Issues

### XSS Vulnerabilities (HIGH)

**Every function in `html.php` is vulnerable to XSS.** Parameters are interpolated directly into HTML without escaping:

```php
// banner_alert() - $text is raw
$html = <<<HTML
<div class="alert alert-danger" role="alert">$text</div>
HTML;

// make_mdwiki_article_url_blank() - $name is not HTML-encoded
$html = "<a target='_blank' href='$url'>$name</a>";
```

**Impact**: If any caller passes user-controlled or database-sourced data containing `<script>` tags, they will execute in the browser.

**Functions affected**: All 13 functions in `html.php` and `make_table_start()` in `results_table_html.php`.

### Namespace/Autoload Mismatch (MEDIUM)

The namespace `TD\Render\Html` doesn't match the PSR-4 autoload path `src/renders/` (which doesn't exist). Functions work only because `include_all.php` loads them via `include_once` chain. If the include chain breaks, all `use function TD\Render\Html\...` statements will fail.

---

## Areas That Need Attention

- **Add `htmlspecialchars()` to all output functions** -- This is the highest priority fix
- **Fix typo** -- `taget` -> `target` in `make_mdwiki_user_url()`
- **Clean up stale comments** -- Remove references to non-existent functions
- **Standardize icon framework** -- Choose either Font Awesome or Bootstrap Icons
- **Align namespaces with PSR-4** -- Either move files or update `composer.json` mappings

---

## Improvement Plan

### Quick Fixes
1. Add `htmlspecialchars($var, ENT_QUOTES, 'UTF-8')` to all parameter interpolation
2. Fix `taget` typo in `make_mdwiki_user_url()`
3. Remove stale function references from comments

### Medium-Term
1. Create an `e()` helper function for consistent HTML escaping
2. Align namespace `TD\Render\` with actual file path or update `composer.json`
3. Standardize on one icon framework

### Long-Term
1. Consider a template engine (Twig/Plates) for automatic escaping
2. Add unit tests for all HTML helper functions
3. Create a design system documentation for consistent component usage

---

## Comprehensive Review

| Metric | Score | Notes |
|--------|-------|-------|
| **Overall Rating** | 5/10 | Good design but critical XSS vulnerabilities |
| **Production Readiness** | Partial | Works but needs output encoding |
| **Security Score** | 3/10 | No output encoding anywhere |
| **Technical Debt** | Low | Small codebase, simple functions |
| **Maintainability** | 7/10 | Clean, focused files |
| **Risk Assessment** | High | XSS exploitable if database data is compromised |

---

## Usage

Functions are available throughout the application via the include chain:

```php
use function TD\Render\Html\make_mdwiki_article_url_blank;
use function TD\Render\Html\makeDropdown;

// Generate a link
echo make_mdwiki_article_url_blank('Article_Title', 'Display Name');

// Generate a dropdown
echo makeDropdown($options, $selected_value, 'my-select');
```
