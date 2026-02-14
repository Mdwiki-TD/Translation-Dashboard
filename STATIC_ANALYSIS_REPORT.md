# Comprehensive Static Analysis Report

**Project**: WikiProjectMed Translation Dashboard
**Analysis Date**: 2026-02-14
**PHP Version Target**: 8.2+
**Total Files Analyzed**: 86 PHP files

---

## Table of Contents

1. [Executive Summary](#1-executive-summary)
2. [Critical Security Issues](#2-critical-security-issues)
3. [Architectural Anti-Patterns](#3-architectural-anti-patterns)
4. [Performance Bottlenecks](#4-performance-bottlenecks)
5. [Type Annotation Standards](#5-type-annotation-standards)
6. [Documentation Standards](#6-documentation-standards)
7. [Refactored Code Examples](#7-refactored-code-examples)
8. [Implementation Roadmap](#8-implementation-roadmap)

---

## 1. Executive Summary

### Risk Assessment Matrix

| Category | Risk Level | Count | Priority |
|----------|------------|-------|----------|
| SQL Injection | HIGH | 4 | P0 |
| XSS Vulnerabilities | HIGH | 8 | P0 |
| CSRF Missing | CRITICAL | All forms | P0 |
| Hardcoded Credentials | CRITICAL | 1 | P0 |
| Debug Mode Exposure | HIGH | 12 files | P0 |
| Missing Type Declarations | MEDIUM | 95%+ | P1 |
| Missing Documentation | MEDIUM | 90%+ | P1 |
| Inconsistent Error Handling | MEDIUM | 15+ | P2 |

### Overall Risk Rating: **CRITICAL**

The application has multiple critical security vulnerabilities that require immediate remediation before production deployment.

---

## 2. Critical Security Issues

### 2.1 Hardcoded Database Credentials

**File**: `src/backend/api_calls/mdwiki_sql.php:54`

```php
// VULNERABLE CODE:
if ($server_name === 'localhost') {
    $this->host = 'localhost:3306';
    $this->dbname = $ts_mycnf['user'] . "__" . $this->db_suffix;
    $this->user = 'root';
    $this->password = 'root11';  // HARDCODED PASSWORD!
}
```

**Remediation**:
```php
// SECURE CODE:
private function set_db(string $server_name): void
{
    $ts_mycnf = parse_ini_file($this->home_dir . "/confs/db.ini");

    if ($server_name === 'localhost') {
        $this->host = getenv('DB_HOST') ?: 'localhost:3306';
        $this->dbname = ($ts_mycnf['user'] ?? getenv('DB_USER')) . "__" . $this->db_suffix;
        $this->user = getenv('DB_USER') ?: throw new RuntimeException('DB_USER not configured');
        $this->password = getenv('DB_PASSWORD') ?: throw new RuntimeException('DB_PASSWORD not configured');
    } else {
        // ... production config
    }
}
```

### 2.2 SQL Injection via Dynamic Column Names

**File**: `src/backend/api_or_sql/top.php:73-77`

```php
// VULNERABLE CODE:
$top_params = [
    "year" => "YEAR(p.pupdate)",
    "month" => "MONTH(p.pupdate)",
    "user_group" => "u.user_group",
    "cat" => "p.cat"
];

foreach ($top_params as $key => $column) {
    if (isvalid($to_add[$key] ?? '')) {
        $query .= " AND $column = ?";  // Column name not validated!
        $params[] = $to_add[$key];
    }
}
```

**Remediation**:
```php
// SECURE CODE:
/**
 * @var array<string, string> Whitelisted column mappings
 */
private const ALLOWED_COLUMNS = [
    "year" => "YEAR(p.pupdate)",
    "month" => "MONTH(p.pupdate)",
    "user_group" => "u.user_group",
    "cat" => "p.cat"
];

/**
 * @param array<string, mixed> $to_add
 * @return array{0: string, 1: array<int, mixed>}
 */
function add_top_params_secure(string $query, array $params, array $to_add): array
{
    foreach (self::ALLOWED_COLUMNS as $key => $column) {
        if (isset($to_add[$key]) && $this->isValidValue($to_add[$key])) {
            $query .= " AND {$column} = ?";
            $params[] = $to_add[$key];
        }
    }
    return [$query, $params];
}
```

### 2.3 XSS Vulnerabilities

**File**: `src/frontend/html.php:24-27`

```php
// VULNERABLE CODE:
$cdcdc = $code == $cod ? "selected" : "";
$options .= <<<HTML
    <option value='$cod' $cdcdc>$name</option>
HTML;
```

**Remediation**:
```php
// SECURE CODE:
/**
 * @param string $code The currently selected code
 * @param string $cod The option code value
 * @param string $name The display name
 * @return string HTML option element
 */
function build_option_element(string $code, string $cod, string $name): string
{
    $selected = ($code === $cod) ? 'selected' : '';
    $escaped_cod = htmlspecialchars($cod, ENT_QUOTES, 'UTF-8');
    $escaped_name = htmlspecialchars($name, ENT_QUOTES, 'UTF-8');

    return sprintf(
        '<option value="%s" %s>%s</option>',
        $escaped_cod,
        $selected,
        $escaped_name
    );
}
```

### 2.4 Debug Mode Backdoor

**Files**: Multiple (12 files affected)

```php
// VULNERABLE CODE - Found in 12 files:
if (isset($_REQUEST['test']) || isset($_COOKIE['test'])) {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
}
```

**Remediation**:
```php
// SECURE CODE:
// Create a dedicated config file: src/config/debug.php
<?php
declare(strict_types=1);

namespace TD\Config;

class DebugConfig
{
    private const ALLOWED_DEBUG_IPS = ['127.0.0.1', '::1'];
    private const DEBUG_SECRET = ''; // Set via environment variable

    public static function isDebugEnabled(): bool
    {
        // Only allow via authenticated admin session + IP check
        $session = $_SESSION['admin_debug'] ?? false;
        $ip = $_SERVER['REMOTE_ADDR'] ?? '';

        return $session && in_array($ip, self::ALLOWED_DEBUG_IPS, true);
    }

    public static function enableIfAllowed(): void
    {
        if (self::isDebugEnabled()) {
            ini_set('display_errors', '1');
            ini_set('display_startup_errors', '1');
            error_reporting(E_ALL);
        }
    }
}
```

### 2.5 Missing CSRF Protection

**Issue**: All forms lack CSRF tokens

**Remediation**:
```php
// Create: src/security/csrf.php
<?php
declare(strict_types=1);

namespace TD\Security;

class CsrfToken
{
    private const TOKEN_NAME = 'csrf_token';
    private const TOKEN_LENGTH = 32;

    /**
     * Generate a new CSRF token
     */
    public static function generate(): string
    {
        if (!isset($_SESSION[self::TOKEN_NAME])) {
            $_SESSION[self::TOKEN_NAME] = bin2hex(random_bytes(self::TOKEN_LENGTH));
        }
        return $_SESSION[self::TOKEN_NAME];
    }

    /**
     * Validate a CSRF token
     */
    public static function validate(string $token): bool
    {
        $sessionToken = $_SESSION[self::TOKEN_NAME] ?? '';
        return hash_equals($sessionToken, $token);
    }

    /**
     * Generate hidden form field
     */
    public static function field(): string
    {
        $token = self::generate();
        return sprintf(
            '<input type="hidden" name="%s" value="%s">',
            self::TOKEN_NAME,
            htmlspecialchars($token, ENT_QUOTES, 'UTF-8')
        );
    }
}

// Usage in forms:
// <form method="POST">
//     <?php echo TD\Security\CsrfToken::field(); ?>
//     ...
// </form>
```

---

## 3. Architectural Anti-Patterns

### 3.1 Global State Overuse

**Issue**: Heavy reliance on `$GLOBALS['global_username']`

**Files Affected**: 15+ files

```php
// ANTI-PATTERN:
$global_username = $GLOBALS['global_username'] ?? "";
```

**Remediation**: Use dependency injection

```php
// RECOMMENDED PATTERN:
<?php
declare(strict_types=1);

namespace TD\Auth;

/**
 * Represents an authenticated user context
 */
final class UserContext
{
    public function __construct(
        private readonly string $username,
        private readonly bool $isAuthenticated,
        private readonly bool $isCoordinator = false
    ) {}

    public function getUsername(): string
    {
        return $this->username;
    }

    public function isAuthenticated(): bool
    {
        return $this->isAuthenticated;
    }

    public function isCoordinator(): bool
    {
        return $this->isCoordinator;
    }

    public function getDisplayName(): string
    {
        return $this->isAuthenticated ? $this->username : 'Guest';
    }
}

// Factory to create from session:
final class UserContextFactory
{
    public static function fromSession(): UserContext
    {
        $username = $_SESSION['username'] ?? '';
        $isAuthenticated = !empty($username);

        return new UserContext(
            username: $username,
            isAuthenticated: $isAuthenticated,
            isCoordinator: self::checkCoordinatorStatus($username)
        );
    }

    private static function checkCoordinatorStatus(string $username): bool
    {
        // Delegate to coordinator service
        return CoordinatorService::isActive($username);
    }
}
```

### 3.2 Mixed Responsibilities in Functions

**File**: `src/backend/api_or_sql/funcs.php`

**Issue**: Functions mix caching, API calls, and SQL queries

```php
// ANTI-PATTERN:
function get_user_pages($user_main, $year_y, $lang_y)
{
    static $data = [];  // Caching mixed with logic
    // ...
    $api_params = [...];  // API params mixed with SQL
    $query = <<<SQL
        SELECT DISTINCT...
    SQL;
    // ...
}
```

**Remediation**: Apply Repository Pattern

```php
// RECOMMENDED PATTERN:
<?php
declare(strict_types=1);

namespace TD\Repository;

use TD\Entity\Page;
use TD\Database\ConnectionInterface;

/**
 * Repository for Page entities
 *
 * @extends Repository<Page>
 */
final class PageRepository
{
    public function __construct(
        private readonly ConnectionInterface $connection,
        private readonly CacheInterface $cache
    ) {}

    /**
     * Find pages by user with optional filters
     *
     * @param string $user Username
     * @param PageFilters $filters Optional filters
     * @return array<Page>
     */
    public function findByUser(string $user, PageFilters $filters = new PageFilters()): array
    {
        $cacheKey = $this->generateCacheKey($user, $filters);

        return $this->cache->remember(
            $cacheKey,
            fn() => $this->fetchByUser($user, $filters),
            ttl: 3600
        );
    }

    /**
     * @return array<Page>
     */
    private function fetchByUser(string $user, PageFilters $filters): array
    {
        $query = $this->buildQuery($filters);
        $params = $this->buildParams($user, $filters);

        $results = $this->connection->fetchAll($query, $params);

        return array_map(
            fn(array $row) => Page::fromArray($row),
            $results
        );
    }

    // ...
}
```

### 3.3 Inconsistent Error Handling

**Issue**: Mix of `echo`, `error_log()`, and throwing exceptions

```php
// INCONSISTENT - File: mdwiki_sql.php
} catch (PDOException $e) {
    echo "sql error:" . $e->getMessage() . "<br>" . $sql_query;  // Exposes SQL!
    return false;  // Inconsistent return type
}
```

**Remediation**: Implement consistent exception hierarchy

```php
// RECOMMENDED PATTERN:
<?php
declare(strict_types=1);

namespace TD\Exception;

/**
 * Base exception for all Translation Dashboard exceptions
 */
abstract class TDException extends \RuntimeException
{
    public function __construct(
        string $message,
        int $code = 0,
        ?\Throwable $previous = null,
        private readonly array $context = []
    ) {
        parent::__construct($message, $code, $previous);
    }

    /**
     * Get safe context for logging (no sensitive data)
     */
    public function getSafeContext(): array
    {
        return $this->context;
    }
}

/**
 * Database-related exceptions
 */
final class DatabaseException extends TDException
{
    public static function connectionFailed(string $host): self
    {
        return new self(
            message: "Failed to connect to database",
            context: ['host' => $host]
        );
    }

    public static function queryFailed(string $query, \Throwable $previous): self
    {
        return new self(
            message: "Query execution failed",
            context: ['query_type' => self::extractQueryType($query)],
            previous: $previous
        );
    }

    private static function extractQueryType(string $query): string
    {
        return strtoupper(substr(trim($query), 0, 6));
    }
}
```

---

## 4. Performance Bottlenecks

### 4.1 N+1 Query Problem

**File**: `src/backend/api_or_sql/funcs.php`

```php
// PROBLEMATIC CODE:
function get_user_views($user, $year_y, $lang_y)
{
    // Query is executed per user/lang/year combination
    // Called in loops elsewhere
}
```

**Remediation**: Batch queries with eager loading

```php
// RECOMMENDED PATTERN:
/**
 * @param array<string> $users
 * @return array<string, array<string, int>>
 */
public function getBulkUserViews(array $users, int $year, ?string $lang = null): array
{
    $placeholders = implode(',', array_fill(0, count($users), '?'));

    $query = <<<SQL
        SELECT p.user, v.target, v.lang, SUM(v.views) as views
        FROM views_new_all v
        JOIN pages p ON p.target = v.target AND p.lang = v.lang
        WHERE p.user IN ({$placeholders})
        AND YEAR(p.pupdate) = ?
    SQL;

    $params = [...$users, $year];

    if ($lang !== null) {
        $query .= " AND p.lang = ?";
        $params[] = $lang;
    }

    $query .= " GROUP BY p.user, v.target, v.lang";

    // Process and return structured data
}
```

### 4.2 Static Cache Without Invalidation

```php
// PROBLEMATIC CODE:
function get_coordinator()
{
    static $coordinator = [];  // Never invalidated
    if (!empty($coordinator ?? [])) {
        return $coordinator;
    }
    // ...
}
```

**Remediation**: Use proper caching with TTL

```php
// RECOMMENDED PATTERN:
<?php
declare(strict_types=1);

namespace TD\Cache;

/**
 * Simple file-based cache with TTL support
 */
final class FileCache implements CacheInterface
{
    public function __construct(
        private readonly string $cacheDir,
        private readonly int $defaultTtl = 3600
    ) {}

    /**
     * @template T
     * @param callable(): T $callback
     * @return T
     */
    public function remember(string $key, callable $callback, ?int $ttl = null): mixed
    {
        $cached = $this->get($key);
        if ($cached !== null) {
            return $cached;
        }

        $value = $callback();
        $this->set($key, $value, $ttl ?? $this->defaultTtl);

        return $value;
    }

    public function get(string $key): mixed
    {
        $path = $this->getPath($key);
        if (!file_exists($path)) {
            return null;
        }

        $data = json_decode(file_get_contents($path), true);
        if ($data['expires_at'] < time()) {
            unlink($path);
            return null;
        }

        return $data['value'];
    }

    public function set(string $key, mixed $value, int $ttl): bool
    {
        $path = $this->getPath($key);
        $data = [
            'value' => $value,
            'expires_at' => time() + $ttl
        ];

        return file_put_contents($path, json_encode($data)) !== false;
    }

    public function forget(string $key): bool
    {
        $path = $this->getPath($key);
        return file_exists($path) && unlink($path);
    }

    private function getPath(string $key): string
    {
        return $this->cacheDir . '/' . md5($key) . '.cache';
    }
}
```

---

## 5. Type Annotation Standards

### 5.1 PHP 8.2+ Type Declarations

All code should use strict typing and comprehensive type declarations:

```php
<?php
declare(strict_types=1);

namespace TD\Backend\ApiOrSql;

use TD\Entity\{Page, User, Language};
use TD\Database\ConnectionInterface;

/**
 * Service for retrieving leaderboard and statistics data
 *
 * This class provides methods to query translation statistics
 * from either the local database or remote API endpoints.
 */
final class LeaderboardService
{
    /**
     * Column whitelist for dynamic queries
     *
     * @var array<string, non-empty-string>
     */
    private const COLUMN_WHITELIST = [
        'year' => 'YEAR(p.pupdate)',
        'month' => 'MONTH(p.pupdate)',
        'user_group' => 'u.user_group',
        'cat' => 'p.cat',
    ];

    /**
     * @param ConnectionInterface $connection Database connection
     * @param PositiveInt $cacheTtl Cache time-to-live in seconds
     */
    public function __construct(
        private readonly ConnectionInterface $connection,
        private readonly int $cacheTtl = 3600
    ) {}

    /**
     * Get top translators by number of translations
     *
     * @param FilterOptions $options Filter criteria
     * @return array<TranslatorStats> List of translator statistics
     * @throws DatabaseException When query execution fails
     */
    public function getTopTranslators(FilterOptions $options): array
    {
        // Implementation
    }

    /**
     * Get top target languages by translation count
     *
     * @param int|null $year Filter by year (null for all time)
     * @param string|null $userGroup Filter by user group
     * @param string|null $category Filter by translation category
     * @param int<1,12>|null $month Filter by month (1-12)
     * @return array<LanguageStats> List of language statistics
     */
    public function getTopLanguages(
        ?int $year,
        ?string $userGroup,
        ?string $category,
        ?int $month = null
    ): array {
        // Implementation
    }
}
```

### 5.2 Value Objects for Type Safety

```php
<?php
declare(strict_types=1);

namespace TD\ValueObject;

/**
 * Represents a validated language code (ISO 639)
 */
final readonly class LanguageCode
{
    private const VALID_PATTERN = '/^[a-z]{2,3}(-[a-z]{2,4})?$/i';

    private string $code;

    private function __construct(string $code)
    {
        $this->code = strtolower($code);
    }

    /**
     * Create from string with validation
     *
     * @throws InvalidArgumentException When code format is invalid
     */
    public static function fromString(string $code): self
    {
        if (!preg_match(self::VALID_PATTERN, $code)) {
            throw new \InvalidArgumentException(
                sprintf('Invalid language code format: %s', $code)
            );
        }
        return new self($code);
    }

    /**
     * Create without validation (for trusted data only)
     */
    public static function fromTrusted(string $code): self
    {
        return new self($code);
    }

    public function toString(): string
    {
        return $this->code;
    }

    public function equals(self $other): bool
    {
        return $this->code === $other->code;
    }
}

/**
 * Represents filter options for queries
 */
final readonly class FilterOptions
{
    /**
     * @param int<1970,2100>|null $year
     * @param int<1,12>|null $month
     */
    public function __construct(
        public ?int $year = null,
        public ?int $month = null,
        public ?string $userGroup = null,
        public ?string $category = null,
        public ?LanguageCode $language = null,
    ) {}

    public function hasDateFilter(): bool
    {
        return $this->year !== null || $this->month !== null;
    }
}
```

### 5.3 Generic Repository Interface

```php
<?php
declare(strict_types=1);

namespace TD\Repository;

use TD\Entity\EntityInterface;

/**
 * Generic repository interface for entity persistence
 *
 * @template T of EntityInterface
 */
interface RepositoryInterface
{
    /**
     * Find entity by ID
     *
     * @param int<1, max> $id
     * @return T|null
     */
    public function find(int $id): ?object;

    /**
     * Find all entities matching criteria
     *
     * @param array<string, mixed> $criteria
     * @return array<T>
     */
    public function findBy(array $criteria): array;

    /**
     * Save entity (insert or update)
     *
     * @param T $entity
     * @return T The saved entity with ID populated
     */
    public function save(object $entity): object;

    /**
     * Delete entity
     *
     * @param T $entity
     */
    public function delete(object $entity): void;
}
```

---

## 6. Documentation Standards

### 6.1 File-Level Headers

Every PHP file should include a file-level docblock:

```php
<?php
/**
 * Translation Dashboard - Leaderboard Service
 *
 * Provides statistical queries for the translation leaderboard.
 * Supports filtering by user, language, category, and time period.
 *
 * @package    TranslationDashboard
 * @subpackage Backend\ApiOrSql
 * @author     Ibrahem <ibrahem.al-radaei@outlook.com>
 * @copyright  2024 WikiProjectMed
 * @license    GPL-2.0-or-later
 * @link       https://github.com/MrIbrahem/Translation-Dashboard
 *
 * @phpstan-import-type TranslatorStatsArray from TranslatorStats
 */

declare(strict_types=1);

namespace TD\Backend\ApiOrSql;
```

### 6.2 Class Documentation

```php
/**
 * Service for aggregating translation statistics
 *
 * This service provides methods to retrieve and aggregate translation
 * statistics from multiple data sources. It implements caching to
 * reduce database load for frequently accessed data.
 *
 * ## Usage Example
 *
 * ```php
 * $service = new LeaderboardService($connection, 3600);
 * $topUsers = $service->getTopTranslators(
 *     new FilterOptions(year: 2024, category: 'RTT')
 * );
 * ```
 *
 * ## Caching Strategy
 *
 * Results are cached for 1 hour by default. The cache is automatically
 * invalidated when:
 * - A new translation is recorded
 * - A user's group membership changes
 * - Manual invalidation is requested
 *
 * @see FilterOptions For available query filters
 * @see TranslatorStats For result structure
 * @see https://www.mediawiki.org/wiki/Content_translation
 */
final class LeaderboardService
{
    // ...
}
```

### 6.3 Method Documentation

```php
/**
 * Retrieve top translators with aggregated statistics
 *
 * Executes a parameterized query to fetch translators ranked by
 * translation count, word count, and view statistics. Results
 * are aggregated per user.
 *
 * ## Query Optimization
 *
 * Uses LEFT JOIN for optional relationships and indexes on:
 * - pages.user (for user filtering)
 * - pages.pupdate (for date filtering)
 * - pages.cat (for category filtering)
 *
 * ## Return Structure
 *
 * Each element contains:
 * - `user`: Translator username
 * - `targets`: Number of completed translations
 * - `words`: Total word count translated
 * - `views`: Aggregate page views for translations
 *
 * @param FilterOptions $options Filter criteria for the query
 *
 * @return array<TranslatorStats> Associative array keyed by username
 *
 * @throws DatabaseException When the underlying query fails
 * @throws InvalidArgumentException When filter values are invalid
 *
 * @example
 * ```php
 * $options = new FilterOptions(year: 2024);
 * $translators = $service->getTopTranslators($options);
 *
 * foreach ($translators as $username => $stats) {
 *     echo "{$stats->user}: {$stats->targets} translations\n";
 * }
 * ```
 */
public function getTopTranslators(FilterOptions $options): array
{
    // Implementation
}
```

### 6.4 PHPStan Type Aliases

Create `phpstan.neon` with type aliases:

```neon
parameters:
    level: max

    paths:
        - src

    treatPhpDocTypesAsAssert: true

    typeAliases:
        # Entity types
        PageArray: array{id: int, title: string, target: string, lang: string, user: string, word: int|null, translate_type: string, cat: string, date: string, pupdate: string, deleted: int, views: int|null}
        UserArray: array{id: int, username: string, user_group: string|null, active: int}
        LanguageArray: array{code: string, autonym: string, english_name: string}

        # Stats types
        TranslatorStatsArray: array{user: string, targets: int, words: int, views: int}
        LanguageStatsArray: array{lang: string, targets: int, words: int, views: int}

        # Request types
        RequestOptions: array{cat: string, camp: string, code: string, tra_type: string, doit: bool}

    universalObjectCratesClasses:
        - stdClass
```

---

## 7. Refactored Code Examples

### 7.1 Database Class Refactor

```php
<?php
/**
 * Database connection and query execution
 *
 * @package TranslationDashboard
 * @subpackage Backend\ApiCalls
 */

declare(strict_types=1);

namespace TD\Database;

use PDO;
use PDOException;
use PDOStatement;
use TD\Exception\DatabaseException;

/**
 * Manages database connections with automatic credential handling
 *
 * Creates PDO connections with proper error handling and security
 * configurations. Supports multiple database suffixes for different
 * data storage needs.
 */
final class Connection implements ConnectionInterface
{
    private readonly PDO $pdo;
    private bool $groupByModeDisabled = false;

    /**
     * @param string $serverName Server hostname for environment detection
     * @param non-empty-string $dbSuffix Database suffix (default: 'mdwiki')
     *
     * @throws DatabaseException When connection fails
     */
    public function __construct(
        string $serverName,
        private readonly string $dbSuffix = 'mdwiki'
    ) {
        $this->pdo = $this->createConnection($serverName);
    }

    /**
     * Execute a SELECT query and return all results
     *
     * @template T of array
     * @param non-empty-string $sql SQL query with placeholders
     * @param array<int, mixed> $params Parameters to bind
     * @return array<T> Array of associative arrays
     * @throws DatabaseException When query execution fails
     */
    public function fetchAll(string $sql, array $params = []): array
    {
        $this->configureGroupByMode($sql);

        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);

            /** @var array<T> $result */
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

            return $result;
        } catch (PDOException $e) {
            throw DatabaseException::queryFailed($sql, $e);
        }
    }

    /**
     * Execute an INSERT, UPDATE, or DELETE query
     *
     * @param non-empty-string $sql SQL query with placeholders
     * @param array<int, mixed> $params Parameters to bind
     * @return int Number of affected rows
     * @throws DatabaseException When query execution fails
     */
    public function execute(string $sql, array $params = []): int
    {
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);

            return $stmt->rowCount();
        } catch (PDOException $e) {
            throw DatabaseException::queryFailed($sql, $e);
        }
    }

    /**
     * Get the underlying PDO instance for advanced operations
     */
    public function getPdo(): PDO
    {
        return $this->pdo;
    }

    /**
     * Create PDO connection with proper configuration
     *
     * @throws DatabaseException When connection fails
     */
    private function createConnection(string $serverName): PDO
    {
        $config = $this->loadConfig($serverName);

        try {
            $dsn = sprintf(
                'mysql:host=%s;dbname=%s;charset=utf8mb4',
                $config['host'],
                $config['database']
            );

            $pdo = new PDO($dsn, $config['user'], $config['password'], [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4",
            ]);

            return $pdo;
        } catch (PDOException $e) {
            throw DatabaseException::connectionFailed($config['host']);
        }
    }

    /**
     * Load database configuration based on environment
     *
     * @return array{host: string, database: string, user: string, password: string}
     */
    private function loadConfig(string $serverName): array
    {
        $homeDir = getenv('HOME') ?: 'I:/mdwiki/mdwiki';
        $configFile = $homeDir . '/confs/db.ini';

        if (!file_exists($configFile)) {
            throw new \RuntimeException('Database configuration file not found');
        }

        $iniConfig = parse_ini_file($configFile);
        if ($iniConfig === false) {
            throw new \RuntimeException('Failed to parse database configuration');
        }

        if ($serverName === 'localhost') {
            return [
                'host' => getenv('DB_HOST') ?: 'localhost:3306',
                'database' => ($iniConfig['user'] ?? 'root') . '__' . $this->dbSuffix,
                'user' => getenv('DB_USER') ?: throw new \RuntimeException('DB_USER not set'),
                'password' => getenv('DB_PASSWORD') ?: throw new \RuntimeException('DB_PASSWORD not set'),
            ];
        }

        return [
            'host' => 'tools.db.svc.wikimedia.cloud',
            'database' => ($iniConfig['user'] ?? '') . '__' . $this->dbSuffix,
            'user' => $iniConfig['user'] ?? '',
            'password' => $iniConfig['password'] ?? '',
        ];
    }

    /**
     * Disable ONLY_FULL_GROUP_BY for legacy queries
     */
    private function configureGroupByMode(string $sql): void
    {
        if (
            !$this->groupByModeDisabled
            && stripos($sql, 'GROUP BY') !== false
        ) {
            try {
                $this->pdo->exec(
                    "SET SESSION sql_mode=(SELECT REPLACE(@@SESSION.sql_mode,'ONLY_FULL_GROUP_BY',''))"
                );
                $this->groupByModeDisabled = true;
            } catch (PDOException) {
                // Silently ignore - query will still work or fail appropriately
            }
        }
    }
}
```

### 7.2 Funcs.php Refactor

```php
<?php
/**
 * Data retrieval functions for translation statistics
 *
 * @package TranslationDashboard
 * @subpackage Backend\ApiOrSql
 */

declare(strict_types=1);

namespace TD\Repository;

use TD\Database\ConnectionInterface;
use TD\Cache\CacheInterface;
use TD\Entity\{Page, Coordinator};
use TD\ValueObject\LanguageCode;

/**
 * Repository for page-related queries
 */
final class PageRepository
{
    private const CACHE_PREFIX = 'page_repo_';

    public function __construct(
        private readonly ConnectionInterface $connection,
        private readonly CacheInterface $cache
    ) {}

    /**
     * Get pages by category and language
     *
     * @return array<Page>
     */
    public function findByCategoryAndLanguage(
        string $category,
        LanguageCode $language
    ): array {
        $cacheKey = self::CACHE_PREFIX . "cat_lang_{$category}_{$language->toString()}";

        return $this->cache->remember(
            $cacheKey,
            fn() => $this->fetchByCategoryAndLanguage($category, $language),
            ttl: 3600
        );
    }

    /**
     * @return array<Page>
     */
    private function fetchByCategoryAndLanguage(
        string $category,
        LanguageCode $language
    ): array {
        $sql = <<<SQL
            SELECT *
            FROM pages p
            WHERE p.lang = ? AND p.cat = ?
        SQL;

        $results = $this->connection->fetchAll($sql, [
            $language->toString(),
            $category
        ]);

        return array_map(
            fn(array $row) => Page::fromArray($row),
            $results
        );
    }

    /**
     * Get pages by user with optional filters
     *
     * @param string $user Username
     * @param int|null $year Filter by year
     * @param LanguageCode|null $language Filter by language
     * @return array<Page>
     */
    public function findByUser(
        string $user,
        ?int $year = null,
        ?LanguageCode $language = null
    ): array {
        $cacheKey = self::CACHE_PREFIX . sprintf(
            'user_%s_%s_%s',
            md5($user),
            $year ?? 'all',
            $language?->toString() ?? 'all'
        );

        return $this->cache->remember(
            $cacheKey,
            fn() => $this->fetchByUser($user, $year, $language),
            ttl: 3600
        );
    }

    /**
     * @return array<Page>
     */
    private function fetchByUser(
        string $user,
        ?int $year,
        ?LanguageCode $language
    ): array {
        $sql = <<<SQL
            SELECT DISTINCT
                p.title, p.word, p.translate_type, p.cat, p.lang,
                p.user, p.target, p.date, p.pupdate, p.add_date,
                p.deleted, v.views
            FROM pages p
            LEFT JOIN views_new_all v
                ON p.target = v.target AND p.lang = v.lang
            WHERE p.user = ?
        SQL;

        $params = [$user];

        if ($year !== null) {
            $sql .= ' AND YEAR(p.date) = ?';
            $params[] = $year;
        }

        if ($language !== null) {
            $sql .= ' AND p.lang = ?';
            $params[] = $language->toString();
        }

        $results = $this->connection->fetchAll($sql, $params);

        return array_map(
            fn(array $row) => Page::fromArray($row),
            $results
        );
    }

    /**
     * Get distinct years with translations for a user
     *
     * @return array<int>
     */
    public function getYearsForUser(string $user): array
    {
        $cacheKey = self::CACHE_PREFIX . "user_years_{$user}";

        return $this->cache->remember(
            $cacheKey,
            function () use ($user): array {
                $sql = <<<SQL
                    SELECT DISTINCT YEAR(p.date) AS year
                    FROM pages p
                    WHERE p.user = ?
                    ORDER BY year DESC
                SQL;

                $results = $this->connection->fetchAll($sql, [$user]);

                return array_filter(
                    array_map('current', $results),
                    fn($year) => !empty($year)
                );
            },
            ttl: 3600
        );
    }
}
```

---

## 8. Implementation Roadmap

### Phase 1: Critical Security Fixes (Week 1-2)

| Task | Priority | Effort | Files Affected |
|------|----------|--------|----------------|
| Remove hardcoded credentials | P0 | 2h | `mdwiki_sql.php` |
| Implement CSRF tokens | P0 | 4h | All form files |
| Fix XSS vulnerabilities | P0 | 8h | `html.php`, `forms.php`, `header.php` |
| Remove debug backdoor | P0 | 2h | 12 files |
| Add output escaping helper | P0 | 2h | New file |

### Phase 2: Type Safety (Week 3-4)

| Task | Priority | Effort | Files Affected |
|------|----------|--------|----------------|
| Add strict_types to all files | P1 | 4h | All PHP files |
| Create Value Objects | P1 | 8h | New files |
| Add PHPStan configuration | P1 | 2h | New file |
| Add type annotations | P1 | 16h | All PHP files |

### Phase 3: Documentation (Week 5-6)

| Task | Priority | Effort | Files Affected |
|------|----------|--------|----------------|
| Add file-level headers | P2 | 4h | All PHP files |
| Document all public methods | P2 | 16h | All PHP files |
| Create architecture docs | P2 | 4h | New file |

### Phase 4: Architecture Improvements (Week 7-10)

| Task | Priority | Effort | Files Affected |
|------|----------|--------|----------------|
| Implement proper caching | P2 | 8h | New files |
| Create Repository classes | P2 | 16h | New files |
| Implement dependency injection | P2 | 8h | Entry points |
| Standardize error handling | P2 | 8h | All files |

---

## Summary of Critical Issues

### Immediate Action Required

1. **Hardcoded Password**: `'root11'` in `mdwiki_sql.php:54` - Remove immediately
2. **Debug Mode Backdoor**: `?test=1` exposes errors - Replace with proper auth
3. **No CSRF Protection**: All forms vulnerable - Add tokens to all forms
4. **XSS in Output**: Multiple unescaped echoes - Add escaping helper

### High Priority

5. **Dynamic SQL Columns**: Not validated against whitelist
6. **Global State**: Replace `$GLOBALS` with dependency injection
7. **Error Messages**: Expose SQL queries and file paths

### Medium Priority

8. **Missing Type Declarations**: 95%+ of code lacks types
9. **Missing Documentation**: 90%+ of code undocumented
10. **Inconsistent Error Handling**: Mix of echo, return false, exceptions

---

**Report Generated**: 2026-02-14
**Recommended PHPStan Level**: 8 (max)
**Target PHP Version**: 8.2+
