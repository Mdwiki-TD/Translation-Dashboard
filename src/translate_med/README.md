# Translate Med Module

## Project Overview

The `src/translate_med/` directory handles translation initiation. When a user clicks "translate" on an article, this module authenticates the user, records the translation attempt in the database, and redirects to the MediaWiki ContentTranslation tool.

### Main Features
- **Authentication check** -- Requires logged-in user, shows login button if not
- **Translation tracking** -- Records in-process translations in database
- **ContentTranslation redirect** -- Builds and redirects to MediaWiki CX tool
- **Multiple redirect mechanisms** -- JavaScript, meta refresh, and noscript fallback

### Technologies
- PHP 8.2+
- PDO (database inserts)
- `defuse/php-encryption` (via auth system)
- Bootstrap 5 (UI components)

---

## Project Structure

```
src/translate_med/
├── index.php                       # Main translation initiation logic
└── medwiki.php                     # Redirect shim (legacy)
```

### Data Flow

```
User clicks "Translate"
    |
    v
translate_med/index.php
    |
    ├── Authenticate user (userinfos_wrap.php)
    ├── Read GET parameters (title, code, cat, camp, type, word)
    ├── INSERT into in_process table (db_insert.php)
    ├── Build ContentTranslation URL (tr_link.php)
    └── Redirect to ContentTranslation
```

### Functions

**`index.php`** (global namespace)
| Function | Purpose |
|----------|---------|
| `go_to_translate_url(...)` | Build CT URL, output HTML link + JS/meta redirect |

**`medwiki.php`** -- Redirect shim to `index.php` (5 lines)

---

## Architecture & Code Quality Review

### Code Organization
Single-file controller handling the entire request lifecycle: input parsing, auth check, database insert, URL construction, and redirect output.

### Design Pattern
Procedural page controller. No classes.

### Maintainability: 5/10
- Mixed concerns (auth, DB, URL building, HTML, JS in one file)
- Global namespace function (`go_to_translate_url`)
- `rawurldecode()` undermines `FILTER_SANITIZE_FULL_SPECIAL_CHARS`

### Readability: 6/10
- Logic flow is clear
- Triple redundant redirect mechanisms (JS + meta + noscript)

---

## Strengths

1. **Authentication required** -- Redirects to login if not authenticated
2. **Duplicate prevention** -- `INSERT ... WHERE NOT EXISTS` pattern
3. **Fallback redirects** -- JS + meta refresh + noscript for compatibility
4. **Parameterized queries** -- PDO prepared statements for database inserts

---

## Weaknesses

1. **Mixed concerns** -- Auth, DB, URL building, HTML, JS in one file
2. **Global namespace** -- `go_to_translate_url()` could collide
3. **`rawurldecode()` after sanitization** -- Undoes `FILTER_SANITIZE_FULL_SPECIAL_CHARS`
4. **Missing `exit` after redirect** -- In both redirect files
5. **No CSRF protection** -- State-changing operation on GET request
6. **Duplicate redirect files** -- `src/translate/medwiki.php` and `medwiki.php` do the same thing

---

## Critical Issues

### XSS in URL Context (MEDIUM)

The redirect URL is interpolated into JavaScript and HTML without proper escaping:

```php
// Line 44 - JavaScript context
echo "<script>window.open('$url', '_self')</script>";

// Line 37 - HTML attribute context (single quotes)
echo "<a target=\"_blank\" href='$url'>Click here</a>";
```

While `http_build_query()` URL-encodes values, the URL as a whole is not HTML-entity-encoded. A crafted value could break out of the attribute.

### `rawurldecode()` Undermines Sanitization (MEDIUM)

```php
$title_o = rawurldecode(filter_input(INPUT_GET, "title", FILTER_SANITIZE_FULL_SPECIAL_CHARS));
```

`FILTER_SANITIZE_FULL_SPECIAL_CHARS` HTML-entity-encodes special characters. `rawurldecode()` then decodes URL-encoded characters, potentially re-introducing dangerous content.

### Debug Mode via Cookie (MEDIUM)

The `test` cookie/parameter enables `display_errors = 1` globally, potentially exposing SQL queries and internal paths.

---

## Areas That Need Attention

- **Add `htmlspecialchars()` to URL output** -- For HTML attribute context
- **Remove `rawurldecode()` calls** -- Or apply decoding before sanitization
- **Add `exit` after redirects** -- In all three redirect files
- **Restrict debug mode** -- Admin-only or remove
- **Consolidate redirect files** -- Remove `src/translate/medwiki.php` and `medwiki.php` if unused

---

## Improvement Plan

### Quick Fixes
1. Add `exit;` after all `header("Location: ...")` calls
2. HTML-encode the URL before inserting into `href` and `window.open()`
3. Fix sanitization order: decode first, then sanitize

### Medium-Term
1. Extract database insert logic into a service function
2. Move HTML output to a template file
3. Add CSRF token validation for the state-changing operation

### Long-Term
1. Implement proper MVC separation
2. Add unit tests for URL building and database insertion
3. Use a router instead of direct file access

---

## Comprehensive Review

| Metric | Score | Notes |
|--------|-------|-------|
| **Overall Rating** | 5/10 | Functional but has security concerns |
| **Production Readiness** | Partial | Works but needs XSS fixes |
| **Security Score** | 5/10 | Auth required, but XSS and debug mode issues |
| **Technical Debt** | Medium | Mixed concerns, duplicate files |
| **Maintainability** | 5/10 | Single monolithic file |
| **Risk Assessment** | Medium | XSS via crafted URL parameters |

---

## Setup & Usage

### URL Parameters
| Parameter | Type | Description |
|-----------|------|-------------|
| `title` | string | Article title to translate |
| `code` | string | Target language code (e.g., `ar`) |
| `cat` | string | Category name |
| `camp` | string | Campaign name |
| `type` | string | Translation type (`lead` or `full`) |
| `word` | int | Word count |

### Example
```
/translate_med/index.php?title=Diabetes&code=ar&cat=RTT&camp=RTT&type=lead&word=5000
```

### Authentication
Requires a valid OAuth session. If not authenticated, redirects to `/auth/index.php`.
