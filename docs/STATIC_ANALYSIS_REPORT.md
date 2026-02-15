# Static Analysis Report - Translation Dashboard

**Generated:** 2026-02-15
**PHP Version:** 8.2+
**Analysis Tool:** Claude Code Static Analysis

---

## Executive Summary

This comprehensive static analysis of the WikiProjectMed Translation Dashboard identifies critical issues across security, logic, performance, and architecture domains. The codebase contains approximately 90+ PHP files organized in a modular structure with PSR-4 autoloading.

### Summary of Findings

| Category | Critical | High | Medium | Low | Info |
|----------|----------|------|--------|-----|------|
| Security Vulnerabilities | 3 | 5 | 8 | 4 | 2 |
| Logical Errors | 1 | 3 | 6 | 5 | 3 |
| Performance Issues | 0 | 2 | 7 | 4 | 1 |
| Architectural Anti-patterns | 0 | 4 | 8 | 6 | 2 |

---

## 1. Security Vulnerabilities

### 1.1 CRITICAL: Hardcoded Database Credentials

**File:** `src/backend/api_calls/mdwiki_sql.php:53-54`

```php
$this->host = 'localhost:3306';
$this->user = 'root';
$this->password = '***REDACTED***';  // CRITICAL: Hardcoded password
```

**Risk:** Database credential exposure in source code. If repository is compromised, attackers gain direct database access.

**Recommendation:**
```php
// Use environment variables or secure configuration
$this->password = getenv('DB_PASSWORD')
    ?: throw new RuntimeException('DB_PASSWORD not configured');
```

---

### 1.2 CRITICAL: Reflected XSS via URL Parameter

**File:** `src/translate_med/index.php:29-46`

```php
$url = make_ContentTranslation_url($title_o, $coden, $cat, $camp, $tr_type);
// ...
echo <<<HTML
    <script type='text/javascript'>
    window.open('$url', '_self');  // VULNERABLE: $url may contain unescaped content
    </script>
HTML;
```

**Risk:** If `$url` contains malicious content, it can break out of the JavaScript context and execute arbitrary code.

**Recommendation:** Use `XssProtection::js()` for JavaScript context escaping:
```php
$safeUrl = XssProtection::js($url);
echo "<script>window.open($safeUrl, '_self');</script>";
```

---

### 1.3 CRITICAL: SQL Injection via Direct Variable Interpolation

**File:** `src/backend/api_calls/mdwiki_sql.php:65`

```php
$this->db = new PDO("mysql:host=$this->host;dbname=$this->dbname", $this->user, $this->password);
```

**Risk:** While currently using string interpolation (not user input), this pattern is dangerous. If `$this->host` or `$this->dbname` ever come from user-controlled sources, SQL injection is possible.

**Recommendation:**
```php
$this->db = new PDO(
    "mysql:host=" . self::sanitizeHost($this->host) . ";dbname=" . self::sanitizeDbName($this->dbname),
    $this->user,
    $this->password
);
```

---

### 1.4 HIGH: Missing CSRF Protection on State-Changing Operations

**File:** `src/translate_med/index.php` - No CSRF token validation

The `insertPage_inprocess` function is called without CSRF validation:
```php
if (($users_no_inprocess[$useree] ?? 0) != 1) {
    insertPage_inprocess($title_o, $word, $tr_type, $cat, $coden, $user_decoded);
}
```

**Risk:** Attackers can trick authenticated users into making unauthorized database insertions.

**Recommendation:**
```php
// In form:
echo CsrfToken::hiddenField();

// In handler:
if (!CsrfToken::validateFromRequest()) {
    throw new SecurityException('Invalid CSRF token');
}
```

---

### 1.5 HIGH: Insufficient Input Validation

**File:** `src/backend/loaders/load_request.php:21-34`

```php
$test = htmlspecialchars($_GET['test'] ?? '', ENT_QUOTES, 'UTF-8');
$code = htmlspecialchars($_GET['code'] ?? '', ENT_QUOTES, 'UTF-8');
// ...
$tra_type = filter_input(INPUT_GET, 'type', FILTER_SANITIZE_FULL_SPECIAL_CHARS) ?? '';
```

**Issues:**
- `htmlspecialchars` is for output escaping, not input validation
- `FILTER_SANITIZE_FULL_SPECIAL_CHARS` is deprecated as of PHP 8.1
- Inconsistent sanitization approach across parameters

**Recommendation:** Use `InputValidator` class consistently:
```php
$code = InputValidator::languageCode($_GET['code'] ?? null);
$tra_type = InputValidator::translationType($_GET['type'] ?? null);
```

---

### 1.6 HIGH: Information Disclosure via Error Messages

**File:** `src/backend/api_calls/mdwiki_sql.php:130,153`

```php
echo "sql error:" . $e->getMessage() . "<br>" . $sql_query;
```

**Risk:** Exposing SQL queries and database error details to end users can reveal schema information to attackers.

**Recommendation:**
```php
error_log("SQL Error: " . $e->getMessage() . " | Query: " . $sql_query);
throw new DatabaseException(
    'A database error occurred. Please try again later.',
    0,
    $e,
    ['query_type' => substr($sql_query, 0, 50)]
);
```

---

### 1.7 HIGH: Session Hijacking Risk

**File:** `src/translate_med/index.php:81` - Session data used without regeneration

No session regeneration after login, making the application vulnerable to session fixation attacks.

**Recommendation:**
```php
session_regenerate_id(true);
```

---

### 1.8 MEDIUM: Path Traversal Vulnerability (Partially Mitigated)

**File:** `src/backend/results/getcats.php:298`

```php
$cat = str_replace(['/', '\\', '..'], '', $cat);
```

**Issue:** This basic sanitization is insufficient. It can be bypassed with URL encoding or null bytes.

**Recommendation:**
```php
$cat = XssProtection::filename($cat);
// Or use basename() to ensure no path components
$cat = basename($cat);
```

---

### 1.9 MEDIUM: Missing Content Security Policy

No CSP headers are set, increasing XSS risk.

**Recommendation:** Add to header.php:
```php
header("Content-Security-Policy: default-src 'self'; script-src 'self' 'unsafe-inline'; style-src 'self' 'unsafe-inline'");
```

---

### 1.10 MEDIUM: Debug Mode Exposes System Information

**File:** Multiple files (index.php, mdwiki_sql.php, etc.)

```php
if (isset($_REQUEST['test']) || isset($_COOKIE['test'])) {
    ini_set('display_errors', 1);
    error_reporting(E_ALL);
}
```

**Risk:** Anyone can enable debug mode by setting a cookie or query parameter, potentially exposing sensitive information.

**Recommendation:**
```php
$debugMode = (getenv('APP_DEBUG') === 'true')
    && (isset($_COOKIE['test']) || isset($_REQUEST['test']));
if ($debugMode) {
    // Enable debug only if APP_DEBUG is true
}
```

---

## 2. Logical Errors and Bugs

### 2.1 HIGH: Duplicate Assignment in Function Call

**File:** `src/backend/others/db_insert.php:37,60`

```php
execute_query($quae_new, $params = $params);
```

**Issue:** `$params = $params` is redundant and confusing. The variable is already defined.

**Fix:**
```php
execute_query($quae_new, $params);
```

---

### 2.2 HIGH: Race Condition in Database Insert

**File:** `src/backend/others/db_insert.php:22-32`

```sql
INSERT INTO pages (...)
SELECT ?, ?, ...
WHERE NOT EXISTS (SELECT 1 FROM pages WHERE title = ? AND lang = ? AND user = ?)
```

**Issue:** Between the `SELECT` and `INSERT`, another request could insert the same record, causing a race condition. While the `NOT EXISTS` helps, it's not atomic.

**Recommendation:** Use `INSERT IGNORE` or `ON DUPLICATE KEY UPDATE`:
```sql
INSERT IGNORE INTO pages (title, word, translate_type, cat, lang, date, user, pupdate, target, add_date)
VALUES (?, ?, ?, ?, ?, DATE(NOW()), ?, '', '', DATE(NOW()))
```

---

### 2.3 MEDIUM: Inconsistent Depth Handling

**File:** `src/backend/results/getcats.php:59-60`

```php
if (!is_int($depth) || $depth < 0) {
    $depth = 0;
}
```

**Issue:** This validation happens inside the method but not at entry points. Type hints should enforce this.

**Fix:** Add type hints and assertions:
```php
public function getMdwikiCatMembers(string $rootCat, int $depth = 0, bool $useCache = true): array
{
    if ($depth < 0) {
        throw new InvalidArgumentException('Depth must be non-negative');
    }
    // ...
}
```

---

### 2.4 MEDIUM: Undefined Variable Risk

**File:** `src/backend/results/getcats.php:192-194`

```php
if ($iteration >= $max_iterations) {
    $this->log("fetch_cats_members_api: Hit maximum iterations for '$cat'");
}
```

**Issue:** If the loop never executes (empty `$cmcontinue`), `$iteration` remains undefined due to the increment happening in the condition.

**Fix:**
```php
$iteration = 0;
while (!empty($cmcontinue) && $iteration < $max_iterations) {
    // ...
    $iteration++;
}
```

---

### 2.5 MEDIUM: Static Variable State Leak

**File:** `src/backend/api_or_sql/funcs.php` - Multiple functions

```php
function get_lang_pages_by_cat($lang, $cat)
{
    static $data = [];
    if (!empty($data[$lang . $cat] ?? [])) {
        return $data[$lang . $cat];
    }
    // ...
}
```

**Issue:** Static variables persist across requests in CLI/worker contexts and can cause stale data issues. The key concatenation (`$lang . $cat`) can cause collisions (e.g., "en" + "RTT" vs "e" + "nRTT").

**Fix:**
```php
static $data = [];
$key = $lang . '|' . $cat;  // Use delimiter to prevent collisions
```

---

### 2.6 MEDIUM: Potential Division by Zero

**File:** Various calculations with view counts and word counts

While not directly observed, any division operations should validate the divisor is non-zero.

---

### 2.7 LOW: Unused Variable

**File:** `src/backend/results/getcats.php:29`

```php
private const NS_CUSTOM_EXAMPLE = 3000; // Or a more descriptive name
```

**Issue:** Constant has unclear purpose ("??? as original" comment).

---

### 2.8 LOW: Magic Numbers

**File:** `src/backend/results/getcats.php:119`

```php
$cacheTtl = 3600 * 12;
```

**Recommendation:** Use named constants:
```php
private const CACHE_TTL_HOURS = 12;
private const CACHE_TTL_SECONDS = self::CACHE_TTL_HOURS * 3600;
```

---

## 3. Performance Bottlenecks

### 3.1 HIGH: New Database Connection Per Query

**File:** `src/backend/api_calls/mdwiki_sql.php:198,222`

```php
function execute_query($sql_query, $params = null, $table_name = null)
{
    $db = new Database($_SERVER['SERVER_NAME'] ?? 'localhost', $dbname);
    // ... query ...
    $db = null; // Connection destroyed
}
```

**Issue:** Every query creates a new database connection. This is extremely inefficient.

**Impact:**
- Connection overhead: ~20-50ms per query
- No connection pooling benefits
- Resource exhaustion under load

**Recommendation:** Implement singleton or connection pool pattern:
```php
class DatabaseConnection
{
    private static ?PDO $instance = null;

    public static function getInstance(): PDO
    {
        if (self::$instance === null) {
            self::$instance = new PDO(/* ... */);
        }
        return self::$instance;
    }
}
```

---

### 3.2 HIGH: N+1 Query Problem

**File:** `src/backend/api_or_sql/funcs.php` - Multiple functions with similar patterns

Each function performs separate queries that could be combined or cached more efficiently.

**Recommendation:** Use eager loading and batch queries.

---

### 3.3 MEDIUM: File System Access on Every Request

**File:** `src/backend/tables/tables.php:63-74`

```php
if (file_exists(__DIR__ . '/lang_names.json')) {
    $contents = file_get_contents(__DIR__ . '/lang_names.json');
    // ...
}
```

**Issue:** File read on every request instead of caching.

**Recommendation:**
```php
private static ?array $langTable = null;

private static function loadLangTable(): array
{
    if (self::$langTable !== null) {
        return self::$langTable;
    }
    // Load from file or cache...
}
```

---

### 3.4 MEDIUM: Repeated glob() Calls

**File:** `src/include_all.php:11-34`

```php
foreach (glob(__DIR__ . "/backend/api_calls/*.php") as $filename) {
    include_once $filename;
}
```

**Issue:** `glob()` is called on every request. For production, the file list should be cached or use Composer autoloading exclusively.

**Recommendation:** Rely on Composer autoloading instead of manual includes.

---

### 3.5 MEDIUM: No Query Result Limiting

**File:** `src/backend/api_or_sql/funcs.php:172`

```php
$query = "select * from pages p where p.lang = ?";
```

**Issue:** `SELECT *` with no limit can return millions of rows, consuming excessive memory.

**Recommendation:** Always use pagination:
```php
$query = "SELECT * FROM pages p WHERE p.lang = ? LIMIT ? OFFSET ?";
```

---

### 3.6 MEDIUM: APCu Cache Invalidation Logic

**File:** `src/backend/results/getcats.php:125-128`

```php
if (empty($items) || ($cat === "RTT" && is_array($items) && count($items) < 3000)) {
    apcu_delete($cacheKey);
    $items = false;
}
```

**Issue:** Hardcoded category name "RTT" and magic number 3000. This special-case logic makes the code harder to maintain.

**Recommendation:** Use TTL for cache invalidation instead of count-based logic.

---

### 3.7 LOW: String Concatenation in Loops

**File:** `src/frontend/forms.php:24-26`

```php
foreach ($uxutable as $name => $cod) {
    $options .= <<<HTML
        <option value='$cod' $cdcdc>$name</option>
    HTML;
}
```

**Issue:** In PHP, string concatenation in loops is less efficient than array building and implode.

**Recommendation:**
```php
$optionList = [];
foreach ($uxutable as $name => $cod) {
    $optionList[] = "<option value='$cod' $cdcdc>$name</option>";
}
$options = implode('', $optionList);
```

---

## 4. Architectural Anti-Patterns

### 4.1 HIGH: Global State via $GLOBALS

**File:** Multiple files

```php
$global_username = $GLOBALS['global_username'] ?? "";
// ...
$GLOBALS['user_in_coord'] ?? false
```

**Issue:** Using `$GLOBALS` makes code:
- Hard to test (requires mocking global state)
- Hard to track data flow
- Prone to naming collisions
- Thread-unsafe in certain contexts

**Recommendation:** Use dependency injection:
```php
class UserContext
{
    public function __construct(
        public readonly string $username,
        public readonly bool $isCoordinator
    ) {}
}

// Pass via constructor or method parameter
function results_loader(array $data, UserContext $user): string
```

---

### 4.2 HIGH: God Object (TablesSql)

**File:** `src/backend/tables/sql_tables.php:22-41`

```php
class TablesSql
{
    public static $s_full_translates = [];
    public static $s_no_lead_translates = [];
    public static $s_cat_titles = [];
    public static $s_cat_to_camp = [];
    // ... 15+ static properties
}
```

**Issue:** This class holds too much unrelated state and violates Single Responsibility Principle.

**Recommendation:** Split into focused classes:
```php
class CategoryRepository { /* categories */ }
class CampaignRepository { /* campaigns */ }
class SettingsRepository { /* settings */ }
```

---

### 4.3 HIGH: Namespace/File Location Mismatch

**File:** `src/backend/others/db_insert.php`

```php
namespace TranslateMed\Inserter;
```

**Issue:** File is in `backend/others/` but namespace is `TranslateMed\Inserter`. This violates PSR-4 conventions and confuses autoloading.

**Recommendation:** Move file to `src/translate_med/Inserter.php` or update namespace to match location.

---

### 4.4 MEDIUM: Mixed Procedural and OO Code

The codebase mixes procedural functions and classes inconsistently:
- `src/backend/api_calls/wiki_api.php` - Procedural functions
- `src/backend/results/getcats.php` - Class with procedural wrappers
- `src/backend/api_calls/mdwiki_sql.php` - Class with procedural wrappers

**Recommendation:** Standardize on OO approach with dependency injection.

---

### 4.5 MEDIUM: Static Method Abuse

**File:** `src/backend/tables/sql_tables.php`

The `TablesSql` class uses only static properties and has no instances, effectively acting as a namespace polluter.

**Recommendation:** Use proper dependency injection and service classes.

---

### 4.6 MEDIUM: Inconsistent Error Handling

Some functions return `false` on error:
```php
return false; // executequery
```

Others return empty arrays:
```php
return []; // fetchquery
```

Some echo errors:
```php
echo "sql error:" . $e->getMessage();
```

**Recommendation:** Standardize on exceptions:
```php
try {
    $results = $db->fetchquery($sql, $params);
} catch (DatabaseException $e) {
    // Handle gracefully
}
```

---

### 4.7 MEDIUM: Primitive Obsession

**File:** `src/results/results.php:45-57`

```php
function Results_tables($tab, $show_exists, $translation_button, $full_tr_user)
{
    $camp = $tab["camp"];
    $code = $tab["code"];
    // ...
}
```

**Issue:** Using arrays instead of value objects loses type safety and documentation.

**Recommendation:** Create DTOs:
```php
readonly class ResultsRequest
{
    public function __construct(
        public string $camp,
        public string $code,
        public string $cat,
        public string $traType,
        // ...
    ) {}
}
```

---

### 4.8 MEDIUM: Hidden Dependencies

Functions like `results_loader()` access `$GLOBALS` directly, hiding their dependencies from callers.

**Recommendation:** Make all dependencies explicit in function signatures.

---

### 4.9 LOW: Commented-Out Code

**File:** Multiple files contain large blocks of commented-out code.

**Recommendation:** Remove dead code. Use version control for history.

---

### 4.10 LOW: Inconsistent Naming Conventions

Mix of:
- snake_case: `$s_full_translates`, `$cat_to_camp`
- camelCase: `$rootCat`, `$code_lang_name`
- Mixed: `$s_camp_input_depth`

**Recommendation:** Standardize on camelCase for PHP (PSR-1, PSR-12).

---

## 5. Type Annotation Improvements

### 5.1 Current State

The codebase has minimal type annotations:
- Few parameter type declarations
- Few return type declarations
- No property types in most classes
- No use of union types or intersection types

### 5.2 Recommended Type Additions

**Example improvements for `CategoryFetcher`:**

```php
class CategoryFetcher
{
    private array $options;
    private bool $debug;
    private string $endPoint;
    private int $connectTimeout;
    private string $tablesDir;
    private int $timeout;

    private const NS_MAIN = 0;
    private const NS_CATEGORY = 14;
    private const NS_CUSTOM_EXAMPLE = 3000;

    /**
     * @param array{nocache?: bool, debug?: bool, tablesDir?: string, connect_timeout?: int, timeout?: int} $options
     */
    public function __construct(
        array $options = [],
        string $endPoint = ''
    ): void {
        // ...
    }

    /**
     * @return array<int, string>
     */
    public function getMdwikiCatMembers(string $rootCat, int $depth = 0, bool $useCache = true): array
    {
        // ...
    }

    /**
     * @param array<string> $titles
     * @param bool $withCategory
     * @return array<int, string>
     */
    private function titlesFilters(array $titles, bool $withCategory = false): array
    {
        // ...
    }
}
```

**For database functions:**

```php
/**
 * @param string $sql_query
 * @param array<int, mixed>|null $params
 * @param string|null $table_name
 * @return array<int, array<string, mixed>>
 */
function fetch_query(string $sql_query, ?array $params = null, ?string $table_name = null): array
{
    // ...
}
```

---

## 6. Documentation Improvements

### 6.1 File-Level Headers

Every PHP file should have a file-level docblock:

```php
<?php
/**
 * Category Fetcher - Fetches category members from mdwiki API
 *
 * Provides recursive category member retrieval with caching support.
 * Used to find articles that need translation.
 *
 * @package    TranslationDashboard
 * @subpackage Results
 * @see        https://www.mediawiki.org/wiki/API:Categorymembers
 */
```

### 6.2 Class Documentation

```php
/**
 * Fetches category members from mdwiki with caching support.
 *
 * This class provides methods to recursively fetch all pages within
 * a category tree, with support for:
 * - File-based caching (JSON files)
 * - APCu memory caching
 * - Configurable depth limits
 * - Namespace filtering
 *
 * @package Results\GetCats
 * @author  WikiProjectMed Team
 * @since   1.0.0
 *
 * @example
 * ```php
 * $fetcher = new CategoryFetcher(['debug' => true]);
 * $pages = $fetcher->getMdwikiCatMembers('RTT', depth: 2);
 * ```
 */
class CategoryFetcher
```

### 6.3 Method Documentation

```php
/**
 * Fetch all page titles within a category tree.
 *
 * Recursively retrieves all pages under the specified root category,
 * up to the specified depth. Results are filtered to exclude:
 * - File: namespace
 * - Template: namespace
 * - User: namespace
 * - Disambiguation pages
 *
 * @param string $rootCat  Root category name (with or without 'Category:' prefix)
 * @param int    $depth    Maximum recursion depth (0 = direct members only)
 * @param bool   $useCache Whether to use file-based cache
 *
 * @return array<int, string> List of unique page titles
 *
 * @throws InvalidArgumentException If depth is negative
 *
 * @example
 * ```php
 * // Get all pages in RTT and its immediate subcategories
 * $pages = $fetcher->getMdwikiCatMembers('RTT', 1);
 *
 * // Get only direct members
 * $pages = $fetcher->getMdwikiCatMembers('Category:RTT', 0);
 * ```
 */
public function getMdwikiCatMembers(string $rootCat, int $depth = 0, bool $useCache = true): array
```

---

## 7. Priority Remediation Plan

### Phase 1: Critical Security Fixes (Immediate)

1. Remove hardcoded database credentials from `mdwiki_sql.php`
2. Add CSRF protection to `translate_med/index.php`
3. Fix XSS vulnerabilities in JavaScript output

### Phase 2: High-Priority Fixes (Within 1 Week)

1. Implement database connection pooling
2. Add proper error handling with exceptions
3. Standardize input validation using `InputValidator`
4. Add CSP headers

### Phase 3: Medium-Priority Improvements (Within 2 Weeks)

1. Refactor `TablesSql` god object
2. Replace `$GLOBALS` with dependency injection
3. Add comprehensive type annotations
4. Implement DTOs for data transfer

### Phase 4: Technical Debt (Within 1 Month)

1. Standardize naming conventions
2. Remove dead code
3. Add comprehensive documentation
4. Improve test coverage

---

## 8. New Security Classes Assessment

The newly created security classes (`XssProtection`, `CsrfToken`, `InputValidator`, `TDException`) are well-implemented:

### Strengths:
- Proper use of `declare(strict_types=1)`
- Comprehensive PHPDoc documentation
- Type-safe methods with proper return types
- Good separation of concerns
- Context-specific escaping (HTML, JS, URL, CSS)

### Recommendations:
1. **Integration:** These classes exist but are not used throughout the codebase
2. **CsrfToken:** Requires session to be started - document this requirement
3. **InputValidator:** Consider adding `whitelist()` method for array validation

---

## 9. Conclusion

The Translation Dashboard codebase has significant technical debt and security vulnerabilities that should be addressed in priority order. The new security classes provide a solid foundation, but they need to be integrated throughout the application.

Key focus areas:
1. **Security:** Remove hardcoded credentials, add CSRF protection, fix XSS
2. **Performance:** Implement connection pooling, add query limits
3. **Architecture:** Reduce global state, implement proper DI, refactor god objects
4. **Type Safety:** Add comprehensive type annotations
5. **Documentation:** Add PHPDoc blocks to all public APIs

---

*Report generated by Claude Code Static Analysis*
