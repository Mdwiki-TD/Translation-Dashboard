# Leaderboard Module

## Project Overview

The `src/leaderboard/` directory implements the translation leaderboard system. It displays translation statistics by user, language, and campaign, with interactive charts, sortable tables, and filtering capabilities.

### Main Features
- **Main leaderboard** -- Summary stats, top users, top languages, translation graph
- **User detail view** -- Per-user translation history with charts
- **Language detail view** -- Per-language translation history with charts
- **Campaign view** -- Campaign grouping and statistics
- **Chart.js graphs** -- Time-series visualization of translation activity
- **DataTables integration** -- Sortable, searchable tables
- **Filter system** -- Filter by campaign, year, month, user, language
- **MassMessage copy modal** -- Copy target syntax for Wikimedia MassMessage

### Technologies
- PHP 8.2+
- Bootstrap 5 (cards, forms, grid, modals)
- Chart.js (bar/line charts)
- jQuery DataTables (sortable tables)
- Font Awesome (icons)

---

## Project Structure

```
src/leaderboard/
├── index.php                       # Router / entry point
├── main.php                        # Main leaderboard composition
├── x.php                           # AJAX-driven alternative leaderboard
├── camps.php                       # Campaign-to-article mapping
├── langs.php                       # Language detail view
├── users.php                       # User detail view
├── graph.php                       # Chart.js rendering (server data)
├── lang_user_graph.php             # Graph data builder
├── leader_filter.php               # Main filter form
├── leader_tables.php               # Summary stats + language table
├── leader_tables_users.php         # Users table + copy modal
├── include_leaderboards.php        # Central loader
├── subs/
│   ├── filter_form.php             # Sub-page filter form
│   ├── langs_sub.php               # Language data retrieval
│   ├── lead_help.php               # Core table row builder
│   └── users_sub.php               # User data retrieval
└── others/
    ├── index.php                   # Empty placeholder
    ├── camps_text.php              # Campaign display tables
    └── graph_api.php               # API-driven graph
```

### Architecture Layers

```
[Router]          index.php
[View]            langs.php, users.php, main.php
[Composition]     subs/filter_form.php, subs/lead_help.php
[Rendering]       leader_tables.php, leader_tables_users.php, graph.php
[Data]            camps.php, subs/langs_sub.php, subs/users_sub.php
```

### Namespace Map

| Namespace | File | Role |
|-----------|------|------|
| `Leaderboard\Index` | `main.php` | Main composition |
| `Leaderboard\Filter` | `leader_filter.php` | Filter form |
| `Leaderboard\LeaderTables` | `leader_tables.php` | Summary + language table |
| `Leaderboard\LeaderTabUsers` | `leader_tables_users.php` | Users table + modal |
| `Leaderboard\Graph` | `graph.php` | Server-data charts |
| `Leaderboard\Graph2` | `others/graph_api.php` | API-data charts |
| `Leaderboard\SubGraph` | `lang_user_graph.php` | Time-series builder |
| `Leaderboard\Camps` | `camps.php` | Article-campaign mapping |
| `Leaderboard\CampText` | `others/camps_text.php` | Campaign display |
| `Leaderboard\Langs` | `langs.php` | Language view |
| `Leaderboard\Users` | `users.php` | User view |
| `Leaderboard\Subs\LeadHelp` | `subs/lead_help.php` | Row/table builder |
| `Leaderboard\Subs\SubLangs` | `subs/langs_sub.php` | Language data |
| `Leaderboard\Subs\SubUsers` | `subs/users_sub.php` | User data |
| `Leaderboard\Subs\FilterForm` | `subs/filter_form.php` | Sub-page filters |

---

## Architecture & Code Quality Review

### Code Organization
Clean namespace organization mirrors directory structure. The router (`index.php`) dispatches to view handlers which compose sub-components.

### Design Patterns
- **Procedural with namespaces** -- No classes, all functions
- **Function-based composition** -- Views assembled from function return values
- **Memoization** -- `camps.php` uses static variable caching
- **Dual rendering** -- Server-side (`main.php`) vs AJAX (`x.php`)

### Maintainability: 5/10
- Code duplication between `langs_sub.php` and `users_sub.php`
- `make_td_fo_user()` has 9 parameters
- Glob-based auto-inclusion of `subs/` and `others/`
- Empty file (`others/index.php`)

### Readability: 6/10
- Descriptive function names in most places
- Cryptic variables in some files (`$dd`, `$tabb`, `$Taab`)
- Arabic comments in some files

---

## Strengths

1. **Clean namespace organization** -- Mirrors directory structure
2. **Consistent input sanitization** -- `filter_input()` with `FILTER_SANITIZE_FULL_SPECIAL_CHARS` at entry points
3. **User data separation** -- `$user_to_curl` (raw) vs `$user_to_html` (sanitized)
4. **Effective memoization** -- Static caching for expensive operations
5. **Dual rendering strategies** -- Server-side and AJAX options

---

## Weaknesses

1. **No HTML escaping** -- Variables interpolated directly into heredoc HTML
2. **High code duplication** -- `langs_sub.php` and `users_sub.php` nearly identical
3. **Too many parameters** -- `make_td_fo_user()` has 9 params
4. **Glob-based includes** -- Any `.php` file in `subs/`/`others/` auto-loaded
5. **Debug mode in production** -- `test` cookie/parameter enables error display
6. **Inconsistent return patterns** -- `camps_text.php` uses `echo` instead of `return`
7. **Empty placeholder file** -- `others/index.php` contains only `<?PHP`

---

## Critical Issues

### XSS Vulnerabilities (HIGH)

Variables interpolated into HTML without escaping throughout:

```php
// subs/lead_help.php line 121
"<a href='leaderboard.php?get=users&user=$use'>$user</a>"

// subs/filter_form.php line 27
"<option value='$dd' $se>$dd</option>"

// graph.php - keys/values in JavaScript
graph_js([$keys], [$values], "$graph_id")
```

### Debug Mode Exposure (MEDIUM)

Multiple files enable debug output via `test` cookie/parameter:
- `main.php` (lines 5-9)
- `x.php` (lines 3-7)
- `leader_filter.php` (lines 11-15)

---

## Areas That Need Attention

- **Add output encoding** -- `htmlspecialchars()` on all HTML interpolation
- **Refactor duplicate code** -- Extract shared logic from `langs_sub.php`/`users_sub.php`
- **Reduce parameter counts** -- Use data arrays or context objects
- **Remove empty files** -- Delete `others/index.php`
- **Restrict debug mode** -- Admin-only or remove from production
- **Fix inconsistent returns** -- `camps_list()` should return, not echo

---

## Improvement Plan

### Quick Fixes
1. Add `htmlspecialchars()` to all HTML output
2. Remove empty `others/index.php`
3. Fix `camps_list()` to return instead of echo

### Medium-Term
1. Extract shared logic from `langs_sub.php`/`users_sub.php` into a common function
2. Replace glob includes with explicit requires
3. Restrict debug mode to admin users

### Long-Term
1. Introduce data classes for translation entries (reduce parameter counts)
2. Add unit tests for data retrieval and rendering functions
3. Consolidate graph implementations (server-side vs API)

---

## Comprehensive Review

| Metric | Score | Notes |
|--------|-------|-------|
| **Overall Rating** | 5/10 | Functional but has security and duplication issues |
| **Production Readiness** | Partial | Works but vulnerable to XSS |
| **Security Score** | 4/10 | Input sanitized but output not encoded |
| **Technical Debt** | Medium-High | Duplication, dead code, debug mode |
| **Maintainability** | 5/10 | Glob includes, high param counts |
| **Risk Assessment** | High | XSS via database data |

---

## Usage

The leaderboard is accessed via `leaderboard.php` which includes the module:

```php
// src/leaderboard.php
include_once 'include_all.php';
include_once 'header.php';
include_once 'leaderboard/main.php';
include_once 'leaderboard/index.php';
include_once 'footer.php';
```

### URL Parameters
- `?get=users&user=Username` -- User detail view
- `?get=langs&langcode=ar` -- Language detail view
- `?get=camps` -- Campaign view
- `?year=2024&camp=RTT` -- Filter by year/campaign
