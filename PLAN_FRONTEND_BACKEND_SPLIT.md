# Plan: Split Frontend from Backend

## Current State

The codebase mixes backend data-fetching logic and HTML rendering in the same PHP files. Entry-point PHP files like `index.php` call data-fetchers then immediately build HTML with heredoc strings. Most "backend" functions return HTML strings, not data arrays.

### Current Architecture (Simplified)

```
Entry-points (.php at src/ root)
  ├── Direct SQL/API calls
  ├── HTML heredoc templates inline
  └── Calls helper functions that ALSO return HTML

Helper functions (in subdirectories)
  ├── Call data-fetchers (super_function → /api.php or SQL)
  ├── Build HTML with heredoc
  └── Return HTML stringss
```

### What's Already Done

| File                                   | Purpose                                                       | Ready?                                         |
| -------------------------------------- | ------------------------------------------------------------- | ---------------------------------------------- |
| `src/get_results_api.php`              | JSON API for `get_results()` / `get_results_new()`            | Done, but **not used** by frontend             |
| `src/leaderboard/x.php`                | AJAX-driven leaderboard (fetches data via JS from `/api.php`) | Done, ready to replace `leaderboard/index.php` |
| `src/leaderboard/others/graph_api.php` | Chart.js graph via JS fetching `/api.php`                     | Done, used by `x.php`                          |

### External `/api.php` (deployed separately, NOT in this repo)

The external `/api.php` already serves these endpoints:

-   `?get=settings` - Settings data
-   `?get=categories` - Campaign/category mappings
-   `?get=top_users` - Leaderboard user stats
-   `?get=top_langs` - Leaderboard language stats
-   `?get=status` - Graph data (monthly counts)
-   `?get=top_lang_of_users` - Per-user top language

The `x.php` leaderboard already calls these via JavaScript DataTables AJAX.

---

## Target Architecture

```
┌─────────────────────────────────────────────────────────┐
│  FRONTEND (PHP templates + JS)                          │
│  src/frontend/                                          │
│  ├── Renders HTML skeletons (no data-fetching)          │
│  ├── Embeds JS that calls API endpoints                 │
│  └── Uses async DataTables / fetch()                    │
├─────────────────────────────────────────────────────────┤
│  API LAYER (PHP JSON endpoints)                         │
│  src/api/                                               │
│  ├── get_results_api.php  (already exists ✅)           │
│  ├── leaderboard_api.php  (new - wraps /api.php calls)  │
│  ├── missing_api.php      (new - missing data JSON)     │
│  └── lang_api.php         (new - language list JSON)    │
├─────────────────────────────────────────────────────────┤
│  BACKEND (Pure data functions, no HTML)                 │
│  src/backend/   (mostly already clean ✅)               │
│  ├── api_or_sql/    → returns arrays                    │
│  ├── api_calls/     → HTTP/SQL clients                  │
│  ├── results/       → CategoryFetcher, SPARQL           │
│  └── tables/        → langcode mappings                 │
└─────────────────────────────────────────────────────────┘
```

The external `/api.php` remains the data source. The new `src/api/` endpoints in this repo act as thin wrappers that:

1. Call backend functions (which return arrays)
2. Return JSON

---

## Phase 1: Complete `leaderboard.php` Migration (Low Risk)

**Goal:** Replace `leaderboard/index.php` with `leaderboard/x.php`

### Step 1.1: Switch the router

-   File: `src/leaderboard.php` (line 18)
-   Currently: `include_once __DIR__ . '/leaderboard/index.php'` (or it delegates to `Leaderboard\Index\main_leaderboard()`)
-   Change: Point to `x.php` instead
-   Keep old `index.php` as `leaderboard/index_v1.php` for reference

### Step 1.2: Handle sub-pages (`?get=users`, `?get=langs`, `?camps=1`)

-   `x.php` currently doesn't route sub-pages. Add routing in `x.php`:
    -   `?get=users` → `users.php` (already returns HTML, keep as-is for now)
    -   `?get=langs` → `langs.php` (already returns HTML, keep as-is for now)
    -   `?camps=1` → `camps.php` (already returns HTML, keep as-is)
-   These sub-pages are secondary; can be migrated to AJAX later.

### Step 1.3: Remove old unused includes

-   Stop loading `leaderboard/main.php`, `leader_tables.php`, `leader_tables_users.php` from the main path (they're used by old `index.php`)
-   These files will only be needed for sub-page rendering until sub-pages are migrated too.

---

## Phase 2: Migrate `index.php` (The Translation Form + Results) — High Value

This is the main page used by translators. It currently:

1. Renders a form (campaign/language selectors)
2. Calls `results_loader()` which:
    - Calls `get_results()` / `get_results_new()` (backend)
    - Calls `Results_tables()` which builds HTML tables from the data

### Step 2.1: Split the form from results

1. **Create** `src/frontend/translation_form.php`

    - Move the form HTML (campaign dropdown, language picker, type radio, login button) from `index.php` (lines 126-246) into a function
    - This function receives pre-fetched `$categories_tab`, `$langs_table`, `$settings` arrays
    - Returns HTML string, no backend calls

2. **Refactor** `src/index.php`
    - Keep the first ~124 lines (debug, includes, config loading, request parsing)
    - Replace inline form HTML with: `echo render_translation_form($categories_tab, $langs_table, ...)`
    - Replace `results_loader()` call with: load results skeleton + JS that fetches `/src/api/get_results_api.php`

### Step 2.2: Create JS-driven results table

1. **Create** `src/js/results_table.js`

    - On form submit, prevent default
    - Call `GET /src/api/get_results_api.php?cat=...&code=...&depth=...&filter_sparql=...`
    - Parse the JSON response (it already returns `{results: {missing: [...], inprocess: [...], exists: [...]}}`)
    - Render DataTable cards for missing, inprocess, exists tables
    - Handle "Translate" button links using `make_ContentTranslation_url()` from `tr_link.php`

2. **Add API endpoint** `src/api/results_full_api.php` (wrapper)
    - Wraps `get_results()` + extra metadata (titles_infos, qids, translate_type)
    - Returns a complete JSON with article properties needed by the frontend table:
        ```json
        {
          "missing": [{ "title": "...", "qid": "...", "views": "...", "words": "...", "asse": "...", "refs": "...", "mdwiki_url": "...", "cx_url": "..." }],
          "inprocess": [...],
          "exists": [...]
        }
        ```
    - This avoids the frontend needing `titles_infos`, `sql_qids`, `endpoint`, etc.

### Step 2.3: Update `get_results_api.php`

-   Currently returns raw `get_results()` output (array of titles)
-   Enhance to include `titles_infos` metadata so frontend doesn't need separate lookups

---

## Phase 3: Migrate Sub-pages (Users, Langs, Camps) to AJAX

### Step 3.1: User detail page (`?get=users&user=X`)

1. **Create** `src/api/user_api.php`

    - Accepts: `?user=X&year=Y&lang=Z`
    - Calls `get_users_tables()`, `get_td_or_sql_top_lang_of_users()`, etc.
    - Returns JSON with translations list, pending list, graph data

2. **Create** `src/frontend/user_page.php`
    - Renders HTML skeleton (user header, filter form, table containers)
    - JS fetches from `/src/api/user_api.php?user=X`
    - Renders DataTables client-side

### Step 3.2: Language detail page (`?get=langs&langcode=X`)

1. **Create** `src/api/lang_detail_api.php`

    - Accepts: `?langcode=X&year=Y`
    - Calls `get_langs_tables()`, etc.
    - Returns JSON with translations, pending, graph

2. **Create** `src/frontend/lang_page.php`
    - Same pattern as user page

### Step 3.3: Camps page (`?camps=1`)

-   Low traffic, can keep server-rendered or add a simple JSON endpoint

---

## Phase 4: Migrate `missing.php`

### Step 4.1: Create `src/api/missing_api.php`

-   Returns JSON from `open_td_tables_file("missing.json")` + `get_td_or_sql_top_langs()`

### Step 4.2: Create `src/frontend/missing_page.php`

-   HTML skeleton + JS that fetches the API and renders the DataTable

---

## Phase 5: Clean Up & Remove Dead Code

After all pages use JS-driven rendering:

### Files to KEEP (backend-only, no HTML)

| File                                             | Reason                 |
| ------------------------------------------------ | ---------------------- |
| `src/backend/api_or_sql/*.php`                   | Pure data functions ✅ |
| `src/backend/api_calls/*.php`                    | HTTP/SQL clients ✅    |
| `src/backend/results/getcats.php`                | CategoryFetcher ✅     |
| `src/backend/results/new_way/get_results.php`    | Backend logic ✅       |
| `src/backend/results/get_titles/get_results.php` | Backend logic ✅       |
| `src/backend/results/sparql_bots/*.php`          | SPARQL ✅              |
| `src/backend/results/tr_link.php`                | URL builder ✅         |
| `src/backend/results/helps.php`                  | Data helpers ✅        |
| `src/backend/loaders/load_request.php`           | Request parser ✅      |
| `src/backend/tables/langcode.php`                | Lang mappings ✅       |
| `src/backend/settings.php`                       | Config ✅              |
| `src/backend/userinfos_wrap.php`                 | Auth ✅                |

### Files to REWRITE (remove HTML, keep data functions)

| File                                      | Action                                                                                                   |
| ----------------------------------------- | -------------------------------------------------------------------------------------------------------- |
| `src/results/results.php`                 | Extract `results_loader()` logic, move `Results_tables()` HTML to frontend                               |
| `src/results/results_table.php`           | Move HTML to JS/frontend; keep `make_one_row_results()` data assembly                                    |
| `src/results/results_table_inprocess.php` | Same as above                                                                                            |
| `src/results/results_table_exists.php`    | Same as above                                                                                            |
| `src/results/helps.php`                   | Keep `make_translate_urls()`, `get_item_properties()`, `sort_py_PageViews()` — they build data, not HTML |

### Files to ARCHIVE (old server-rendered pages)

| File                                      | Action                                             |
| ----------------------------------------- | -------------------------------------------------- |
| `src/leaderboard/main.php`                | Archive: replaced by `x.php`                       |
| `src/leaderboard/leader_tables.php`       | Archive: tables rendered client-side               |
| `src/leaderboard/leader_tables_users.php` | Archive                                            |
| `src/leaderboard/leader_filter.php`       | Keep (form builder, returns HTML — acceptable)     |
| `src/leaderboard/graph.php`               | Archive: replaced by `graph_api.php` JS approach   |
| `src/leaderboard/users.php`               | Rewrite: keep data function, move HTML to frontend |
| `src/leaderboard/langs.php`               | Rewrite: keep data function, move HTML to frontend |
| `src/leaderboard/subs/*.php`              | Evaluate: keep data logic, archive HTML builders   |

### Files to KEEP as-is (HTML form builders)

| File                                               | Reason                                                                        |
| -------------------------------------------------- | ----------------------------------------------------------------------------- |
| `src/leaderboard/leader_filter.php`                | Generates `<form>` HTML — forms need server rendering for CSRF/select options |
| `src/frontend/html.php`                            | HTML helper functions (makeCard, makeCol, etc.)                               |
| `src/frontend/results_rows/results_table_html.php` | Table header HTML                                                             |
| `src/header.php`, `src/head.php`, `src/footer.php` | Page chrome — always server-rendered                                          |

---

## New Directory Structure After Migration

```
src/
├── index.php                     # Entry: renders form + JS skeleton
├── leaderboard.php               # Entry: delegates to x.php
├── missing.php                   # Entry: renders JS skeleton
├── translate.php                 # Redirect (no change)
├── coordinator.php               # Redirect (no change)
├── tools.php                     # Redirect (no change)
├── translate_med/index.php       # Redirect handler (no change)
├── sitelinks.php                 # Admin tool (keep as-is or migrate later)
│
├── api/                          # NEW: JSON API endpoints
│   ├── get_results_api.php       # Results JSON (enhanced from existing)
│   ├── results_full_api.php      # New: full results with metadata
│   ├── leaderboard_api.php       # New: leaderboard data proxy
│   ├── missing_api.php           # New: missing articles JSON
│   ├── user_api.php              # New: user detail JSON
│   ├── lang_detail_api.php       # New: lang detail JSON
│   └── lang_list_api.php         # New: language list JSON
│
├── frontend/                     # RENAMED from frontend + results
│   ├── include.php
│   ├── html.php                  # HTML helpers
│   ├── translation_form.php      # New: extracted from index.php
│   ├── results_rows/
│   │   └── results_table_html.php
│   └── pages/
│       ├── leaderboard_shell.php # New: x.php skeleton extracted
│       ├── user_page.php         # New
│       ├── lang_page.php         # New
│       └── missing_page.php      # New
│
├── js/
│   ├── g.js
│   ├── graph_api.js
│   ├── c.js (x.php's inline JS extracted)
│   ├── results_table.js          # New: renders results from API JSON
│   └── ...
│
├── backend/                      # No structural changes
│   └── ...                       # Kept as-is (already returns arrays)
│
├── leaderboard/
│   ├── x.php                     # Active leaderboard page
│   ├── index.php                 # Renamed to index_v1.php
│   ├── leader_filter.php         # Kept (form builder)
│   └── ...
│
├── results/
│   └── helps.php                 # Kept (data helpers, not HTML)
│
└── archive/                      # NEW: archived server-rendered code
    ├── leaderboard_main.php
    ├── leaderboard_graph.php
    ├── results_table.php
    ├── results_table_inprocess.php
    ├── results_table_exists.php
    └── results.php
```

---

## Migration Order (Recommended)

| #   | Task                                                      | Files Affected                                           | Risk   | Effort |
| --- | --------------------------------------------------------- | -------------------------------------------------------- | ------ | ------ |
| 1   | Switch `leaderboard.php` to use `x.php`                   | `src/leaderboard.php`                                    | Low    | 15 min |
| 2   | Add sub-page routing to `x.php` (users, langs, camps)     | `src/leaderboard/x.php`                                  | Low    | 30 min |
| 3   | Extract `get_results_api.php` enhancements (add metadata) | `src/get_results_api.php`                                | Low    | 1 hr   |
| 4   | Extract form HTML from `index.php` to frontend function   | `src/index.php`, new `src/frontend/translation_form.php` | Medium | 1 hr   |
| 5   | Create `results_full_api.php` with full metadata          | New `src/api/results_full_api.php`                       | Medium | 2 hr   |
| 6   | Create JS table renderer (`results_table.js`)             | New `src/js/results_table.js`; modify `src/index.php`    | High   | 3 hr   |
| 7   | Switch `index.php` to JS-driven results                   | `src/index.php`                                          | High   | 1 hr   |
| 8   | Create `missing_api.php` + `missing_page.php`             | New files; modify `src/missing.php`                      | Medium | 2 hr   |
| 9   | Create `user_api.php` + `user_page.php`                   | New files; modify `src/leaderboard/users.php`            | Medium | 2 hr   |
| 10  | Create `lang_detail_api.php` + `lang_page.php`            | New files; modify `src/leaderboard/langs.php`            | Medium | 2 hr   |
| 11  | Archive dead code, update `include_all.php`               | `src/include_all.php`, move files to `archive/`          | Low    | 1 hr   |

**Total estimated effort: ~16 hours**

---

## Risks & Considerations

1. **SEO / JS-disabled users**: The current server-rendered pages work without JS. After migration, pages will require JS. Consider `<noscript>` fallback or keep server-side rendering as fallback.

2. **The external `/api.php`**: Since it's deployed separately, any changes to its response format must be coordinated. Phase 1-2 do not require changes to it.

3. **Authentication**: Login/auth is handled server-side in `header.php` and `src/backend/userinfos_wrap.php`. The `get_results_api.php` currently has NO auth check — it should require auth since it would expose translation data. Add `$GLOBALS['global_username']` check.

4. **In-process recording (`insertPage_inprocess`)**: The "Translate" button currently POSTs to `translate_med/index.php` which records the translation intent in SQL. This redirect flow works fine regardless of frontend approach.

5. **Backward compatibility**: Keep old URL patterns working. `index.php?cat=RTT&code=ar&doit=Do+it` should still work — it can either server-render or redirect to the JS version.

6. **The `new_result` toggle**: `index.php` has a `load_new_result` setting (from DB) that switches between `get_results()` and `get_results_new()`. This logic should stay in the API endpoint.
