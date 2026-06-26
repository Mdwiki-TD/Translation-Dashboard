# CSS Module

## Project Overview

The `src/css/` directory contains the application's stylesheets. It extends Bootstrap 5 with custom styles for the Translation Dashboard's specific components, layouts, and responsive behavior.

### Main Features
- **Main application styles** -- Navbar, grid layout, tables, cards
- **Responsive tables** -- Mobile-friendly table layouts using card-style rows
- **Mobile utilities** -- Show/hide classes for mobile breakpoints
- **Theme system** -- Light/dark mode toggle with CSS custom properties
- **Dashboard styling** -- RTL support, color modes, icon lists

### Technologies
- CSS3 (custom properties, nesting, grid, flexbox)
- Bootstrap 5 (extended)
- Responsive design (760px/768px breakpoints)

---

## Project Structure

```
src/css/
├── styles.css                      # Main application stylesheet
├── dashboard_new1.css              # Dashboard + RTL support
├── mobile_format.css               # Mobile visibility utilities
├── Responsive_Table.css            # Responsive table layouts
└── theme.css                       # Theme toggle + dark mode
```

### File Details

| File | Size | Purpose |
|------|------|---------|
| `styles.css` | Largest | Main styles: navbar, grid, tables, cards, typography |
| `dashboard_new1.css` | Medium | RTL margins, icon lists, color modes, mobile navbar |
| `mobile_format.css` | Small | `.hide_on_mobile` / `.only_on_mobile` utilities |
| `Responsive_Table.css` | Medium | Mobile table-to-card conversion |
| `theme.css` | Medium | Theme toggle button, dropdown menu, dark mode vars |

---

## Key Components

### Main Styles (`styles.css`)
- **Navbar**: `.navbar`, `.med-logo`, active nav items
- **Grid Layout**: `.mainindex` with `grid-template-columns: 2fr 1fr`
- **Sticky Headers**: `thead { position: sticky }`
- **Compact Tables**: `.compact` class for dense data
- **Card Collapse**: `.collapsed-card`, `.expanding-card`
- **Selectpicker**: Custom `.selectpickerr` styling

### Responsive Tables (`Responsive_Table.css`)
Based on [bootstrap-mobile-tables](https://github.com/benjamin-Keller/bootstrap-mobile-tables).

At 760px breakpoint:
- Tables convert to block display
- `thead` hidden
- `data-content` attribute used as row labels
- Supports `.sided`, `.styleless`, `.striped` variants

### Theme System (`theme.css`)
- CSS custom properties (`--ps5-*` naming)
- Theme toggle with hover rotation animation
- `prefers-reduced-motion` support
- Light/dark/system mode switching

### Mobile Utilities (`mobile_format.css`)
```css
.hide_on_mobile      /* Hidden below 760px */
.only_on_mobile      /* Visible only below 760px */
.hide_on_mobile_bl   /* Block-level hidden on mobile */
.only_on_mobile_bl   /* Block-level visible only on mobile */
.hide_on_mobile_cell /* Table cell hidden on mobile */
.only_on_mobile_cell /* Table cell visible only on mobile */
```

---

## Architecture & Code Quality Review

### Code Organization
Well-organized with clear file separation by concern. Each file has a specific responsibility.

### Browser Support
- Modern CSS nesting used in `dashboard_new1.css` and `theme.css` (Chrome 120+, Firefox 117+)
- CSS custom properties (widely supported)
- CSS Grid and Flexbox (widely supported)

### Maintainability: 7/10
- Clear file separation
- Responsive breakpoints are consistent (760px/768px)
- Some duplication between `styles.css` and `dashboard_new1.css`

---

## Strengths

1. **Clean file separation** -- Each CSS file has a focused purpose
2. **Responsive design** -- Mobile-first approach with consistent breakpoints
3. **Theme support** -- Light/dark mode with system preference detection
4. **Accessibility** -- `prefers-reduced-motion` support, focus-visible outlines
5. **Modern CSS** -- Uses nesting, custom properties, grid

---

## Weaknesses

1. **Inconsistent breakpoints** -- 760px in some files, 768px in others, 960px in dashboard
2. **Duplicate theme systems** -- Both `theme.js` and `color-modes.js` loaded (JS issue)
3. **Mixed icon frameworks** -- Font Awesome + Bootstrap Icons (CSS classes)
4. **Old library included** -- `sorttable.js` (2007) conflicts with DataTables

---

## Areas That Need Attention

- **Standardize breakpoints** -- Use consistent values across all files
- **Remove old sorttable.js** -- DataTables handles sorting
- **Consolidate theme systems** -- Choose one JS theme switcher

---

## Usage

Stylesheets are loaded via `src/head.php`:

```php
<link rel="stylesheet" href="/Translation_Dashboard/css/styles.css">
<link rel="stylesheet" href="/Translation_Dashboard/css/dashboard_new1.css">
<link rel="stylesheet" href="/Translation_Dashboard/css/mobile_format.css">
<link rel="stylesheet" href="/Translation_Dashboard/css/Responsive_Table.css">
<link rel="stylesheet" href="/Translation_Dashboard/css/theme.css">
```

### Responsive Table Usage
```html
<table class="table-mobile-responsive">
  <thead>
    <tr><th>Name</th><th>Value</th></tr>
  </thead>
  <tbody>
    <tr>
      <td data-content="Name">Item 1</td>
      <td data-content="Value">42</td>
    </tr>
  </tbody>
</table>
```
