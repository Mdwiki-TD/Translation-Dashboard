# JavaScript Module

## Project Overview

The `src/js/` directory contains the application's client-side JavaScript. It handles theme switching, chart rendering, table sorting, autocomplete, page view counting, and UI interactions.

### Main Features
- **Theme switching** -- Light/dark/system mode with localStorage persistence
- **Chart.js graphs** -- Line/bar chart rendering for translation statistics
- **DataTables integration** -- Sortable, searchable tables
- **Autocomplete** -- User and language input autocomplete via jQuery UI
- **Page view counters** -- Fetches and displays Wikipedia pageview data
- **Card collapse/expand** -- Animated card widget toggling
- **Table sorting** -- Legacy sorttable.js library (conflicts with DataTables)

### Technologies
- Vanilla JavaScript (ES5 + ES modules)
- jQuery 3.7.0
- jQuery UI 1.13.2 (autocomplete)
- Chart.js (graph rendering)
- DataTables 2.2.2 (table plugin)
- Bootstrap 5 (JS components)

---

## Project Structure

```
src/js/
├── theme.js                        # Theme switching (custom)
├── color-modes.js                  # Theme switching (Bootstrap)
├── g.js                            # Chart.js graph functions
├── graph_api.js                    # API-driven graph data fetch
├── leadtable.js                    # DataTables leaderboard init
├── to.js                           # Page view counter
├── c.js                            # Card collapse/expand
├── sorttable.js                    # Legacy table sorting (2007)
├── autocomplate.js                 # Autocomplete (typo in filename)
├── codes.js                        # Language code selector
└── main.js                         # Bootstrap popover init (unused)
```

### File Details

| File | Size | Purpose | Dependencies |
|------|------|---------|--------------|
| `theme.js` | Medium | Custom theme switcher with dropdown | Vanilla JS |
| `color-modes.js` | Small | Bootstrap color mode toggler | Bootstrap 5 |
| `g.js` | Small | Chart.js rendering functions | jQuery, Chart.js |
| `graph_api.js` | Small | Fetch graph data from API | jQuery, Fetch API |
| `leadtable.js` | Small | DataTables leaderboard init | jQuery, DataTables |
| `to.js` | Small | Page view counter | jQuery, Fetch API |
| `c.js` | Small | Card collapse/expand | jQuery |
| `sorttable.js` | Large | Legacy table sorting | None (2007 library) |
| `autocomplate.js` | Small | User/language autocomplete | jQuery, jQuery UI |
| `codes.js` | Small | Language code selector | Vanilla JS |
| `main.js` | Small | Bootstrap popover init | Bootstrap 5 (ES module) |

---

## Architecture & Code Quality Review

### Code Organization
Flat structure with one file per concern. No build system or module bundler.

### Design Patterns
- **Vanilla JS** -- Most files use plain JavaScript
- **jQuery plugins** -- DataTables, jQuery UI autocomplete
- **ES modules** -- `color-modes.js` and `main.js` use `import` syntax
- **Global functions** -- All functions are globally scoped

### Maintainability: 5/10
- Duplicate theme systems (`theme.js` + `color-modes.js`)
- Legacy library conflicts (`sorttable.js` vs DataTables)
- Filename typo (`autocomplate.js`)
- `console.log()` left in production code
- Arabic comments in some files

---

## Strengths

1. **Chart.js integration** -- Clean graph rendering with unique canvas IDs
2. **DataTables integration** -- Sortable, searchable tables
3. **Autocomplete** -- User-friendly input with API-backed suggestions
4. **Page view counters** -- Dynamic Wikipedia view data loading
5. **Theme persistence** -- localStorage-based theme preference

---

## Weaknesses

1. **Duplicate theme systems** -- `theme.js` and `color-modes.js` both manage themes
2. **Legacy library** -- `sorttable.js` (2007) conflicts with DataTables
3. **Filename typo** -- `autocomplate.js` should be `autocomplete.js`
4. **Debug output** -- `console.log()` in `graph_api.js` (lines 13, 36, 37)
5. **Global scope pollution** -- All functions attached to `window`
6. **Unused file** -- `main.js` not loaded by `head.php`
7. **Chart.js v2 API** -- `g.js` uses deprecated v2 scales format

---

## Critical Issues

### None (Client-Side Only)

JavaScript files run in the browser and don't directly access databases or server resources. Security issues are limited to:
- Potential XSS if chart labels contain malicious HTML (mitigated by Chart.js canvas rendering)
- `console.log()` exposing internal data in browser console

---

## Areas That Need Attention

- **Remove `console.log()` calls** -- From `graph_api.js`
- **Consolidate theme systems** -- Choose one (`theme.js` or `color-modes.js`)
- **Remove `sorttable.js`** -- DataTables handles all table sorting
- **Fix filename** -- Rename `autocomplate.js` to `autocomplete.js`
- **Update Chart.js API** -- Migrate from v2 to v3+ scales format
- **Remove or load `main.js`** -- Either use it or delete it

---

## Improvement Plan

### Quick Fixes
1. Remove `console.log()` from `graph_api.js`
2. Rename `autocomplate.js` to `autocomplete.js`
3. Remove unused `sorttable.js`

### Medium-Term
1. Consolidate `theme.js` and `color-modes.js` into one theme system
2. Update `g.js` to Chart.js v3+ API
3. Remove or integrate `main.js`

### Long-Term
1. Add a build system (Vite/webpack) for bundling and minification
2. Convert global functions to ES modules
3. Add ESLint for code quality

---

## Usage

Scripts are loaded via `src/head.php`:

```html
<!-- External libraries -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.datatables.net/2.2.2/js/dataTables.min.js"></script>

<!-- Application scripts -->
<script src="/Translation_Dashboard/js/to.js"></script>
<script src="/Translation_Dashboard/js/g.js"></script>
<script src="/Translation_Dashboard/js/theme.js"></script>
<script src="/Translation_Dashboard/js/c.js"></script>
<script type="module" src="/Translation_Dashboard/js/color-modes.js"></script>
```

### Graph Rendering
```javascript
// Simple line chart
graph_js(['Jan', 'Feb', 'Mar'], [10, 20, 30], 'myChart');

// API-driven chart
graph_js_params('myChart', { code: 'ar', camp: 'RTT' });
```

### Autocomplete
```html
<input class="td_user_input" type="text">
<input class="lang_input" type="text">
```
Automatically initialized with jQuery UI autocomplete.
