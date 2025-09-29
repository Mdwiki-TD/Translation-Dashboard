<?php


namespace Results\GetCats;
/*
use function Results\GetCats\get_cats_from_cache;
use function Results\GetCats\get_mdwiki_cat_members;

*/

/**
 * Class CategoryFetcher
 *
 * Fetch category members (recursively) from mdwiki with optional caching and file-based "cache" lookup.
 * Designed for dependency injection and unit testing.
 */

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
    private const NS_CUSTOM_EXAMPLE = 3000; // Or a more descriptive name

    /**
     * @param array $options Options array. Supported keys:
     *   - 'nocache' => bool (if true, ignore file cache),
     *   - 'debug' => bool (if true, echo debug prints).
     */
    public function __construct(
        array $options = [],
        string $endPoint = '',
    ) {
        $this->options = $options;
        $this->endPoint = (!empty($endPoint)) ? $endPoint : 'https://mdwiki.org/w/api.php';
        $this->debug = (bool)($options['debug'] ?? false);
        $this->connectTimeout = $options['connect_timeout'] ?? 10;
        $this->timeout = $options['timeout'] ?? 15;
        $this->tablesDir = $options['tablesDir'] ?? '';
    }

    /**
     * Public entry point — fetch members (pages only) under $rootCat up to $depth levels deep.
     *
     * @param string $rootCat root category name (e.g. 'RTT' or 'Category:RTT')
     * @param int $depth depth >= 0; 0 = only members of root, 1 = include subcategories one level down, etc.
     * @param bool $useCache whether to use file-based cache (openTablesFile)
     * @return array filtered list of page titles (unique)
     */
    public function getMdwikiCatMembers(string $rootCat, int $depth = 0, bool $useCache = true): array
    {
        // Validate depth is non-negative
        if (!is_int($depth) || $depth < 0) {
            $depth = 0;
        }
        $titles = [];
        $cats = [$rootCat];
        $depthDone = -1;

        while (count($cats) > 0 && $depth > $depthDone) {
            $nextCats = [];
            foreach ($cats as $cat) {
                $all = $this->getCatsMembers($cat, $useCache);
                foreach ($all as $title) {
                    if ($this->startsWith($title, 'Category:')) {
                        $nextCats[] = $title;
                    } else {
                        $titles[] = $title;
                    }
                }
            }
            $depthDone++;
            $cats = $nextCats;
        }

        $titles = array_unique($titles);
        $newTitles = $this->titlesFilters($titles, false);
        $this->log("getMdwikiCatMembers newTitles size:" . count($newTitles));
        return $newTitles;
    }

    /* ----------------- Internal helpers ----------------- */

    /**
     * Get members for a single category, using file cache first if requested, otherwise the fetcher.
     *
     * @param string $cat
     * @param bool $useCache
     * @return array
     */
    private function getCatsMembers(string $cat, bool $useCache): array
    {
        $all = [];
        if ($useCache) {
            $all = $this->getCatsFromCache($cat);
        }
        if (empty($all)) {
            $all = $this->fetchCatsMembers($cat);
        }
        $this->log("getCatsMembers all size: " . count($all) . " cat: $cat");
        return $all;
    }

    /**
     * Default implementation of fetch_cats_members() — uses APCu if available and falls back to API.
     *
     * @param string $cat
     * @return array
     */
    private function fetchCatsMembers(string $cat): array
    {
        $cacheKey = "Category_members_" . md5($cat);
        $cacheTtl = 3600 * 12;
        $items = false;

        if (function_exists('apcu_fetch')) {
            $items = apcu_fetch($cacheKey);
            // original special-case behavior preserved:
            if (empty($items) || ($cat === "RTT" && is_array($items) && count($items) < 3000)) {
                apcu_delete($cacheKey);
                $items = false;
            }
        }

        if ($items === false) {
            $items = $this->fetchCatsMembersApi($cat);
            $this->log("apcu_store() size:" . count($items) . " cat: $cat");
            if (function_exists('apcu_store')) {
                apcu_store($cacheKey, $items, $cacheTtl);
            }
        } else {
            $this->log("apcu_fetch() size:" . count($items) . " cat: $cat");
        }

        return $items;
    }

    /**
     * Default API-based fetch for category members (mirrors original fetch_cats_members_api).
     *
     * @param string $cat
     * @return array
     */
    private function fetchCatsMembersApi(string $cat): array
    {
        if (!$this->startsWith($cat, 'Category:')) {
            $cat = "Category:$cat";
        }

        $params = [
            "action" => "query",
            "list" => "categorymembers",
            "cmtitle" => $cat,
            "cmlimit" => "max",
            "cmtype" => "page|subcat",
            "format" => "json"
        ];

        $items = [];
        $cmcontinue = 'x';
        $max_iterations = 100;
        $iteration = 0;

        // while (!empty($cmcontinue)) {
        while (!empty($cmcontinue) && $iteration++ < $max_iterations) {
            if ($cmcontinue !== 'x') {
                $params['cmcontinue'] = $cmcontinue;
            }

            $resa = $this->getMdwikiUrlsWithParams($params);
            if (!isset($resa["query"]) || !isset($resa["query"]["categorymembers"])) {
                $this->log("Error fetching category members for '$cat'");
                return $items;
            }
            $cmcontinue = $resa["continue"]["cmcontinue"] ?? '';

            $categoryMembers = $resa["query"]["categorymembers"] ?? [];
            foreach ($categoryMembers as $pages) {
                // keep ns 0 (articles), 14 (Category), 3000 (??? as original)
                $ns = $pages["ns"] ?? -1;
                if ($ns === self::NS_MAIN || $ns === self::NS_CATEGORY || $ns === self::NS_CUSTOM_EXAMPLE) {
                    $items[] = $pages["title"];
                }
            }
        }
        if ($iteration >= $max_iterations) {
            $this->log("fetch_cats_members_api: Hit maximum iterations for '$cat'");
        }

        $this->log("fetchCatsMembersApi() items size:" . count($items));
        return $items;
    }

    /**
     * Performs POST to mdwiki API and returns decoded JSON array (or empty array).
     *
     * @param array $params
     * @return array
     */
    private function getMdwikiUrlsWithParams(array $params): array
    {
        $out = $this->postUrlsMdwiki($params);
        $result = json_decode($out, true);
        if (!is_array($result)) {
            $result = [];
        }
        return $result;
    }

    /**
     * Minimal curl POST helper (based on original post_urls_mdwiki).
     *
     * @param array $params
     * @return string raw output (JSON string) or empty string on failure
     */
    private function postUrlsMdwiki(array $params = []): string
    {
        $usrAgent = "WikiProjectMed Translation Dashboard/1.0 (https://mdwiki.toolforge.org/; tools.mdwiki@toolforge.org)";
        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => $this->endPoint,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => http_build_query($params, '', '&', PHP_QUERY_RFC3986),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_USERAGENT => $usrAgent,
            CURLOPT_CONNECTTIMEOUT => $this->connectTimeout,
            CURLOPT_TIMEOUT => $this->timeout,
        ]);
        $output = curl_exec($ch);

        $url = "{$this->endPoint}?" . http_build_query($params, '', '&', PHP_QUERY_RFC3986);
        $url2 = str_replace('&format=json', '', $url);
        $url2 = "<a target='_blank' href='$url2'>$url2</a>";

        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        if ($httpCode !== 200) {
            $this->log('postUrlsMdwiki: Error: API request failed with status code ' . $httpCode);
        }
        $this->log("postUrlsMdwiki: (http_code: $httpCode) $url2");

        if ($output === false) {
            $this->log("postUrlsMdwiki: cURL Error: " . curl_error($ch));
            $output = '';
        }
        if (curl_errno($ch)) {
            $this->log('postUrlsMdwiki: Error:' . curl_error($ch));
        }
        curl_close($ch);
        return (string)$output;
    }

    /**
     * Read categories from a JSON "cache" file using injected callable or default openTablesFile.
     *
     * @param string $cat
     * @return array filtered titles (Category:, File:, Template:, User: excluded unless with_Category true)
     */
    public function getCatsFromCache(string $cat): array
    {
        if (!empty($this->options['nocache'])) {
            return [];
        }

        $newList = $this->openTablesFile($cat) ?? [];

        if (empty($newList)) {
            return [];
        }

        if (!isset($newList['list']) || !is_array($newList['list'])) {
            $this->log("Invalid format in JSON file cats_cash/$cat.json");
            return [];
        }

        return $this->titlesFilters($newList['list'], true);
    }

    /**
     * Default open_tables_file implementation (mirrors your original behavior).
     *
     * @param string $path
     * @param bool $echo
     * @return array
     */
    private function openTablesFile(string $cat): array
    {
        if (empty($this->tablesDir)) {
            return [];
        }
        // Sanitize category name to prevent path traversal
        $cat = str_replace(['/', '\\', '..'], '', $cat);

        $filePath = "{$this->tablesDir}/cats_cash/$cat.json";

        if (!is_file($filePath)) {
            $this->log("---- openTablesFile: file $filePath does not exist");
            return [];
        }

        $contents = file_get_contents($filePath);
        if ($contents === false) {
            $this->log("---- Failed to read file contents from $filePath");
            return [];
        }

        $result = json_decode($contents, true);
        if ($result === null || $result === false) {
            $this->log("---- Failed to decode JSON from $filePath");
            $result = [];
        } else {
            $len = $result['list'] ? count($result['list']) : count($result);
            $this->log("---- openTablesFile File: $filePath: Exists size: $len");
        }
        return $result;
    }

    /**
     * Filter titles: remove File:, Template:, User: (and optionally Category:) and disambiguation pages.
     *
     * @param array $titles
     * @param bool $withCategory whether to treat Category: as excluded like others
     * @return array
     */
    private function titlesFilters(array $titles, bool $withCategory = false): array
    {
        $regline = $withCategory ? '/^(Category|File|Template|User):/' : '/^(File|Template|User):/';

        return array_values(array_filter($titles, function ($title) use ($regline) {
            if (!is_string($title)) {
                return false;
            }
            if (preg_match($regline, $title)) {
                return false;
            }
            if (preg_match('/\(disambiguation\)$/', $title)) {
                return false;
            }
            return true;
        }));
    }

    /**
     * Starts-with helper (safe).
     *
     * @param string $haystack
     * @param string $needle
     * @return bool
     */
    private function startsWith(string $haystack, string $needle): bool
    {
        return strpos($haystack, $needle) === 0;
    }

    /**
     * Internal logger replacement for tests_print.
     *
     * @param mixed $s
     * @return void
     */
    private function log($s): void
    {
        if (!$this->debug) {
            return;
        }
        if (is_string($s)) {
            echo "\n<br>\n" . $s;
        } else {
            echo "\n<br>\n";
            print_r($s);
        }
    }
}

function make_options(): array
{
    $nocache = filter_var($_GET['nocache'] ?? false, FILTER_VALIDATE_BOOLEAN);

    $debug = (isset($_REQUEST['test']) || isset($_COOKIE['test'])) ? true : false;

    if (isset($_COOKIE['test']) && $_COOKIE['test'] == 'x') {
        $debug = false;
    }

    $tablesDir = getenv("HOME") . '/public_html/td/Tables';

    if (substr(__DIR__, 0, 2) == 'I:') {
        $tablesDir = 'I:/mdwiki/mdwiki/public_html/td/Tables';
    }

    $options = [
        'nocache' => $nocache,
        'debug' => $debug,
        'tablesDir' => $tablesDir,
    ];

    return $options;
}


/**
 * Procedural wrapper to use CategoryFetcher.
 *
 * @param string $cat
 * @param int    $depth
 * @param bool   $use_cache
 * @return array
 */

function get_mdwiki_cat_members(string $cat, int $depth = 0, bool $use_cache = true): array
{
    $endPoint = 'https://mdwiki.org/w/api.php';

    $options = make_options();

    $fetcher = new CategoryFetcher($options, $endPoint);

    return $fetcher->getMdwikiCatMembers($cat, $depth, $use_cache);
}

function get_cats_from_cache($cat)
{
    static $data = [];
    // ---
    if (!empty($data[$cat] ?? [])) {
        return $data[$cat];
    }
    // ---
    $endPoint = 'https://mdwiki.org/w/api.php';

    $options = make_options();

    $fetcher = new CategoryFetcher($options, $endPoint);

    $result = $fetcher->getCatsFromCache($cat);

    $data[$cat] = $result;

    return $result;
}
