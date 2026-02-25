# Static Analysis Report: WikiProjectMed Translation Dashboard

**Date**: 2026-01-26
**Analyst**: Senior Software Architecture Review
**Repository**: Translation_Dashboard (PHP 8.2+)
**Files Analyzed**: 65+ PHP source files

---

## Executive Summary

This report provides a comprehensive static analysis of the WikiProjectMed Translation Dashboard codebase. The analysis reveals significant architectural debt, security vulnerabilities, and maintainability concerns that require immediate attention.

**Key Findings**:
- **Security**: CRITICAL - Hard-coded credentials in database class (mdwiki_sql.php:54)
- **Architecture**: Mixed paradigms (procedural + OOP) causing cognitive load
- **Coupling**: Tight coupling through global state and static properties
- **Testability**: POOR - Global state and static methods prevent isolated testing
- **Code Duplication**: Multiple implementations of same functionality

**Recommended Approach**: Incremental refactoring prioritizing security → architecture → quality.

---

## 1. System Overview: Current Architecture

### 1.1 Technology Stack

```
Language:       PHP 8.2+
Database:       MySQL/MariaDB via PDO
External APIs:  MediaWiki API, Wikidata SPARQL
Frontend:       Vanilla JavaScript + Bootstrap 5
Testing:        PHPUnit (minimal coverage)
Dependency Mgmt: Composer
```

### 1.2 Directory Structure Analysis

```
src/
├── backend/
│   ├── api_calls/         # External API integration (2 files)
│   ├── api_or_sql/        # Hybrid API/SQL abstraction layer (7 files)
│   ├── include_first/     # Global initialization (3 files)
│   ├── loaders/           # Request loading (1 file)
│   ├── others/            # Miscellaneous (1 file)
│   ├── results/           # Results processing (7 files)
│   ├── tables/            # Data table management (3 files)
│   ├── td_api_wrap/       # Translation Dashboard API wrapper
│   └── userinfos_wrap.php # User authentication wrapper
├── css/                   # Stylesheets
├── frontend/              # Presentation layer (4 directories)
├── js/                    # JavaScript files (13 files)
├── leaderboard/           # Statistics/leaderboard module (15 files)
├── results/               # Results display (5 files)
├── translate/             # Translation routing
├── translate_med/         # Medical translation handling
├── td_tests/              # Unit tests (5 test files)
├── 404.php
├── auth.php
├── coordinator.php
├── footer.php
├── head.php
├── header.php
├── include_all.php        # Global file loader
├── index.php              # Main entry point
├── leaderboard.php        # Leaderboard entry point
├── missing.php
├── results.php
├── sitelinks.php
├── t.php
├── tools.php
└── translate.php
```

### 1.3 Architecture Pattern Assessment

**Current Pattern**: Modified MVC with "API-or-SQL" hybrid abstraction

```
Request → index.php → load_request() → results_loader()
                                  ↓
                         [SQL/API Hybrid Layer]
                                  ↓
                    ┌─────────────┴─────────────┐
                    ↓                           ↓
            MediaWiki API                  MySQL Database
```

**Architectural Style**: Procedural with namespaced functions and static class properties

**Key Architectural Decisions** (as implemented):
1. Dual data source abstraction (API vs SQL) - `super_function()`
2. Static property caching within functions
3. Global state for user context (`$GLOBALS['global_username']`)
4. Include-based autoloading pattern

---

## 2. Code Smells and Anti-Patterns

### 2.1 Global State Anti-Pattern

**Severity**: HIGH
**Impact**: Testability, thread safety, implicit dependencies

#### Example 1: Global User State
**File**: `src/backend/userinfos_wrap.php:11-14`
```php
if (!empty($GLOBALS['global_username'] ?? "")) {
    $global_username = $GLOBALS['global_username'];
} else {
    $GLOBALS['global_username'] = '';
}
```
**Problem**: User state stored in globals, impossible to test in isolation.

#### Example 2: Global Configuration Switch
**File**: `src/backend/api_or_sql/index.php:18-24`
```php
$settings_tabe = array_column(get_td_api(['get' => 'settings']), 'value', 'title');
$use_td_api  = (($settings_tabe['use_td_api'] ?? "") == "1") ? true : false;
// ...
function super_function(array $api_params, array $sql_params, string $sql_query, $table_name = null, $no_refind = false): array
{
    global $use_td_api;  // ← GLOBAL FUNCTION VARIABLE
    $data = ($use_td_api) ? get_td_api($api_params) : [];
```
**Problem**: Behavior changes based on global variable, unpredictable.

#### Example 3: Coordinator Flag in Global and Constant
**File**: `src/header.php:26-30`
```php
$user_in_coord = false;
if (($coordinators[$GLOBALS['global_username']] ?? 0) == 1) {
    $user_in_coord = true;
};
$GLOBALS['user_in_coord'] = $user_in_coord;
define('user_in_coord', $user_in_coord);  // ← CONSTANT FROM RUNTIME VALUE
```
**Problem**: Runtime value converted to constant, can't be tested.

**Refactoring Approach**:
```php
// Instead of globals, use a Request/Session service:
class UserContext {
    private string $username;
    private bool $isCoordinator;

    public function __construct(SessionInterface $session, CoordinatorRepository $repo) {
        $this->username = $session->get('username', '');
        $this->isCoordinator = $repo->isActiveCoordinator($this->username);
    }

    public function getUsername(): string { return $this->username; }
    public function isCoordinator(): bool { return $this->isCoordinator; }
}
```

### 2.2 Static Properties as Global Cache

**Severity**: MEDIUM
**Impact**: Memory leaks, state pollution, testing issues

#### Example 1: MainTables Static Arrays
**File**: `src/backend/tables/tables.php:25-33`
```php
class MainTables
{
    public static $x_enwiki_pageviews_table = [];
    public static $x_Words_table = [];
    public static $x_All_Words_table = [];
    public static $x_All_Refs_table = [];
    public static $x_Lead_Refs_table = [];
    public static $x_Assessments_table = [];
    public static $x_Langs_table = [];
}
```
**Problem**: These are populated at request time and persist, acting as global mutable state.

#### Example 2: TablesSql Static Configuration
**File**: `src/backend/tables/sql_tables.php:22-41`
```php
class TablesSql
{
    public static $s_full_translates = [];
    public static $s_no_lead_translates = [];
    public static $s_cat_titles = [];
    public static $s_cat_to_camp = [];
    public static $s_camp_to_cat = [];
    public static $s_main_cat = '';
    public static $s_main_camp = '';
    // ... 8 more static properties
}
```
**Problem**: Configuration mixed with data, all static, no encapsulation.

#### Example 3: Function-Level Static Caching
**File**: `src/backend/api_or_sql/funcs.php:28-36`
```php
function get_coordinator()
{
    static $coordinator = [];  // ← STATIC IN FUNCTION
    if (!empty($coordinator ?? [])) {
        return $coordinator;
    }
    // ... fetch and populate
}
```
**Problem**: Same pattern repeated in 15+ functions, no cache invalidation.

**Refactoring Approach**:
```php
// Use a proper caching service:
class CacheService {
    private array $cache = [];

    public function get(string $key, callable $callback) {
        if (!isset($this->cache[$key])) {
            $this->cache[$key] = $callback();
        }
        return $this->cache[$key];
    }

    public function clear(): void {
        $this->cache = [];
    }
}

// Usage:
$coordinators = $cache->get('coordinators', fn() => $repo->getAll());
```

### 2.3 Primitive Obsession

**Severity**: MEDIUM
**Impact**: Type safety, domain modeling

#### Example: Untyped Array Returns
**File**: `src/backend/api_or_sql/funcs.php:50-66`
```php
function get_coordinator()
{
    // ...
    $u_data = super_function($api_params, [], $query);
    $coordinator = $u_data;
    return $u_data;  // ← Returns array, no type safety
}
```
**Problem**: Caller doesn't know structure of returned data.

**Refactoring Approach**:
```php
// Define value objects:
class Coordinator {
    public function __construct(
        public readonly int $id,
        public readonly string $user,
        public readonly bool $active
    ) {}
}

interface CoordinatorRepository {
    /** @return array<string, Coordinator> */
    public function getAllIndexed(): array;
}
```

### 2.4 Shotgun Surgery

**Severity**: MEDIUM
**Impact**: Maintenance burden

#### Example: Database Connection Creation Scattered
Multiple locations create database connections:
- `src/backend/api_calls/mdwiki_sql.php:192-215` (`execute_query()`)
- `src/backend/api_calls/mdwiki_sql.php:217-239` (`fetch_query()`)

Both functions duplicate this pattern:
```php
$dbname = get_dbname($table_name);
$db = new Database($_SERVER['SERVER_NAME'] ?? 'localhost', $dbname);
// ... use db once
$db = null;  // ← Wasteful
```

**Problem**: Every query creates new connection; connection settings scattered.

### 2.5 Magic Numbers and Strings

**Severity**: LOW
**Impact**: Maintainability

#### Example 1: Hard-coded Drive Letter
**File**: `src/backend/userinfos_wrap.php:5-9`
```php
if (substr(__DIR__, 0, 2) == 'I:') {
    include_once 'I:/mdwiki/auth_repo/src/oauth/user_infos.php';
} else {
    include_once __DIR__ . '/../../auth/oauth/user_infos.php';
}
```
**Problem**: Hard-coded Windows drive path, not portable.

#### Example 2: Magic Cookie Values
**File**: `src/backend/api_calls/mdwiki_sql.php:78`
```php
if (isset($_COOKIE['test']) && $_COOKIE['test'] == 'x') {
    return;
}
```
**Problem**: What does 'x' mean? No constant definition.

#### Example 3: Direct String Comparison
**File**: `src/backend/results/results_table.php:103`
```php
if (strtolower(substr($title, 0, 6)) == 'video:') {
    $tra_type = 'all';
};
```
**Problem**: Magic string 'video:', no constant.

### 2.6 Feature Envy

**Severity**: LOW
**Impact**: Encapsulation violation

#### Example: Data Access in Presentation
**File**: `src/backend/tables/tables.php:47-61`
```php
$titles_infos = get_td_or_sql_titles_infos();  // ← Data fetch in file init

foreach ($titles_infos as $k => $tab) {
    $title = $tab['title'];
    MainTables::$x_enwiki_pageviews_table[$title] = $tab['en_views'];
    MainTables::$x_Words_table[$title] = $tab['w_lead_words'];
    // ... more assignments
}
```
**Problem**: File-level code populating static tables; should be in repository.

### 2.7 Long Parameter List

**Severity**: LOW
**Impact**: Readability

#### Example: Results Table Function
**File**: `src/results/results.php:45-111`
```php
function Results_tables($tab, $show_exists, $translation_button, $full_tr_user)
{
    $camp       = $tab["camp"];
    $code       = $tab["code"];
    $cat        = $tab["cat"];
    $tra_type   = $tab["tra_type"];
    $test       = $tab["test"];
    $code_lang_name  = $tab["code_lang_name"];
    $global_username = $tab["global_username"];
    $user_coord      = $tab["user_coord"];
    $mobile_td       = $tab["mobile_td"];
    // ... unpacks from associative array anyway
}
```
**Problem**: Function takes array but immediately unpacks it; confusing interface.

### 2.8 Duplicated Code

**Severity**: MEDIUM
**Impact**: Maintenance, bug fixes

#### Example 1: Two Get Results Implementations
- `src/backend/results/get_titles/get_results.php`
- `src/backend/results/new_way/get_results.php`

Both implement similar functionality with different approaches.

#### Example 2: Database Query Functions
**File**: `src/backend/api_calls/mdwiki_sql.php`
```php
public function executequery($sql_query, $params = null) { ... }
public function fetchquery($sql_query, $params = null) { ... }
```
**Problem**: Nearly identical with minor differences; violation of DRY.

---

## 3. Dependency Issues and Coupling Map

### 3.1 Coupling Analysis

```
┌─────────────────────────────────────────────────────────────────────┐
│                    COUPLING DEPENDENCY GRAPH                        │
├─────────────────────────────────────────────────────────────────────┤
│                                                                     │
│  ┌─────────────┐                                                    │
│  │  index.php  │                                                    │
│  └──────┬──────┘                                                    │
│         │                                                           │
│         ├──────────────────────────────────────────┐                │
│         ↓                                          ↓                │
│  ┌──────────────┐                         ┌──────────────┐         │
│  │ load_request │                         │ header.php   │         │
│  └──────┬───────┘                         └──────┬───────┘         │
│         │                                         │                 │
│         ↓                                         ↓                 │
│  ┌─────────────────────────────────────────────────────┐           │
│  │           TablesSql (Static Properties)              │           │
│  │  - $s_settings, $s_camp_to_cat, $s_main_cat, ...    │           │
│  └──────────────────────┬──────────────────────────────┘           │
│                         │                                          │
│         ┌───────────────┼───────────────┐                         │
│         ↓               ↓               ↓                         │
│  ┌─────────────┐ ┌─────────────┐ ┌─────────────┐                   │
│  │MainTables   │ │  results_   │ │  leaderboard│                   │
│  │(Static)     │ │  loader()   │ │  modules    │                   │
│  └─────────────┘ └──────┬──────┘ └─────────────┘                   │
│                        │                                          │
│                        ↓                                          │
│         ┌──────────────────────────────┐                           │
│         │    super_function()          │                           │
│         │  (Global: $use_td_api)       │                           │
│         └──────────────┬───────────────┘                           │
│                        │                                          │
│            ┌───────────┴───────────┐                              │
│            ↓                       ↓                              │
│     ┌─────────────┐         ┌─────────────┐                       │
│     │ get_td_api()│         │fetch_query()│                       │
│     └─────────────┘         └──────┬──────┘                       │
│                                     │                              │
│                                     ↓                              │
│                            ┌───────────────┐                       │
│                            │  new Database │                       │
│                            │  (Every Call) │                       │
│                            └───────────────┘                       │
│                                                                     │
└─────────────────────────────────────────────────────────────────────┘
```

### 3.2 Critical Coupling Issues

#### Issue 1: Tight Coupling to Globals
**Affected Files**: 25+ files depend on `$GLOBALS['global_username']`

**Dependency Chain**:
```
index.php → header.php → (sets $GLOBALS['global_username'])
                              ↓
                    results_loader() uses $GLOBALS
                              ↓
                    make_results_table() uses $GLOBALS
```

**Problem**: Cannot run any component without global state initialization.

#### Issue 2: Static Property Dependencies
**File**: `src/backend/tables/sql_tables.php`

**Dependency Chain**:
```
Any file → TablesSql::$s_settings
         → TablesSql::$s_camp_to_cat
         → TablesSql::$s_campaign_input_list
```

**Problem**: Order-dependent initialization; tests fail if TablesSql not loaded first.

#### Issue 3: Include Chain Dependencies
**File**: `src/include_all.php:8-38`

```php
include_once __DIR__ . '/frontend/include.php';
include_once __DIR__ . '/backend/include_first/include.php';

foreach (glob(__DIR__ . "/backend/api_calls/*.php") as $filename) {
    include_once $filename;
}
foreach (glob(__DIR__ . "/backend/api_or_sql/*.php") as $filename) {
    include_once $filename;
}
// ... more includes
```

**Problem**: Must load in exact order; circular dependencies possible.

### 3.3 Dependency Violations

#### Violation 1: Dependency Inversion Principle
High-level modules depend on low-level modules directly.

**Example**:
```php
// High-level business logic directly calls low-level API:
function results_loader($data) {
    $results_list = get_results_new($cat, $camp, $depth, $code, ...);
    // get_results_new() → super_function() → fetch_query() → new Database
}
```

**Should be**:
```php
interface ResultsRepository {
    public function getResults(Criteria $criteria): ResultsCollection;
}
```

#### Violation 2: Interface Segregation
No interfaces defined; all dependencies are concrete implementations.

#### Violation 3: Single Responsibility
Many classes/functions do multiple things:
- `Database::test_print()` - prints debug AND is a database method
- `MainTables` class - holds data AND acts as cache

### 3.4 External Dependency Map

```
┌────────────────────────────────────────────────────────────────────┐
│                    EXTERNAL DEPENDENCIES                           │
├────────────────────────────────────────────────────────────────────┤
│                                                                    │
│  ┌────────────┐     ┌────────────┐     ┌────────────┐            │
│  │ MediaWiki  │     │  Wikidata  │     │  Pageviews │            │
│  │    API     │     │  SPARQL    │     │    API     │            │
│  └─────┬──────┘     └─────┬──────┘     └─────┬──────┘            │
│        │                  │                  │                    │
│        └──────────────────┼──────────────────┘                    │
│                           ↓                                       │
│                    ┌────────────┐                                 │
│                    │wiki_api.php│                                 │
│                    └────────────┘                                 │
│                           │                                       │
│                           ↓                                       │
│                    ┌────────────┐                                 │
│                    │sparql_bot  │                                 │
│                    │  .php      │                                 │
│                    └────────────┘                                 │
│                                                                    │
│  ┌────────────┐     ┌────────────┐     ┌────────────┐            │
│  │ Translation│     │  Leaderboard│     │  Results   │            │
│  │Dashboard   │     │    API     │     │   Service  │            │
│  │    API     │     │            │     │            │            │
│  └─────┬──────┘     └─────┬──────┘     └─────┬──────┘            │
│        │                  │                  │                    │
│        └──────────────────┼──────────────────┘                    │
│                           ↓                                       │
│                    ┌────────────┐                                 │
│                    │ td_api.php │                                 │
│                    └────────────┘                                 │
│                                                                    │
└────────────────────────────────────────────────────────────────────┘
```

### 3.5 Circular Dependency Risk

**Potential Circular Dependency**:
```
include_all.php → backend/include_first/include.php
                           ↓
                  TablesSql (loads settings)
                           ↓
                  get_td_or_sql_settings()
                           ↓
                  super_function()
                           ↓
                  fetch_query()
                           ↓
                  new Database()
                           ↓
                  set_db() uses parse_ini_file()
```

**Risk**: If `parse_ini_file()` fails, entire application crashes.

---

## 4. Refactoring Roadmap

### Phase 1: Security & Critical Infrastructure (Week 1-2)

**Priority**: CRITICAL
**Risk**: HIGH (security vulnerabilities)

#### 1.1 Remove Hard-Coded Credentials
**Files**: `src/backend/api_calls/mdwiki_sql.php:50-60`

**Current**:
```php
if ($server_name === 'localhost') {
    $this->host = 'localhost:3306';
    $this->dbname = $ts_mycnf['user'] . "__" . $this->db_suffix;
    $this->user = 'root';
    $this->password = '***REDACTED***';  // ← HARDCODED
}
```

**Target**:
```php
// .env file
DB_HOST=localhost:3306
DB_NAME=mdwiki
DB_USER=root
DB_PASS=${DB_PASSWORD}

// Database.php
class Database {
    public function __construct(
        private readonly string $host,
        private readonly string $dbname,
        private readonly string $user,
        private readonly string $password,
        private readonly PDOFactory $pdoFactory
    ) {}
}
```

#### 1.2 Implement Request Validation Layer
**Files**: All entry points (index.php, leaderboard.php, translate.php)

**Current**: Direct `$_GET`, `$_POST`, `$_REQUEST` usage

**Target**:
```php
// src/Http/Request.php
final class Request {
    public function __construct(private readonly array $get, private readonly array $post) {}

    public function getString(string $key, string $default = ''): string {
        $value = $this->get[$key] ?? $this->post[$key] ?? $default;
        return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
    }

    public function getBool(string $key): bool {
        return !empty($this->get[$key] ?? $this->post[$key]);
    }

    public function getEnum(string $key, array $allowed, string $default): string {
        $value = $this->getString($key, $default);
        return in_array($value, $allowed, true) ? $value : $default;
    }
}
```

#### 1.3 Create Configuration Service
**Files**: `src/backend/tables/sql_tables.php`, settings access throughout

**Target**:
```php
// src/Config/SettingsService.php
final class SettingsService {
    private array $cache = [];

    public function __construct(
        private readonly SettingsRepository $repo,
        private readonly CacheInterface $cache
    ) {}

    public function get(string $key, mixed $default = null): mixed {
        return $this->cache->get("setting.$key",
            fn() => $this->repo->getValue($key) ?? $default
        );
    }

    public function getBool(string $key): bool {
        return $this->get($key) === '1';
    }
}
```

### Phase 2: Architecture Refactoring (Week 3-4)

**Priority**: HIGH
**Risk**: MEDIUM (architectural changes)

#### 2.1 Implement Dependency Injection Container

**Target**:
```php
// src/Container/Container.php
final class Container {
    private array $services = [];
    private array $factories = [];

    public function register(string $id, callable $factory): void {
        $this->factories[$id] = $factory;
    }

    public function get(string $id): mixed {
        if (!isset($this->services[$id])) {
            if (!isset($this->factories[$id])) {
                throw new ServiceNotFoundException($id);
            }
            $this->services[$id] = ($this->factories[$id])($this);
        }
        return $this->services[$id];
    }
}

// Usage in bootstrap
$container = new Container();
$container->register(Database::class, fn() => new Database(
    getenv('DB_HOST'),
    getenv('DB_NAME'),
    getenv('DB_USER'),
    getenv('DB_PASS')
));
$container->register(SettingsService::class, fn($c) =>
    new SettingsService($c->get(SettingsRepository::class), $c->get(CacheInterface::class))
);
```

#### 2.2 Extract Repository Layer

**Target Structure**:
```
src/Repository/
├── CoordinatorRepository.php
├── CategoryRepository.php
├── PageRepository.php
├── TranslationRepository.php
└── SettingsRepository.php
```

**Example**:
```php
// src/Repository/PageRepository.php
interface PageRepository {
    /**
     * @return array<int, Page>
     */
    public function findByUserAndYear(string $user, ?string $year): array;

    /**
     * @return array<int, Page>
     */
    public function findByLangAndYear(string $lang, ?string $year): array;

    public function countByUser(string $user): int;
}

final class SqlPageRepository implements PageRepository {
    public function __construct(
        private readonly PDO $pdo
    ) {}

    public function findByUserAndYear(string $user, ?string $year): array {
        $sql = "SELECT * FROM pages WHERE user = :user";
        $params = ['user' => $user];

        if ($year !== null && $year !== 'all') {
            $sql .= " AND YEAR(date) = :year";
            $params['year'] = $year;
        }

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);

        return array_map(
            fn($row) => Page::fromArray($row),
            $stmt->fetchAll(PDO::FETCH_ASSOC)
        );
    }
}
```

#### 2.3 Create Service Layer

**Target Structure**:
```
src/Service/
├── ResultsService.php
├── LeaderboardService.php
├── TranslationService.php
└── CategoryService.php
```

**Example**:
```php
// src/Service/ResultsService.php
final class ResultsService {
    public function __construct(
        private readonly PageRepository $pageRepo,
        private readonly CategoryRepository $categoryRepo,
        private readonly WikidataApi $wikidata,
        private readonly CacheInterface $cache
    ) {}

    public function getMissingArticles(
        string $category,
        string $targetLang,
        string $campaign,
        bool $filterSparql
    ): ResultsCollection {
        $cacheKey = "missing.$category.$targetLang.$campaign.$filterSparql";

        return $this->cache->get($cacheKey, function() use ($category, $targetLang, $campaign, $filterSparql) {
            $existing = $this->pageRepo->findByCategoryAndLang($category, $targetLang);
            $allArticles = $this->wikidata->getCategoryMembers($category);

            if ($filterSparql) {
                $allArticles = $this->wikidata->filterExistingArticles($allArticles, $targetLang);
            }

            $missing = array_diff($allArticles, array_column($existing, 'title'));
            $inProcess = $this->pageRepo->findInProcessByTitles($missing, $targetLang);

            return new ResultsCollection(
                inProcess: $inProcess,
                existing: $existing,
                missing: array_diff($missing, array_column($inProcess, 'title'))
            );
        });
    }
}
```

### Phase 3: Presentation Layer Refactoring (Week 5-6)

**Priority**: MEDIUM
**Risk**: MEDIUM (UI changes)

#### 3.1 Extract View Components

**Target Structure**:
```
src/View/
├── Component/
│   ├── Card.php
│   ├── Table.php
│   ├── Dropdown.php
│   └── Modal.php
├── Results/
│   ├── ResultsTableView.php
│   ├── MissingRowView.php
│   └── InProcessRowView.php
└── Leaderboard/
    ├── NumbersCard.php
    ├── UsersTable.php
    └── LanguagesTable.php
```

**Example**:
```php
// src/View/Results/ResultsTableView.php
final class ResultsTableView {
    public function __construct(
        private readonly UrlGenerator $urls,
        private readonly Translator $trans
    ) {}

    public function render(ResultsCollection $results, TableContext $context): string {
        $rows = array_map(
            fn($article) => $this->renderRow($article, $context),
            $results->missing
        );

        return $this->renderTemplate('results/table', [
            'rows' => implode("\n", $rows),
            'count' => count($results->missing),
            'context' => $context
        ]);
    }

    private function renderRow(Article $article, TableContext $context): string {
        return $this->renderTemplate('results/row', [
            'title' => $article->title,
            'url' => $this->urls->translate($article, $context),
            'views' => $article->views,
            // ...
        ]);
    }
}
```

#### 3.2 Create Controller Layer

**Target Structure**:
```
src/Controller/
├── IndexController.php
├── LeaderboardController.php
├── ResultsController.php
└── TranslationController.php
```

**Example**:
```php
// src/Controller/ResultsController.php
final class ResultsController {
    public function __construct(
        private readonly ResultsService $service,
        private readonly ResultsTableView $view,
        private readonly Request $request
    ) {}

    public function handle(): Response {
        $criteria = new SearchCriteria(
            category: $this->request->getEnum('cat', $this->getValidCategories(), 'RTT'),
            targetLang: $this->request->getString('code'),
            campaign: $this->request->getString('camp'),
            translationType: $this->request->getEnum('type', ['lead', 'all'], 'lead'),
            filterSparql: $this->request->getBool('filter_sparql_x')
        );

        $results = $this->service->getMissingArticles(
            $criteria->category,
            $criteria->targetLang,
            $criteria->campaign,
            $criteria->filterSparql
        );

        $context = new TableContext(
            user: $this->request->getUser(),
            isCoordinator: $this->request->isCoordinator(),
            mobile: $this->request->getString('mobile_td') === 'mobile'
        );

        $html = $this->view->render($results, $context);

        return new HtmlResponse($html);
    }
}
```

### Phase 4: Testing & Quality (Week 7-8)

**Priority**: MEDIUM
**Risk**: LOW (testing infrastructure)

#### 4.1 Establish Testing Infrastructure

**Target Structure**:
```
tests/
├── Unit/
│   ├── Service/
│   │   ├── ResultsServiceTest.php
│   │   └── LeaderboardServiceTest.php
│   ├── Repository/
│   │   └── PageRepositoryTest.php
│   └── View/
│       └── ResultsTableViewTest.php
├── Integration/
│   ├── Api/
│   │   └── WikidataApiTest.php
│   └── Database/
│       └── DatabaseFixtureTest.php
└── Fixture/
    ├── PageFixture.php
    └── CategoryFixture.php
```

**Example Test**:
```php
// tests/Unit/Service/ResultsServiceTest.php
final class ResultsServiceTest extends TestCase {
    private ResultsService $service;
    private PageRepository&MockObject $pageRepo;
    private CacheInterface&MockObject $cache;

    protected function setUp(): void {
        $this->pageRepo = $this->createMock(PageRepository::class);
        $this->cache = $this->createMock(CacheInterface::class);
        $this->service = new ResultsService(
            $this->pageRepo,
            $this->createMock(CategoryRepository::class),
            $this->createMock(WikidataApi::class),
            $this->cache
        );
    }

    public function testGetMissingArticlesReturnsCachedData(): void {
        $expected = new ResultsCollection([], [], []);
        $this->cache->expects($this->once())
            ->method('get')
            ->willReturn($expected);

        $result = $this->service->getMissingArticles('RTT', 'ar', 'Main', true);

        $this->assertEquals($expected, $result);
    }

    public function testGetMissingArticlesFiltersInProcess(): void {
        // Given
        $missing = ['Article A', 'Article B', 'Article C'];
        $inProcess = [
            new Article('Article A', 'ar', new \DateTime())
        ];

        $this->pageRepo->expects($this->once())
            ->method('findInProcessByTitles')
            ->with($missing, 'ar')
            ->willReturn($inProcess);

        // When
        $result = $this->service->getMissingArticles('RTT', 'ar', 'Main', false);

        // Then
        $this->assertNotContains('Article A', $result->missing);
        $this->assertContains('Article B', $result->missing);
        $this->assertContains('Article C', $result->missing);
    }
}
```

#### 4.2 Add Static Analysis

**composer.json additions**:
```json
{
    "require-dev": {
        "phpstan/phpstan": "^1.10",
        "slevomat/coding-standard": "^8.0",
        "phpunit/phpunit": "^11.0",
        "vimeo/psalm": "^5.0"
    },
    "scripts": {
        "analyze": "phpstan analyse src --level=8",
        "psalm": "psalm --show-info=true",
        "test": "phpunit",
        "ci": ["@analyze", "@psalm", "@test"]
    }
}
```

**phpstan.neon**:
```neon
parameters:
    level: 8
    paths:
        - src
    checkMissingIterableValueType: false
    checkGenericClassInNonGenericObjectType: false
```

### Phase 5: Performance & Polish (Week 9-10)

**Priority**: LOW
**Risk**: LOW

#### 5.1 Implement Connection Pooling

**Target**:
```php
// src/Database/ConnectionPool.php
final class ConnectionPool {
    private ?PDO $connection = null;

    public function getConnection(
        string $host,
        string $dbname,
        string $user,
        string $password
    ): PDO {
        if ($this->connection === null) {
            $this->connection = new PDO(
                "mysql:host=$host;dbname=$dbname",
                $user,
                $password,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_PERSISTENT => true,
                    PDO::MYSQL_ATTR_INIT_COMMAND => "SET time_zone = '+00:00'"
                ]
            );
        }
        return $this->connection;
    }
}
```

#### 5.2 Add Distributed Caching

**Target**:
```php
// src/Cache/RedisCache.php
final class RedisCache implements CacheInterface {
    public function __construct(
        private readonly \Redis $redis,
        private readonly int $ttl = 3600
    ) {}

    public function get(string $key, ?callable $callback = null): mixed {
        $value = $this->redis->get($key);

        if ($value === false && $callback !== null) {
            $value = $callback();
            $this->set($key, $value, $this->ttl);
        }

        return $value !== false ? unserialize($value) : null;
    }

    public function set(string $key, mixed $value, int $ttl): void {
        $this->redis->setex($key, $ttl, serialize($value));
    }

    public function clear(): void {
        $this->redis->flushDB();
    }

    public function delete(string $key): void {
        $this->redis->del($key);
    }
}
```

#### 5.3 Add Monitoring

**Target**:
```php
// src/Middleware/PerformanceMiddleware.php
final class PerformanceMiddleware {
    public function __construct(
        private readonly LoggerInterface $logger,
        private readonly MetricsCollector $metrics
    ) {}

    public function process(callable $next): mixed {
        $start = hrtime(true);

        try {
            $result = $next();
            return $result;
        } finally {
            $duration = (hrtime(true) - $start) / 1e9; // Convert to seconds

            $this->metrics->recordTiming('request_duration', $duration);

            if ($duration > 1.0) { // Log slow requests
                $this->logger->warning('Slow request', [
                    'duration' => $duration,
                    'url' => $_SERVER['REQUEST_URI'] ?? 'unknown'
                ]);
            }
        }
    }
}
```

---

## 5. Concrete Changes Per File/Module

### 5.1 Entry Points

#### `src/index.php`
**Issues**:
- Direct `$_REQUEST` usage (line 5)
- Mixed concerns (loading, rendering, business logic)
- Hardcoded form HTML

**Refactoring**:
```php
// Before: 96 lines of mixed concerns
<?PHP
namespace TD;
if (isset($_REQUEST['test']) || isset($_COOKIE['test'])) {
    ini_set('display_errors', 1);
    // ...
}
include_once __DIR__ . '/include_all.php';
// ... form generation, result loading

// After: Clean controller routing
<?php
use App\Controller\IndexController;
use App\Container\Container;

require_once __DIR__ . '/bootstrap.php';

$container = Container::getInstance();
$controller = $container->get(IndexController::class);

$response = $controller->handle(Request::fromGlobals());
$response->send();
```

#### `src/leaderboard/index.php`
**Issues**:
- Multiple `filter_input()` calls (lines 20-30)
- Conditional rendering based on `$_GET` params
- No proper routing

**Refactoring**:
```php
// Before: 65 lines of conditional logic
<?PHP
use function Leaderboard\Graph\print_graph_tab;
// ... 9 use statements
$get = filter_input(INPUT_GET, 'get', FILTER_SANITIZE_FULL_SPECIAL_CHARS) ?? '';
// ... conditionals based on $get

// After: Router-based approach
<?php
use App\Routing\Router;
use App\Controller\{
    LeaderboardController,
    LeaderboardGraphController,
    LeaderboardUsersController,
    LeaderboardLangsController
};

require_once __DIR__ . '/bootstrap.php';

$router = new Router();
$router->get('/leaderboard.php', LeaderboardController::class);
$router->get('/leaderboard.php', LeaderboardGraphController::class, ['graph' => '1']);
$router->get('/leaderboard.php', LeaderboardUsersController::class, ['get' => 'users']);
$router->get('/leaderboard.php', LeaderboardLangsController::class, ['get' => 'langs']);

$router->dispatch(Request::fromGlobals())->send();
```

#### `src/header.php`
**Issues**:
- Sets `$GLOBALS['user_in_coord']` (line 29)
- Converts runtime value to constant (line 30)
- Mixed navigation and authentication logic
- 192 lines of mixed concerns

**Refactoring**:
```php
// Extract to separate classes:

// src/View/Component/NavigationBar.php
final class NavigationBar {
    public function __construct(
        private readonly UserContext $user,
        private readonly UrlGenerator $urls
    ) {}

    public function render(): string {
        $items = [
            $this->leaderboardLink(),
            $this->priorLink(),
            $this->missingLink(),
            $this->coordinatorLink(),
            $this->githubLink(),
            $this->userLinks()
        ];

        return $this->renderTemplate('nav/bar', ['items' => $items]);
    }

    private function coordinatorLink(): ?string {
        return $this->user->isCoordinator()
            ? '<a href="/tdc/index.php" class="nav-link">Coordinator Tools</a>'
            : null;
    }
}

// src/Middleware/AuthenticationMiddleware.php
final class AuthenticationMiddleware {
    public function process(Request $request, callable $next): Response {
        $session = $request->getSession();
        $username = $session->get('username', '');

        $user = new UserContext(
            username: $username,
            isCoordinator: $this->coordinatorRepo->isActiveCoordinator($username)
        );

        return $next($request->withAttribute('user', $user));
    }
}
```

### 5.2 Backend Layer

#### `src/backend/api_calls/mdwiki_sql.php`
**Issues**:
- Hardcoded credentials (line 54)
- Duplicate methods `executequery()` and `fetchquery()`
- Creates new connection per query
- Test printing in database class

**Refactoring**:
```php
// Split into multiple classes:

// src/Database/DatabaseConfiguration.php
final class DatabaseConfiguration {
    public function __construct(
        public readonly string $host,
        public readonly string $dbname,
        public readonly string $user,
        public readonly string $password
    ) {}

    public static function fromEnv(): self {
        return new self(
            host: getenv('DB_HOST') ?: 'localhost:3306',
            dbname: getenv('DB_NAME') ?: 'mdwiki',
            user: getenv('DB_USER') ?: 'root',
            password: getenv('DB_PASSWORD') ?: ''
        );
    }
}

// src/Database/PdoConnection.php
final class PdoConnection {
    private ?PDO $pdo = null;

    public function __construct(
        private readonly DatabaseConfiguration $config,
        private readonly LoggerInterface $logger
    ) {}

    public function getConnection(): PDO {
        if ($this->pdo === null) {
            $this->pdo = $this->createConnection();
        }
        return $this->pdo;
    }

    private function createConnection(): PDO {
        try {
            $pdo = new PDO(
                "mysql:host={$this->config->host};dbname={$this->config->dbname}",
                $this->config->user,
                $this->config->password,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
                ]
            );

            $pdo->exec("SET SESSION sql_mode=(SELECT REPLACE(@@SESSION.sql_mode,'ONLY_FULL_GROUP_BY',''))");

            return $pdo;
        } catch (PDOException $e) {
            $this->logger->critical('Database connection failed', [
                'error' => $e->getMessage(),
                'host' => $this->config->host
            ]);
            throw new DatabaseConnectionException('Failed to connect to database', 0, $e);
        }
    }
}

// src/Database/QueryExecutor.php
final class QueryExecutor {
    public function __construct(
        private readonly PdoConnection $connection
    ) {}

    /**
     * @return array<int, array<string, mixed>>
     */
    public function execute(string $sql, array $params = []): array {
        $stmt = $this->connection->getConnection()->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function executeNonSelect(string $sql, array $params = []): int {
        $stmt = $this->connection->getConnection()->prepare($sql);
        $stmt->execute($params);
        return $stmt->rowCount();
    }
}
```

#### `src/backend/api_or_sql/funcs.php`
**Issues**:
- 15+ functions with static caching
- No cache invalidation
- Direct SQL in functions
- `super_function()` dependency

**Refactoring**:
```php
// Replace with repository pattern:

// src/Repository/CoordinatorRepository.php
interface CoordinatorRepository {
    /** @return array<string, bool> */
    public function getActiveIndexed(): array;
    public function isActiveCoordinator(string $user): bool;
}

final class SqlCoordinatorRepository implements CoordinatorRepository {
    public function __construct(
        private readonly QueryExecutor $query,
        private readonly CacheInterface $cache
    ) {}

    public function getActiveIndexed(): array {
        return $this->cache->get('coordinators.active', function() {
            $rows = $this->query->execute(
                "SELECT user, active FROM coordinator ORDER BY id"
            );
            return array_column($rows, 'active', 'user');
        });
    }

    public function isActiveCoordinator(string $user): bool {
        $coordinators = $this->getActiveIndexed();
        return ($coordinators[$user] ?? 0) == 1;
    }
}

// src/Repository/PageRepository.php (partial)
interface PageRepository {
    /** @return array<int, Page> */
    public function findByUserAndYearAndLang(string $user, ?string $year, ?string $lang): array;

    /** @return array<string, int> */
    public function getViewsIndexed(string $user, ?string $year, ?string $lang): array;
}

final class SqlPageRepository implements PageRepository {
    public function __construct(
        private readonly QueryExecutor $query,
        private readonly CacheInterface $cache
    ) {}

    public function findByUserAndYearAndLang(string $user, ?string $year, ?string $lang): array {
        $cacheKey = "pages.$user.$year.$lang";

        return $this->cache->get($cacheKey, function() use ($user, $year, $lang) {
            $sql = "SELECT DISTINCT title, word, translate_type, cat, lang, user,
                           target, date, pupdate, add_date, deleted
                    FROM pages p
                    LEFT JOIN views_new_all v ON p.target = v.target AND p.lang = v.lang
                    WHERE p.user = :user";
            $params = ['user' => $user];

            if ($this->isValid($year)) {
                $sql .= " AND YEAR(p.date) = :year";
                $params['year'] = $year;
            }

            if ($this->isValid($lang)) {
                $sql .= " AND p.lang = :lang";
                $params['lang'] = $lang;
            }

            $rows = $this->query->execute($sql, $params);
            return array_map(fn($row) => Page::fromArray($row), $rows);
        });
    }

    private function isValid(?string $value): bool {
        return !empty($value) && strtolower($value) !== 'all';
    }
}
```

#### `src/backend/api_or_sql/data_tab.php`
**Issues**:
- Static caching in functions
- `super_function()` dependency
- Direct SQL queries
- No return type declarations

**Refactoring**:
```php
// Create specific repositories:

// src/Repository/TranslationTypeRepository.php
interface TranslationTypeRepository {
    /** @return array<string, array{tt_lead: int, tt_full: int}> */
    public function getAll(): array;

    /** @return list<string> */
    public function getFullTranslateTypes(): array;

    /** @return list<string> */
    public function getNoLeadTranslateTypes(): array;
}

final class SqlTranslationTypeRepository implements TranslationTypeRepository {
    private ?array $cache = null;

    public function __construct(private readonly QueryExecutor $query) {}

    public function getAll(): array {
        if ($this->cache === null) {
            $rows = $this->query->execute(
                "SELECT tt_title, tt_lead, tt_full FROM translate_type"
            );
            $this->cache = array_column($rows, null, 'tt_title');
        }
        return $this->cache;
    }

    public function getFullTranslateTypes(): array {
        return array_keys(array_filter(
            $this->getAll(),
            fn($item) => $item['tt_full'] == 1
        ));
    }
}

// src/Repository/CategoryRepository.php
interface CategoryRepository {
    /** @return array<int, Category> */
    public function getAll(): array;

    public function getDefault(): ?Category;
    public function findByCategory(string $category): ?Category;
    public function findByCampaign(string $campaign): ?Category;
}

final class SqlCategoryRepository implements CategoryRepository {
    private ?array $cache = null;

    public function __construct(private readonly QueryExecutor $query) {}

    public function getAll(): array {
        if ($this->cache === null) {
            $rows = $this->query->execute(
                "SELECT id, category, category2, campaign, depth, def
                 FROM categories"
            );
            $this->cache = array_map(
                fn($row) => Category::fromArray($row),
                $rows
            );
        }
        return $this->cache;
    }

    public function findByCampaign(string $campaign): ?Category {
        $categories = $this->getAll();
        foreach ($categories as $category) {
            if ($category->campaign === $campaign) {
                return $category;
            }
        }
        return null;
    }
}
```

#### `src/backend/tables/tables.php`
**Issues**:
- Static properties as global cache
- File-level code execution
- No encapsulation

**Refactoring**:
```php
// Create proper service:

// src/Service/TitleInfoService.php
final class TitleInfoService {
    private array $pageviews = [];
    private array $words = [];
    private array $refs = [];
    private array $assessments = [];
    private array $langs = [];

    public function __construct(
        private readonly TitleInfoRepository $repo,
        private readonly LangRepository $langRepo
    ) {
        $this->initialize();
    }

    private function initialize(): void {
        $infos = $this->repo->getAll();

        foreach ($infos as $info) {
            $this->pageviews[$info->title] = $info->enViews;
            $this->words[$info->title] = $info->leadWords;
            $this->refs[$info->title] = $info->leadRefs;
            $this->assessments[$info->title] = $info->importance;
        }

        $this->langs = $this->langRepo->getAllIndexed();
    }

    public function getPageviews(string $title): int {
        return $this->pageviews[$title] ?? 0;
    }

    public function getWords(string $title): int {
        return $this->words[$title] ?? 0;
    }

    public function getLanguageName(string $code): string {
        return $this->langs[$code]['name'] ?? $code;
    }
}
```

#### `src/backend/tables/sql_tables.php`
**Issues**:
- Static configuration
- File-level execution populates statics
- Mixed concerns (data + configuration)

**Refactoring**:
```php
// Split into config and service:

// src/Config/CategoriesConfig.php
final class CategoriesConfig {
    private array $catToCamp = [];
    private array $campToCat = [];
    private array $campCat2 = [];
    private array $campDepth = [];
    private string $mainCat = '';
    private string $mainCamp = '';

    public function __construct(CategoryRepository $repo) {
        $this->loadFromRepository($repo);
    }

    private function loadFromRepository(CategoryRepository $repo): void {
        $categories = $repo->getAll();

        foreach ($categories as $cat) {
            $this->catToCamp[$cat->category] = $cat->campaign;
            $this->campToCat[$cat->campaign] = $cat->category;
            $this->campCat2[$cat->campaign] = $cat->category2;
            $this->campDepth[$cat->campaign] = $cat->depth;

            if ($cat->isDefault) {
                $this->mainCat = $cat->category;
                $this->mainCamp = $cat->campaign;
            }
        }
    }

    public function campaignToCategory(string $campaign): string {
        return $this->campToCat[$campaign] ?? '';
    }

    public function categoryToCampaign(string $category): string {
        return $this->catToCamp[$category] ?? '';
    }

    public function getMainCategory(): string {
        return $this->mainCat;
    }

    public function getDepth(string $campaign): int {
        return $this->campDepth[$campaign] ?? 1;
    }
}

// src/Config/SettingsConfig.php
final class SettingsConfig {
    private array $settings = [];

    public function __construct(SettingsRepository $repo) {
        $rows = $repo->getAll();
        $this->settings = array_column($rows, null, 'title');
    }

    public function get(string $key, mixed $default = null): mixed {
        return $this->settings[$key]['value'] ?? $default;
    }

    public function getBool(string $key): bool {
        return $this->get($key) === '1';
    }
}
```

### 5.3 Results Layer

#### `src/results/results.php`
**Issues**:
- Long parameter list passed via associative array
- HTML generation in business logic
- Mixed concerns

**Refactoring**:
```php
// Split into service and view:

// src/Service/ResultsService.php
final class ResultsService {
    public function __construct(
        private readonly PageRepository $pageRepo,
        private readonly WikidataService $wikidata,
        private readonly CategoryRepository $categoryRepo
    ) {}

    public function getResults(SearchCriteria $criteria): ResultsCollection {
        // Business logic only
        $existing = $this->pageRepo->findByCategoryAndLang(
            $criteria->category,
            $criteria->targetLang
        );

        $allArticles = $this->wikidata->getCategoryMembers(
            $criteria->category,
            $criteria->depth
        );

        if ($criteria->filterSparql) {
            $allArticles = $this->wikidata->filterExisting($allArticles, $criteria->targetLang);
        }

        $missing = array_diff($allArticles, array_column($existing, 'title'));
        $inProcess = $this->pageRepo->findInProcess($missing, $criteria->targetLang);

        $missing = array_diff($missing, array_column($inProcess, 'title'));

        return new ResultsCollection(
            inProcess: $inProcess,
            existing: $existing,
            missing: array_values($missing)
        );
    }
}

// src/View/ResultsView.php
final class ResultsView {
    public function __construct(
        private readonly TemplateRenderer $renderer,
        private readonly UrlGenerator $urls
    ) {}

    public function renderResults(ResultsCollection $results, RenderContext $context): string {
        $html = '';

        $html .= $this->renderCard(
            title: "Results: " . count($results->missing),
            content: $this->renderMissingTable($results->missing, $context)
        );

        if (!empty($results->inProcess)) {
            $html .= $this->renderCard(
                title: "In process: " . count($results->inProcess),
                content: $this->renderInProcessTable($results->inProcess, $context)
            );
        }

        if ($context->showExisting && count($results->existing) > 1) {
            $html .= $this->renderCard(
                title: "Exists: " . count($results->existing),
                content: $this->renderExistingTable($results->existing, $context)
            );
        }

        return $html;
    }

    private function renderCard(string $title, string $content): string {
        return $this->renderer->render('components/card', [
            'title' => $title,
            'content' => $content
        ]);
    }
}
```

#### `src/results/results_table.php`
**Issues**:
- Complex nested logic
- 144 lines in single function
- Inline HTML generation
- Multiple responsibilities

**Refactoring**:
```php
// Create dedicated view components:

// src/View/Results/ResultsTableView.php
final class ResultsTableView {
    public function __construct(
        private readonly ResultsRowView $rowView,
        private readonly TranslationUrlGenerator $urls
    ) {}

    public function render(array $articles, TableContext $context): string {
        $rows = array_map(
            fn($article) => $this->rowView->render($article, $context),
            $this->sortAndFilter($articles, $context)
        );

        return $this->renderTemplate('results/table', [
            'rows' => implode("\n", $rows)
        ]);
    }

    private function sortAndFilter(array $articles, TableContext $context): array {
        $sorted = $this->sortByPageviews($articles);

        return $this->applyTranslationRules(
            $sorted,
            $context->translationType,
            $context->fullTranslatorUser,
            $context->noLeadTranslates,
            $context->translatesFull
        );
    }
}

// src/View/Results/ResultsRowView.php
final class ResultsRowView {
    public function __construct(
        private readonly TranslationUrlGenerator $urls,
        private readonly TitleInfoService $info
    ) {}

    public function render(Article $article, TableContext $context): string {
        $props = $this->info->getProperties($article);
        $translateButton = $this->renderTranslateButton($article, $context, $props);

        return $this->renderTemplate('results/row', [
            'title' => $article->title,
            'mdwiki_url' => $this->urls->mdwiki($article->title),
            'translate_url' => $this->urls->translate($article, $context->targetLang),
            'translate_button' => $translateButton,
            'pageviews' => $props->views,
            'assessment' => $props->assessment,
            'words' => $props->words,
            'refs' => $props->refs,
            'qid' => $props->qid
        ]);
    }
}
```

### 5.4 Leaderboard Layer

#### `src/leaderboard/main.php`
**Issues**:
- Static HTML generation
- Data transformation in view logic
- Mixed concerns

**Refactoring**:
```php
// Create service and view separation:

// src/Service/LeaderboardService.php
final class LeaderboardService {
    public function __construct(
        private readonly UserRepository $userRepo,
        private readonly LanguageRepository $langRepo,
        private readonly PageRepository $pageRepo
    ) {}

    public function getLeaderboardData(LeaderboardCriteria $criteria): LeaderboardData {
        $topUsers = $this->userRepo->getTopUsers(
            year: $criteria->year,
            userGroup: $criteria->userGroup,
            category: $criteria->category,
            month: $criteria->month
        );

        $topLangs = $this->langRepo->getTopLanguages(
            year: $criteria->year,
            userGroup: $criteria->userGroup,
            category: $criteria->category,
            month: $criteria->month
        );

        $status = $this->pageRepo->getStatusByMonth(
            year: $criteria->year,
            userGroup: $criteria->userGroup,
            category: $criteria->category
        );

        return new LeaderboardData(
            userCount: count($topUsers),
            articleCount: array_sum(array_column($topUsers, 'count')),
            wordCount: array_sum(array_column($topUsers, 'words')),
            langCount: count($topLangs),
            viewsCount: array_sum(array_column($topUsers, 'views')),
            topUsers: $topUsers,
            topLangs: $topLangs,
            status: $status
        );
    }
}

// src/View/LeaderboardView.php
final class LeaderboardView {
    public function __construct(
        private readonly NumbersCardView $numbers,
        private readonly UsersTableCardView $users,
        private readonly LangsTableCardView $langs
    ) {}

    public function render(LeaderboardData $data, FilterFormContext $filters): string {
        return <<<HTML
            {$filters->render()}
            <hr/>
            <div class="container-fluid">
                <div class="row g-3">
                    {$this->numbers->render($data)}
                    {$this->users->render($data->topUsers)}
                    {$this->langs->render($data->topLangs)}
                </div>
            </div>
            HTML;
    }
}
```

#### `src/leaderboard/leader_tables.php`
**Issues**:
- Inline HTML in functions
- 100+ lines of HTML string concatenation
- No separation of concerns

**Refactoring**:
```php
// Use template engine:

// src/View/Leaderboard/NumbersCardView.php
final class NumbersCardView {
    public function render(LeaderboardData $data): string {
        return $this->renderer->render('leaderboard/numbers_card', [
            'user_count' => number_format($data->userCount),
            'article_count' => number_format($data->articleCount),
            'word_count' => number_format($data->wordCount),
            'lang_count' => number_format($data->langCount),
            'views_count' => number_format($data->viewsCount)
        ]);
    }
}

// templates/leaderboard/numbers_card.html.php
?>
<div class="col-lg-3 col-md-12 col-sm-12">
    <div class="card card2 mb-3">
        <div class="card-header">
            <span class="card-title" style="font-weight:bold;">Numbers</span>
        </div>
        <div class="card-body1 card2">
            <table class='table compact table-striped'>
                <thead>
                    <tr><th class="spannowrap">Type</th><th>Number</th></tr>
                </thead>
                <tbody>
                    <tr><td><b>Users</b></td><td><?= $user_count ?></td></tr>
                    <tr><td><b>Articles</b></td><td><?= $article_count ?></td></tr>
                    <tr><td><b>Words</b></td><td><?= $word_count ?></td></tr>
                    <tr><td><b>Languages</b></td><td><?= $lang_count ?></td></tr>
                    <tr><td><b>Pageviews</b></td><td><?= $views_count ?></td></tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php
```

### 5.5 Frontend Layer

#### `src/frontend/html.php`
**Issues**:
- 273 lines of HTML helper functions
- No component structure
- Direct URL generation

**Refactoring**:
```php
// Create proper components:

// src/View/Component/Card.php
final class Card {
    public function __construct(
        public readonly string $title,
        public readonly string $content,
        public readonly ?string $subtitle = null,
        public readonly bool $collapsible = true
    ) {}

    public function render(): string {
        return <<<HTML
            <div class='card'>
                <div class="card-header">
                    <span class="card-title h5">{$this->title}</span>
                    {$this->subtitle}
                    {$this->renderCollapseButton()}
                </div>
                <div class='card-body1 card2'>
                    {$this->content}
                </div>
            </div>
            HTML;
    }

    private function renderCollapseButton(): string {
        return $this->collapsible
            ? '<button type="button" class="btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i></button>'
            : '';
    }
}

// src/View/Component/Dropdown.php
final class Dropdown {
    public function __construct(
        public readonly string $id,
        public readonly string $name,
        /** @var array<string, string> */
        public readonly array $options,
        public readonly ?string $selected = null,
        public readonly ?string $additionalOption = null
    ) {}

    public function render(): string {
        $optionsHtml = '';

        if ($this->additionalOption) {
            $selected = $this->selected === $this->additionalOption ? 'selected' : '';
            $label = $this->additionalOption === 'all' ? 'All' : $this->additionalOption;
            $optionsHtml .= "<option value='{$this->additionalOption}' $selected>$label</option>";
        }

        foreach ($this->options as $value => $label) {
            $selected = $this->selected === $value ? 'selected' : '';
            $optionsHtml .= "<option value='$value' $selected>$label</option>";
        }

        return <<<HTML
            <select dir="ltr" id="{$this->id}" name="{$this->name}" class="form-select" data-bs-theme="auto">
                $optionsHtml
            </select>
            HTML;
    }
}

// src/Routing/UrlGenerator.php
final class UrlGenerator {
    public function __construct(
        private readonly string $baseUrl
    ) {}

    public function mdwikiArticle(string $title): string {
        $encoded = rawurlencode(str_replace(' ', '_', $title));
        return "https://mdwiki.org/wiki/$encoded";
    }

    public function wikipediaArticle(string $title, string $lang): string {
        $encoded = rawurlencode(str_replace(' ', '_', $title));
        return "https://$lang.wikipedia.org/wiki/$encoded";
    }

    public function wikidataItem(string $qid): string {
        return "https://wikidata.org/wiki/" . rawurlencode($qid);
    }

    public function translation(
        string $title,
        string $fromLang,
        string $toLang,
        TranslationType $type
    ): string {
        $params = [
            'page' => $type === TranslationType::FULL
                ? "User:Mr. Ibrahem/$title/full"
                : "User:Mr. Ibrahem/$title",
            'from' => $fromLang,
            'sx' => 'true',
            'to' => $toLang,
            'targettitle' => $title
        ];

        return "//$toLang.wikipedia.org/wiki/Special:ContentTranslation?"
            . http_build_query($params, '', '&', PHP_QUERY_RFC3986);
    }
}
```

#### `src/frontend/forms.php`
**Issues**:
- Form generation mixed with business logic
- No form validation
- Direct HTML output

**Refactoring**:
```php
// Create form builder:

// src/Form/FormBuilder.php
final class FormBuilder {
    private array $fields = [];

    public function select(string $name, array $options, array $attributes = []): self {
        $this->fields[] = new SelectField($name, $options, $attributes);
        return $this;
    }

    public function text(string $name, array $attributes = []): self {
        $this->fields[] = new TextField($name, $attributes);
        return $this;
    }

    public function submit(string $label = 'Submit'): self {
        $this->fields[] = new SubmitField($label);
        return $this;
    }

    public function render(): string {
        $html = '<form method="GET" action="index.php" class="form-inline">';

        foreach ($this->fields as $field) {
            $html .= $field->render();
        }

        $html .= '</form>';
        return $html;
    }
}

// src/Form/Field/SelectField.php
final class SelectField implements FormField {
    public function __construct(
        public readonly string $name,
        /** @var array<string, string> */
        public readonly array $options,
        public readonly array $attributes = []
    ) {}

    public function render(): string {
        $selected = $this->attributes['value'] ?? null;
        $attrs = $this->buildAttributes();

        $options = '';
        foreach ($this->options as $value => $label) {
            $isSelected = $value === $selected ? 'selected' : '';
            $options .= "<option value='$value' $isSelected>$label</option>";
        }

        return "<select name='$this->name' $attrs>$options</select>";
    }
}
```

### 5.6 API Layer

#### `src/backend/api_calls/wiki_api.php`
**Issues**:
- Single function with mixed concerns
- Hardcoded URL construction
- No error handling

**Refactoring**:
```php
// Create proper API client:

// src/ExternalApi/WikipediaPageviewsClient.php
final class WikipediaPageviewsClient {
    public function __construct(
        private readonly HttpClient $http,
        private readonly LoggerInterface $logger
    ) {}

    public function getArticleViewsUrl(
        string $article,
        string $lang,
        ?\DateTimeInterface $startDate = null
    ): string {
        $start = $startDate?->format('Y-m-d') ?: '2019-01-01';
        $end = (new \DateTime())->modify('-1 day')->format('Y-m-d');

        $params = [
            'project' => "$lang.wikipedia.org",
            'platform' => 'all-access',
            'agent' => 'all-agents',
            'start' => $start,
            'end' => $end,
            'redirects' => '0',
            'pages' => $article
        ];

        return 'https://pageviews.wmcloud.org/?' . http_build_query($params);
    }

    public function getArticleViews(
        string $article,
        string $lang,
        ?\DateTimeInterface $startDate = null
    ): ?int {
        $url = $this->getAnalyticsApiUrl($article, $lang, $startDate);

        try {
            $response = $this->http->request('GET', $url);
            $data = json_decode($response->getBody()->getContents(), true);

            return $data['items'][0]['views'] ?? null;
        } catch (\Exception $e) {
            $this->logger->error('Failed to fetch pageviews', [
                'article' => $article,
                'lang' => $lang,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }

    private function getAnalyticsApiUrl(string $article, string $lang, ?\DateTimeInterface $startDate): string {
        $start = $startDate?->format('Ymd') ?: '20190101';
        $encoded = rawurlencode($article);

        return "https://wikimedia.org/api/rest_v1/metrics/pageviews/per-article/"
            . "$lang.wikipedia/all-access/all-agents/$encoded/daily/$start/2030010100";
    }
}
```

#### `src/backend/results/sparql_bots/sparql_bot.php`
**Issues**:
- SPARQL query construction inline
- No query abstraction
- Hardcoded endpoint

**Refactoring**:
```php
// Create SPARQL service:

// src/ExternalApi/WikidataSparqlClient.php
final class WikidataSparqlClient {
    private const ENDPOINT = 'https://query.wikidata.org/sparql';

    public function __construct(
        private readonly HttpClient $http,
        private readonly LoggerInterface $logger
    ) {}

    /**
     * @return array<int, string>
     */
    public function getCategoryMembers(string $category, int $depth = 1): array {
        $query = $this->buildCategoryQuery($category, $depth);

        try {
            $response = $this->executeQuery($query);
            return $this->extractTitles($response);
        } catch (SparqlException $e) {
            $this->logger->error('SPARQL query failed', [
                'category' => $category,
                'error' => $e->getMessage()
            ]);
            return [];
        }
    }

    /**
     * @return array<int, string>
     */
    public function filterExistingArticles(array $titles, string $langCode): array {
        $query = $this->buildFilterQuery($titles, $langCode);

        try {
            $response = $this->executeQuery($query);
            $existing = $this->extractTitles($response);
            return array_diff($titles, $existing);
        } catch (SparqlException $e) {
            $this->logger->warning('Filter query failed, returning all titles', [
                'error' => $e->getMessage()
            ]);
            return $titles;
        }
    }

    private function buildCategoryQuery(string $category, int $depth): string {
        $encoded = addslashes($category);
        return <<<SPARQL
            SELECT ?item ?itemLabel WHERE {
              SERVICE wikibase:mwapi {
                bd:serviceParam wikibase:api "Generator";
                bd:serviceParam wikibase:endpoint "https://mdwiki.org/w/api.php";
                bd:serviceParam mwapi:gcmtitle "Category:$encoded";
                bd:serviceParam mwapi:gcmlimit "max";
                bd:serviceParam mwapi:gcmdepth "$depth";
                bd:serviceParam mwapi:gcmtype "page";
                ?item wikibase:apiOutputItem mwapi:item.
              }
              SERVICE wikibase:label { bd:serviceParam wikibase:language "[AUTO_LANGUAGE],en". }
            }
            SPARQL;
    }

    private function buildFilterQuery(array $titles, string $langCode): string {
        $values = implode(' ', array_map(fn($t) => '"' . addslashes($t) . '"', $titles));
        $lang = addslashes($langCode);

        return <<<SPARQL
            SELECT ?item ?itemLabel WHERE {
              VALUES ?title { $values }
              ?item (wdt:P31/(wdt:P279*)) wd:Q5185279;
                    schema:name ?title;
                    wdt:P407 ?lang.
              ?lang wdt:P218 ?langCode.
              FILTER (?langCode = "$lang")
            }
            SPARQL;
    }

    /**
     * @return array<string, mixed>
     */
    private function executeQuery(string $query): array {
        $response = $this->http->request('GET', self::ENDPOINT, [
            'query' => [
                'format' => 'json',
                'query' => $query
            ]
        ]);

        $data = json_decode($response->getBody()->getContents(), true);

        if (!isset($data['results']['bindings'])) {
            throw new SparqlException('Invalid SPARQL response format');
        }

        return $data['results']['bindings'];
    }

    /**
     * @param array<string, mixed> $bindings
     * @return array<int, string>
     */
    private function extractTitles(array $bindings): array {
        return array_map(
            fn($b) => $b['itemLabel']['value'] ?? '',
            $bindings
        );
    }
}
```

### 5.7 Utilities

#### `src/backend/loaders/load_request.php`
**Issues**:
- Input validation mixed with request loading
- Inconsistent validation
- Returns array (primitive obsession)

**Refactoring**:
```php
// Create request object:

// src/Http/SearchRequest.php
final class SearchRequest {
    public function __construct(
        public readonly bool $test,
        public readonly bool $doit,
        public readonly string $code,
        public readonly string $codeLangName,
        public readonly string $category,
        public readonly string $campaign,
        public readonly TranslationType $translationType,
        public readonly bool $filterSparql,
        public readonly string $mobileTd
    ) {}

    public static function fromGlobals(Request $request, LangCodeResolver $langResolver, CategoriesConfig $categories): self {
        $code = $request->getString('code');
        $code = $langResolver->normalizeCode($code);
        $codeLangName = $langResolver->getNameForCode($code);

        $category = $request->getString('cat');
        $campaign = $request->getString('camp');

        // Resolve category/campaign relationship
        if (empty($category) && !empty($campaign)) {
            $category = $categories->campaignToCategory($campaign);
        }
        if (!empty($category) && empty($campaign)) {
            $campaign = $categories->categoryToCampaign($category);
        }

        $translationType = TranslationType::from(
            $request->getEnum('type', ['lead', 'all'], 'lead')
        );

        $doit = $request->getString('doit') !== '';
        if (empty($codeLangName)) {
            $doit = false;
        }

        return new self(
            test: $request->getString('test') !== '',
            doit: $doit,
            code: $code,
            codeLangName: $codeLangName,
            category: $category,
            campaign: $campaign,
            translationType: $translationType,
            filterSparql: $request->getBool('filter_sparql_x'),
            mobileTd: $request->getString('mobile_td', '1')
        );
    }
}

// src/Http/Request.php
final class Request {
    private array $attributes = [];

    public function __construct(
        private readonly array $get,
        private readonly array $post,
        private readonly array $cookies,
        private readonly array $server
    ) {}

    public static function fromGlobals(): self {
        return new self($_GET, $_POST, $_COOKIE, $_SERVER);
    }

    public function getString(string $key, string $default = ''): string {
        $value = $this->get[$key] ?? $this->post[$key] ?? $default;
        return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
    }

    public function getBool(string $key): bool {
        return !empty($this->get[$key] ?? $this->post[$key]);
    }

    public function getEnum(string $key, array $allowed, string $default): string {
        $value = $this->getString($key, $default);
        return in_array($value, $allowed, true) ? $value : $default;
    }

    public function withAttribute(string $key, mixed $value): self {
        $new = clone $this;
        $new->attributes[$key] = $value;
        return $new;
    }

    public function getAttribute(string $key, mixed $default = null): mixed {
        return $this->attributes[$key] ?? $default;
    }
}
```

---

## 6. Technical Debt Risks

### 6.1 Critical Risks

| Risk | Impact | Likelihood | Mitigation Priority |
|------|--------|------------|-------------------|
| Hard-coded database credentials | Security breach | HIGH | IMMEDIATE |
| SQL Injection via poor escaping | Data breach | MEDIUM | HIGH |
| Global state corruption | Application crashes | MEDIUM | HIGH |
| Memory leaks from static caching | Performance degradation | MEDIUM | MEDIUM |
| Test coverage < 10% | Undetected bugs | HIGH | MEDIUM |
| No input validation layer | XSS/Injection attacks | HIGH | HIGH |

### 6.2 Security Risk Matrix

```
┌─────────────────────────────────────────────────────────────────────┐
│                    SECURITY RISK ASSESSMENT                         │
├─────────────────────────────────────────────────────────────────────┤
│                                                                     │
│  CRITICAL (Fix Immediately)                                         │
│  ┌─────────────────────────────────────────────────────────────┐   │
│  │ • Hard-coded credentials (mdwiki_sql.php:54)                │   │
│  │ • No input sanitization on 95+ superglobal accesses          │   │
│  │ • SQL queries without proper escaping in some locations      │   │
│  └─────────────────────────────────────────────────────────────┘   │
│                                                                     │
│  HIGH (Fix This Sprint)                                             │
│  ┌─────────────────────────────────────────────────────────────┐   │
│  │ • XSS via direct output of user input                       │   │
│  │ • No CSRF protection on forms                               │   │
│  │ • Error messages expose internal details                    │   │
│  │ • No rate limiting on API calls                             │   │
│  └─────────────────────────────────────────────────────────────┘   │
│                                                                     │
│  MEDIUM (Plan Fix)                                                  │
│  ┌─────────────────────────────────────────────────────────────┐   │
│  │ • Session management via globals                            │   │
│  │ • No authentication on some routes                          │   │
│  │ • Debug mode exposed via cookie check                       │   │
│  │ • Path traversal via user input                             │   │
│  └─────────────────────────────────────────────────────────────┘   │
│                                                                     │
└─────────────────────────────────────────────────────────────────────┘
```

### 6.3 Maintainability Risk Matrix

```
┌─────────────────────────────────────────────────────────────────────┐
│                   MAINTAINABILITY RISK ASSESSMENT                    │
├─────────────────────────────────────────────────────────────────────┤
│                                                                     │
│  HIGH IMPACT                                                        │
│  ┌─────────────────────────────────────────────────────────────┐   │
│  │ • Global state prevents unit testing                        │   │
│  │ • Static properties cause state pollution                   │   │
│  │ • Duplicated code (get_results x2, database x2)             │   │
│  │ • No interfaces defined                                     │   │
│  └─────────────────────────────────────────────────────────────┘   │
│                                                                     │
│  MEDIUM IMPACT                                                      │
│  ┌─────────────────────────────────────────────────────────────┐   │
│  │ • Long parameter lists (10+ params)                         │   │
│  │ • Mixed paradigms (procedural + OO)                          │   │
│  │ • Magic numbers and strings                                 │   │
│  │ • Deep directory nesting                                    │   │
│  │ • No type declarations                                      │   │
│  └─────────────────────────────────────────────────────────────┘   │
│                                                                     │
│  LOW IMPACT                                                         │
│  ┌─────────────────────────────────────────────────────────────┐   │
│  │ • Inconsistent naming (file vs class)                       │   │
│  │ • Commented-out code                                        │   │
│  │ • Missing documentation                                     │   │
│  └─────────────────────────────────────────────────────────────┘   │
│                                                                     │
└─────────────────────────────────────────────────────────────────────┘
```

### 6.4 Performance Risks

| Issue | Impact | Current Load | Risk at 10x Load |
|-------|--------|--------------|------------------|
| New DB connection per query | HIGH | ~50 req/sec | CRITICAL |
| No query result caching | MEDIUM | ~100 queries/page | HIGH |
| Static variable memory growth | MEDIUM | ~5MB/request | MEDIUM |
| No connection pooling | MEDIUM | Limited connections | CRITICAL |
| Synchronous external API calls | HIGH | 200ms+ per call | CRITICAL |

### 6.5 Scalability Concerns

```
Current Architecture Scalability Analysis:

Single Server:
├── Database: New connection per query
│   └── LIMIT: ~100 concurrent requests
│
├── PHP Process: Include-based loading
│   └── LIMIT: Memory per process ~50MB
│
├── Static Caching: Per-process static variables
│   └── LIMIT: No horizontal scaling possible
│
└── External APIs: Blocking calls
    └── LIMIT: 200ms+ latency per request

Recommendations for 10x Scale:
1. Implement connection pooling (immediate)
2. Add Redis cache layer (before 3x)
3. Convert to stateless services (before 5x)
4. Add async queue for API calls (before 10x)
```

### 6.6 Technical Debt Prioritization

**Pay Down Immediately**:
1. Remove hard-coded credentials (SECURITY)
2. Add input validation layer (SECURITY)
3. Fix SQL query escaping (SECURITY)

**Pay Down This Quarter**:
1. Replace global state with DI
2. Implement proper caching
3. Add connection pooling
4. Write unit tests for core logic

**Pay Down This Year**:
1. Complete repository pattern migration
2. Extract service layer
3. Implement proper routing
4. Add monitoring/alerting

---

## 7. Success Metrics

### 7.1 Code Quality Metrics

| Metric | Current | Target | Measurement |
|--------|---------|--------|-------------|
| Test Coverage | ~5% | 70% | PHPUnit --coverage-html |
| Cyclomatic Complexity | 15-30 avg | <10 avg | PHPStan |
| Code Duplication | ~15% | <5% | phpcpd |
| Type Coverage | ~20% | 95% | Psalm |
| Security Issues | 5+ critical | 0 | PHPStan + security audit |

### 7.2 Performance Metrics

| Metric | Current | Target | Measurement |
|--------|---------|--------|-------------|
| Avg Response Time | 800ms | <400ms | APM/New Relic |
| P95 Response Time | 2000ms | <800ms | APM/New Relic |
| DB Connections/request | 5-10 | 1-2 | Connection pool stats |
| Cache Hit Rate | 0% | >80% | Redis stats |
| Memory/request | 50MB | <30MB | memory_get_usage() |

### 7.3 Architecture Health Metrics

| Metric | Current | Target | Notes |
|--------|---------|--------|-------|
| Files using globals | 25+ | 0 | Zero global state |
| Static properties | 8 classes | 0 | Use DI |
| Files without namespace | 5 | 0 | PSR-4 compliance |
| Functions without types | 90% | 0 | Strict types |
| Max nesting depth | 6 | 3 | Cognitive load |

---

## 8. Implementation Checklist

### Phase 1: Security (Week 1-2)
- [ ] Remove hard-coded credentials from mdwiki_sql.php
- [ ] Add .env file support
- [ ] Create Request validation class
- [ ] Add CSRF token generation/validation
- [ ] Implement XSS escaping in all output
- [ ] Add rate limiting middleware
- [ ] Security audit and penetration testing

### Phase 2: Architecture (Week 3-4)
- [ ] Create DI container
- [ ] Define repository interfaces
- [ ] Implement UserRepository
- [ ] Implement PageRepository
- [ ] Implement CategoryRepository
- [ ] Implement SettingsRepository
- [ ] Create service layer base classes

### Phase 3: Presentation (Week 5-6)
- [ ] Create View component base classes
- [ ] Extract Card component
- [ ] Extract Table component
- [ ] Extract Dropdown component
- [ ] Create template system
- [ ] Implement routing
- [ ] Create controller base class

### Phase 4: Testing (Week 7-8)
- [ ] Set up PHPUnit with fixtures
- [ ] Write UserRepository tests
- [ ] Write PageRepository tests
- [ ] Write ResultsService tests
- [ ] Write LeaderboardService tests
- [ ] Add integration tests
- [ ] Set up test coverage reporting

### Phase 5: Performance (Week 9-10)
- [ ] Implement connection pooling
- [ ] Add Redis caching
- [ ] Implement cache invalidation
- [ ] Add performance monitoring
- [ ] Optimize slow queries
- [ ] Add async processing for API calls
- [ ] Load testing and optimization

---

## 9. Recommended Tools & Libraries

### Development Tools
```json
{
  "require-dev": {
    "phpstan/phpstan": "^1.10",
    "slevomat/coding-standard": "^8.0",
    "vimeo/psalm": "^5.0",
    "phpunit/phpunit": "^11.0",
    "sebastian/phpcpd": "^6.0",
    "phpmd/phpmd": "^2.13",
    "squizlabs/php_codesniffer": "^3.7"
  }
}
```

### Production Dependencies
```json
{
  "require": {
    "php": "^8.2",
    "ext-pdo": "*",
    "ext-json": "*",
    "vlucas/phpdotenv": "^5.5",
    "guzzlehttp/guzzle": "^7.7",
    "twig/twig": "^3.6",
    "monolog/monolog": "^3.0",
    "psr/container": "^2.0",
    "psr/http-message": "^2.0",
    "nyholm/psr7": "^1.8",
    "relay/relay": "^2.1"
  }
}
```

---

## Appendix A: File-by-File Priority Matrix

| File | Lines | Complexity | Security Risk | Priority |
|------|-------|------------|---------------|----------|
| mdwiki_sql.php | 240 | High | CRITICAL | P0 |
| index.php | 96 | Medium | MEDIUM | P1 |
| header.php | 192 | Medium | LOW | P1 |
| leaderboard/index.php | 65 | Medium | MEDIUM | P1 |
| funcs.php (api_or_sql) | 405 | High | LOW | P2 |
| data_tab.php | 258 | Medium | LOW | P2 |
| results.php | 165 | High | MEDIUM | P2 |
| sql_tables.php | 120 | Medium | LOW | P2 |
| tables.php | 75 | Low | LOW | P2 |
| load_request.php | 62 | Low | MEDIUM | P2 |
| results_table.php | 144 | High | LOW | P2 |
| get_results.php | 87 | Medium | LOW | P2 |
| top.php | 232 | High | LOW | P3 |
| main.php (leaderboard) | 109 | Medium | LOW | P3 |
| leader_tables.php | 101 | Medium | LOW | P3 |

**Legend**: P0 = Immediate, P1 = This week, P2 = This month, P3 = This quarter

---

*End of Static Analysis Report*
