# Results Module

## Project Overview

The `src/results/` directory handles the presentation layer for translation results. It assembles three types of result tables -- missing articles, in-process translations, and existing translations -- into a unified Bootstrap card-based UI.

### Main Features
- **Missing articles table** -- Shows articles not yet translated with translation buttons
- **In-process table** -- Shows articles currently being translated with user/date info
- **Existing translations table** -- Shows completed translations with Wikipedia links
- **Sorting** -- By English Wikipedia pageviews and medical importance
- **Translation URL generation** -- Links to MediaWiki ContentTranslation tool
- **Bootstrap card wrapping** -- Collapsible card containers for each table

### Technologies
- PHP 8.2+
- Bootstrap 5 (cards, tables, tooltips)
- Font Awesome (icons)
- DataTables (implied by CSS class `display`)

---

## Project Structure

```
src/results/
├── include.php                     # Bootstrap aggregator
├── results.php                     # Main orchestrator (Results\ResultsIndex)
├── helps.php                       # Utility functions (Results\Helps)
├── results_table.php               # Missing articles table (Results\ResultsTable)
├── results_table_exists.php        # Existing translations table (Results\ResultsTableExists)
└── results_table_inprocess.php     # In-process table (Results\ResultsTableInprocess)
```

### Data Flow

```
results_loader($data)               [results.php]
    |
    ├── get_results() / get_results_new()   [backend]
    ├── get_td_or_sql_titles_infos()        [backend]
    ├── get_td_or_sql_translate_type()      [backend]
    ├── get_td_or_sql_qids()                [backend]
    |
    └── Results_tables()
            ├── make_results_table()              [results_table.php]
            ├── make_results_table_inprocess()    [results_table_inprocess.php]
            └── make_results_table_exists()       [results_table_exists.php]
```

### Functions by File

**`results.php`** (Results\ResultsIndex)
| Function | Purpose |
|----------|---------|
| `load_translate_type($ty)` | Loads translation type classification (memoized) |
| `card_result($title, $text)` | Bootstrap card wrapper with collapse |
| `Results_tables(...)` | Orchestrates three table builders |
| `results_loader($data)` | Top-level entry point |

**`helps.php`** (Results\Helps)
| Function | Purpose |
|----------|---------|
| `sort_py_pageviews_rows(...)` | Sort by English Wikipedia pageviews |
| `sort_py_importance(...)` | Sort by medical importance rating |
| `make_translate_urls(...)` | Build translation button URLs |
| `get_item_properties(...)` | Extract article metadata |
| `normalizeItems(array)` | Normalize mixed arrays |

**`results_table.php`** (Results\ResultsTable)
| Function | Purpose |
|----------|---------|
| `make_td_rows_responsive(...)` | Generate single `<tr>` row |
| `make_one_row_results(...)` | Assemble data for one missing article |
| `sort_py_PageViews(...)` | Sort by pageviews (duplicate of helps.php) |
| `make_results_table(...)` | Build complete missing articles table |

---

## Architecture & Code Quality Review

### Code Organization
Clean orchestrator pattern -- `results.php` coordinates data fetching and delegates to three specialized table builders. Each table builder handles one result type.

### Design Patterns
- **Orchestrator/Facade** -- `results_loader()` is the single public entry point
- **Static Memoization** -- `load_translate_type()` caches within request
- **Data-Array Transport** -- Associative arrays passed between functions
- **Heredoc Templating** -- HTML generated via `<<<HTML` blocks

### Maintainability: 5/10
- High function parameter counts (9-13 params per function)
- Duplicate function (`sort_py_PageViews` / `sort_py_pageviews_rows`)
- Dead code in `results_table_exists.php` (variables built then overwritten)
- Mixed concerns (data + presentation in same functions)

### Readability: 6/10
- Heredoc HTML is readable for simple cases
- Cryptic variable names (`$cnt2`, `$tab`, `$frist`)

---

## Strengths

1. **Clean orchestrator pattern** -- Single entry point coordinates all table building
2. **Effective memoization** -- `load_translate_type()` avoids repeated DB calls
3. **Consistent structure** -- All three table builders follow the same pattern
4. **Sorting options** -- Both pageview and importance sorting available

---

## Weaknesses

1. **No HTML escaping** -- All variables interpolated raw into heredoc HTML
2. **High parameter counts** -- Functions have 9-13 parameters
3. **Duplicate function** -- `sort_py_PageViews` in `results_table.php` duplicates `sort_py_pageviews_rows` in `helps.php`
4. **Dead code** -- `$td22` and `$th22` built then immediately overwritten in `results_table_exists.php`
5. **Dead `usort` call** -- Sort result in `results_table.php` line 186 is never used
6. **Mixed concerns** -- Data transformation and HTML rendering in same functions
7. **Typo** -- `$frist` instead of `$first` in two files

---

## Critical Issues

### XSS Vulnerabilities (HIGH)

All three table builders interpolate variables directly into HTML without escaping:

```php
// results_table.php line 41-42
$html .= "<a target='_blank' href='$mdwiki_url'>$title</a>";

// results_table_inprocess.php line 35-68
// $_user_, $_date_, $title all raw

// results_table_exists.php lines 95-132
// $translate_url, $target_tab, $mdwiki_a_tag all raw
```

**Data sources**: Database/API results (article titles, usernames, dates) and user parameters (`$global_username`). If any contain malicious HTML/JS, it will execute.

---

## Areas That Need Attention

- **Add `htmlspecialchars()` to all HTML output** -- Critical security fix
- **Remove duplicate `sort_py_PageViews()`** -- Use `sort_py_pageviews_rows()` from `helps.php`
- **Remove dead code** -- `$td22`/`$th22` overwrite blocks in `results_table_exists.php`
- **Reduce parameter counts** -- Use data arrays or context objects
- **Separate data from presentation** -- Extract data preparation into separate functions

---

## Improvement Plan

### Quick Fixes
1. Add `htmlspecialchars()` wrapping to all variable interpolation in heredoc blocks
2. Remove duplicate `sort_py_PageViews()` function
3. Remove dead `$td22`/`$th22` code blocks
4. Fix `$frist` typo to `$first`

### Medium-Term
1. Create a `ResultRow` data class to replace 12+ parameter functions
2. Separate data preparation from HTML rendering
3. Add unit tests for sorting and property extraction functions

### Long-Term
1. Adopt a template engine for automatic escaping
2. Create a component-based rendering system (table row, cell, button components)
3. Integrate with a proper design system

---

## Comprehensive Review

| Metric | Score | Notes |
|--------|-------|-------|
| **Overall Rating** | 5/10 | Functional but has security and code quality issues |
| **Production Readiness** | Partial | Works but vulnerable to XSS |
| **Security Score** | 3/10 | No output encoding anywhere |
| **Technical Debt** | Medium | Dead code, duplicates, high param counts |
| **Maintainability** | 5/10 | Mixed concerns, no tests |
| **Risk Assessment** | High | XSS exploitable via database data |

---

## Usage

```php
use Results\ResultsIndex\results_loader;

$data = [
    'camp' => 'RTT',
    'code' => 'ar',
    'cat' => 'RTT',
    'depth' => 1,
    'show_exists' => true,
    'global_username' => $GLOBALS['global_username'],
    'endpoint' => 'https://medwiki.toolforge.org',
];

$html = results_loader($data);
echo $html;
```
