**Complete restructured code for `results_2026`**

All comments are in English.
The code keeps the same public behavior while being cleaner, typed, and better organized.

### Directory structure

```
src/app/backend/results_27/
├── Data/
│   └── ResultsFetcher.php
├── Helpers/
│   ├── CardRenderer.php
│   └── TranslateTypeLoader.php
├── Tables/
│   ├── AbstractResultsTable.php
│   ├── MissingTable.php
│   ├── ExistsTable.php
│   └── InProcessTable.php
├── Rows/
│   ├── MissingRowBuilder.php
│   ├── ExistsRowBuilder.php
│   └── InProcessRowBuilder.php
├── ResultsLoader.php          ← Main entry point
├── get_results_27.php       ← Backward-compatible wrapper
└── include.php
```

---

### 1. `Data/ResultsFetcher.php`

```php
<?php

namespace Results\GetResults27\Data;

use function TD\Render\Html\make_mdwiki_cat_url;
use function SQLorAPI\Funcs\get_lang_pages_by_cat;
use function SQLorAPI\Process\get_lang_in_process;
use function SQLorAPI\Funcs\missing_by_lang_and_category;
use function SQLorAPI\Funcs\exists_by_lang_and_category;
use function TD\Render\TestPrint\test_print;

/**
 * Responsible for fetching and preparing all result data
 * (exists, missing, in-process) for a given category and language.
 */
class ResultsFetcher
{
    private bool $debug;

    public function __construct(bool $debug = false)
    {
        $this->debug = $debug;
    }

    /**
     * Main method: returns structured results.
     *
     * @return array{
     *     ix: string,
     *     inprocess: array,
     *     exists: array,
     *     missing: array
     * }
     */
    public function get(string $cat, string $code): array
    {
        // Pages that already exist via Translation Dashboard
        $existsViaTd = get_lang_pages_by_cat($code, $cat);
        $existsViaTd = array_column($existsViaTd, null, "title");
        $this->log("exists_via_td", count($existsViaTd));

        // Missing pages
        $itemsMissing = missing_by_lang_and_category($code, $cat);
        $this->log("Items missing", count($itemsMissing));

        // Existing pages
        $itemsExists = exists_by_lang_and_category($code, $cat);
        $itemsExists = array_column($itemsExists, null, "title");

        // Mark origin of each existing page
        foreach ($itemsExists as $title => &$item) {
            $item["via"] = isset($existsViaTd[$title]) ? "td" : "before";
        }
        unset($item);

        $this->log("Items exists", count($itemsExists));

        $lenExists = count($itemsExists);

        // In-process items that are still in the missing list
        $missingTitles = array_column($itemsMissing, "title");
        $inProcess = $this->getInProcess($missingTitles, $code);

        // Remove in-process titles from the missing list
        $missing = $itemsMissing;
        if (!empty($inProcess)) {
            $inProcessTitles = array_flip(array_column($inProcess, "title"));
            $missing = array_filter($itemsMissing, static function (array $item) use ($inProcessTitles): bool {
                return !isset($inProcessTitles[$item["title"]]);
            });
        }

        $summary = $this->createSummary(
            $code,
            $cat,
            count($inProcess),
            count($missing),
            $lenExists
        );

        // Sort existing pages by title
        ksort($itemsExists);

        return [
            "ix"        => $summary,
            "inprocess" => $inProcess,
            "exists"    => $itemsExists,
            "missing"   => array_values($missing),
        ];
    }

    /**
     * Filter in-process records that belong to the current missing list.
     */
    private function getInProcess(array $missingTitles, string $code): array
    {
        $res = get_lang_in_process($code);
        $result = [];

        foreach ($res as $row) {
            if (in_array($row["title"], $missingTitles, true)) {
                $result[$row["title"]] = $row;
            }
        }

        return $result;
    }

    /**
     * Build the human-readable summary string.
     */
    private function createSummary(
        string $code,
        string $cat,
        int $lenInProcess,
        int $lenMissing,
        int $lenExists
    ): string {
        $total  = $lenExists + $lenMissing + $lenInProcess;
        $catUrl = make_mdwiki_cat_url($cat, "Category");

        return sprintf(
            "Found %d pages in %s, %d exists, and %d missing in (<a href="https://%s.wikipedia.org" target="_blank">%s</a>), %d In process.",
            $total,
            $catUrl,
            $lenExists,
            $lenMissing,
            $code,
            $code,
            $lenInProcess
        );
    }

    private function log(string $message, $value = null): void
    {
        if (!$this->debug) {
            return;
        }

        $text = $value !== null ? "{$message}: {$value}" : $message;
        test_print($text);
    }
}
```

---

### 2. `Helpers/TranslateTypeLoader.php`

```php
<?php

namespace Results\GetResults27\Helpers;

use function SQLorAPI\GetDataTab\get_td_or_sql_translate_type;

/**
 * Loads and caches lists of titles that require full translation
 * or do not allow lead-only translation.
 */
class TranslateTypeLoader
{
    private static array $fullTranslates = [];
    private static array $noLeadTranslates = [];
    private static bool $loaded = false;

    /**
     * @param string $type  "full" or "no"
     * @return string[]
     */
    public static function load(string $type): array
    {
        if (!self::$loaded) {
            self::loadData();
        }

        return $type === "full" ? self::$fullTranslates : self::$noLeadTranslates;
    }

    private static function loadData(): void
    {
        $rows = get_td_or_sql_translate_type();

        foreach ($rows as $tab) {
            if (($tab["tt_full"] ?? 0) == 1) {
                self::$fullTranslates[] = $tab["tt_title"];
            }
            if (($tab["tt_lead"] ?? 1) == 0) {
                self::$noLeadTranslates[] = $tab["tt_title"];
            }
        }

        self::$loaded = true;
    }
}
```

---

### 3. `Helpers/CardRenderer.php`

```php
<?php

namespace Results\GetResults27\Helpers;

/**
 * Renders a Bootstrap card used for result sections.
 */
class CardRenderer
{
    public static function render(string $title, string $body, string $extraHeader = ''): string
    {
        return <<<HTML
        <br>
        <div class="card">
            <div class="card-header">
                <span class="card-title h5">{$title}</span>
                {$extraHeader}
                <div class="card-tools">
                    <button type="button" class="btn-tool" data-card-widget="collapse">
                        <i class="fas fa-minus"></i>
                    </button>
                </div>
            </div>
            <div class="card-body1 card2">
                {$body}
            </div>
        </div>
        HTML;
    }
}
```

---

### 4. `Tables/AbstractResultsTable.php`

```php
<?php

namespace Results\GetResults27\Tables;

use function Results\ResultsTableHtml\make_table_start;

/**
 * Base class for all result tables.
 */
abstract class AbstractResultsTable
{
    protected function startTable(bool $isInProcess = false, bool $showInProgressButton = false): string
    {
        return make_table_start($isInProcess, $showInProgressButton);
    }

    protected function endTable(): string
    {
        return "</tbody></table>";
    }

    abstract public function render(array $items): string;
}
```

---

### 5. `Rows/MissingRowBuilder.php`

```php
<?php

namespace Results\GetResults27\Rows;

use function TD\Render\Html\make_mdwiki_href;
use function TD\Render\Html\make_wikidata_url_blank;
use function Results\TrLink\make_tr_link_medwiki;

/**
 * Builds a single row for the Missing results table.
 */
class MissingRowBuilder
{
    public function build(
        string $title,
        string $traType,
        int $counter,
        string $langCode,
        string $cat,
        string $camp,
        bool $isFullRow,
        bool $fullTrUser,
        ?string $globalUsername,
        array $titleData
    ): string {
        if (empty($traType)) {
            $traType = "lead";
        }

        $isVideo = str_starts_with(strtolower($title), "video:");
        if ($isVideo) {
            $traType = "all";
        }

        $words    = $titleData["w_lead_words"] ?? 0;
        $refs     = $titleData["r_lead_refs"] ?? 0;
        $importance = $titleData["importance"] ?? "Unknown";
        $enViews  = $titleData["en_views"] ?? '';
        $qid      = $titleData["qid"] ?? '';

        if ($traType === "all") {
            $words = $titleData["w_all_words"] ?? 0;
            $refs  = $titleData["r_all_refs"] ?? 0;
        }

        if (empty($importance)) {
            $importance = "Unknown";
        }

        $qidUrl    = make_wikidata_url_blank($qid);
        $mdwikiUrl = make_mdwiki_href($title);

        // Translate buttons
        if (empty($globalUsername)) {
            $buttons = <<<HTML
                <a role="button" class="btn btn-outline-primary" href="/auth/login.php">
                    <i class="fas fa-sign-in-alt fa-sm fa-fw mr-1"></i>
                    <span class="navtitles">Login</span>
                </a>
            HTML;
        } else {
            $fullUrl = make_tr_link_medwiki($title, $langCode, $cat, $camp, "all", $words);
            $leadUrl = make_tr_link_medwiki($title, $langCode, $cat, $camp, $traType, $words);

            if ($fullTrUser && !$isVideo) {
                $buttons = <<<HTML
                    <div class="inline">
                        <a href="{$leadUrl}" class="btn btn-outline-primary btn-sm" target="_blank">Lead</a>
                        <a href="{$fullUrl}" class="btn btn-outline-primary btn-sm" target="_blank">Full</a>
                    </div>
                HTML;
            } else {
                $buttons = "<a href=\"{$leadUrl}\" class=\"btn btn-outline-primary btn-sm\" target=\"_blank\">Translate</a>";
            }
        }

        $displayCounter = ($isFullRow && !$isVideo) ? "{$counter}.Full" : $counter;

        return <<<HTML
        <tr>
            <th class="num" scope="row">{$displayCounter}</th>
            <td class="link_container">
                <a target="_blank" href="{$mdwikiUrl}">{$title}</a>
            </td>
            <th>{$buttons}</th>
            <td class="num" style="text-align:left">{$enViews}</td>
            <td class="num" style="text-align:left">{$importance}</td>
            <td class="num" style="text-align:left">{$words}</td>
            <td class="num" style="text-align:left">{$refs}</td>
            <td>{$qidUrl}</td>
        </tr>
        HTML;
    }
}
```

---

### 6. `Tables/MissingTable.php`

```php
<?php

namespace Results\GetResults27\Tables;

use Results\GetResults27\Rows\MissingRowBuilder;

/**
 * Renders the table of missing pages.
 */
class MissingTable extends AbstractResultsTable
{
    private MissingRowBuilder $rowBuilder;
    private string $langCode;
    private string $cat;
    private string $camp;
    private string $traType;
    private bool $fullTrUser;
    private ?string $globalUsername;
    private array $noLeadTranslates;
    private array $fullTranslates;

    public function __construct(
        string $langCode,
        string $cat,
        string $camp,
        string $traType,
        bool $fullTrUser,
        ?string $globalUsername,
        array $noLeadTranslates,
        array $fullTranslates
    ) {
        $this->rowBuilder       = new MissingRowBuilder();
        $this->langCode         = $langCode;
        $this->cat              = $cat;
        $this->camp             = $camp;
        $this->traType          = $traType;
        $this->fullTrUser       = $fullTrUser;
        $this->globalUsername   = $globalUsername;
        $this->noLeadTranslates = $noLeadTranslates;
        $this->fullTranslates   = $fullTranslates;
    }

    public function render(array $items): string
    {
        $doFull = ($this->traType !== "all");

        // Sort by English page views (descending)
        usort($items, static function (array $a, array $b): int {
            return ($b["en_views"] ?? 0) <=> ($a["en_views"] ?? 0);
        });

        $items = array_column($items, null, "title");

        $html = $this->startTable(false, false);
        $counter = 1;

        foreach ($items as $title => $titleData) {
            if (empty($title)) {
                continue;
            }

            $title = str_replace("_", " ", $title);

            $row = $this->rowBuilder->build(
                $title,
                $this->traType,
                $counter,
                $this->langCode,
                $this->cat,
                $this->camp,
                false,
                $this->fullTrUser,
                $this->globalUsername,
                $titleData
            );

            // Special handling when full translation is restricted
            if (!$doFull || $this->fullTrUser) {
                $html .= $row;
                $counter++;
                continue;
            }

            $noLead = in_array($title, $this->noLeadTranslates, true);
            $full   = in_array($title, $this->fullTranslates, true);

            if ($noLead && !$full) {
                continue;
            }

            if (!$noLead) {
                $html .= $row;
            }

            if ($full) {
                $html .= $this->rowBuilder->build(
                    $title,
                    "all",
                    $counter,
                    $this->langCode,
                    $this->cat,
                    $this->camp,
                    true,
                    $this->fullTrUser,
                    $this->globalUsername,
                    $titleData
                );
            }

            $counter++;
        }

        $html .= $this->endTable();
        return $html;
    }
}
```

---

### 7. `Rows/ExistsRowBuilder.php`

```php
<?php

namespace Results\GetResults27\Rows;

use function Results\TrLink\make_ContentTranslation_url;
use function TD\Render\Html\make_mdwiki_article_url_blank;
use function TD\Render\Html\make_wikipedia_url_blank;
use function TD\Render\Html\make_wikidata_url_blank;

/**
 * Builds a single row for the Exists results table.
 */
class ExistsRowBuilder
{
    public function build(
        string $title,
        int $counter,
        string $langCode,
        string $cat,
        string $camp,
        array $titleData,
        ?string $globalUsername,
        bool $userCoord,
        string $endpoint
    ): string {
        $importance = $titleData["importance"] ?? "Unknown";
        $qid        = $titleData["qid"] ?? '';
        $target     = $titleData["target"] ?? '';
        $via        = $titleData["via"] ?? "before";

        $mdwikiLink = make_mdwiki_article_url_blank($title);
        $qidUrl     = make_wikidata_url_blank($qid);

        $targetTd  = '';
        $targetTd2 = '';

        if ($target) {
            if ($via === "td") {
                $targetTd = make_wikipedia_url_blank($target, $langCode);
            } else {
                $targetTd2 = make_wikipedia_url_blank($target, $langCode);
            }
        }

        $translateButton = '';
        if (!empty($globalUsername) && $userCoord) {
            $translateUrl = make_ContentTranslation_url(
                $title,
                $langCode,
                $cat,
                $camp,
                "lead",
                $endpoint
            );
            $translateButton = <<<HTML
                <div class="inline">
                    <a href="{$translateUrl}" class="btn btn-outline-primary btn-sm" target="_blank">Translate</a>
                </div>
            HTML;
        }

        return <<<HTML
        <tr>
            <th class="" scope="row" style="text-align:center">{$counter}</th>
            <td class="link_container spannowrap">{$mdwikiLink}</td>
            <td>{$translateButton}</td>
            <td>{$targetTd}</td>
            <td>{$targetTd2}</td>
            <td>{$qidUrl}</td>
        </tr>
        HTML;
    }
}
```

---

### 8. `Tables/ExistsTable.php`

```php
<?php

namespace Results\GetResults27\Tables;

use Results\GetResults27\Rows\ExistsRowBuilder;

/**
 * Renders the table of already existing pages.
 */
class ExistsTable extends AbstractResultsTable
{
    private ExistsRowBuilder $rowBuilder;
    private string $langCode;
    private string $cat;
    private string $camp;
    private ?string $globalUsername;
    private bool $userCoord;
    private string $endpoint;

    public function __construct(
        string $langCode,
        string $cat,
        string $camp,
        ?string $globalUsername,
        bool $userCoord,
        string $endpoint
    ) {
        $this->rowBuilder     = new ExistsRowBuilder();
        $this->langCode       = $langCode;
        $this->cat            = $cat;
        $this->camp           = $camp;
        $this->globalUsername = $globalUsername;
        $this->userCoord      = $userCoord;
        $this->endpoint       = $endpoint;
    }

    public function render(array $items): string
    {
        $countTranslated       = 0;
        $countTranslatedBefore = 0;
        $rowsHtml              = '';
        $counter               = 1;

        foreach ($items as $title => $data) {
            if (empty($title)) {
                continue;
            }

            $title = str_replace("_", " ", $title);

            if (($data["via"] ?? '") === "td') {
                $countTranslated++;
            } else {
                $countTranslatedBefore++;
            }

            $rowsHtml .= $this->rowBuilder->build(
                $title,
                $counter,
                $this->langCode,
                $this->cat,
                $this->camp,
                $data,
                $this->globalUsername,
                $this->userCoord,
                $this->endpoint
            );

            $counter++;
        }

        return <<<HTML
        <table class="table compact table-striped table_100 table_text_left table_responsive display">
            <thead>
                <tr>
                    <th class="num">#</th>
                    <th class="spannowrap" style="text-align:center">Title</th>
                    <th>Translate</th>
                    <th>Translated ({$countTranslated})</th>
                    <th>Translated before ({$countTranslatedBefore})</th>
                    <th class="spannowrap" style="text-align:center">
                        <span data-bs-toggle="tooltip" data-bs-title="Wikidata identifier">Qid</span>
                    </th>
                </tr>
            </thead>
            <tbody>
                {$rowsHtml}
            </tbody>
        </table>
        HTML;
    }
}
```

---

### 9. `Rows/InProcessRowBuilder.php`

```php
<?php

namespace Results\GetResults27\Rows;

use function TD\Render\Html\make_mdwiki_href;
use function TD\Render\Html\make_wikidata_url_blank;
use function Results\Helps\make_translate_urls;

/**
 * Builds a single row for the In-process results table.
 */
class InProcessRowBuilder
{
    public function build(
        string $title,
        string $traType,
        int $counter,
        string $langCode,
        string $cat,
        string $camp,
        array $inProcessData,
        bool $inProgressButton,
        bool $isFullRow,
        bool $fullTrUser,
        ?string $globalUsername,
        array $titleData,
        string $endpoint,
        bool $userCoord
    ): string {
        $user = $inProcessData["user"] ?? '';
        $date = $inProcessData["date"] ?? $inProcessData["add_date"] ?? '';

        $words      = $titleData["w_lead_words"] ?? 0;
        $refs       = $titleData["r_lead_refs"] ?? 0;
        $importance = $titleData["importance"] ?? "Unknown";
        $enViews    = $titleData["en_views"] ?? '';
        $qid        = $titleData["qid"] ?? '';

        if ($traType === "all") {
            $words = $titleData["w_all_words"] ?? 0;
            $refs  = $titleData["r_all_refs"] ?? 0;
        }

        if (empty($importance)) {
            $importance = "Unknown";
        }

        $qidUrl    = make_wikidata_url_blank($qid);
        $mdwikiUrl = make_mdwiki_href($title);

        $loginUserIsTranslator = (!empty($globalUsername) && $user === $globalUsername) || $userCoord;

        [$buttons] = make_translate_urls(
            $title,
            $traType,
            $words,
            $langCode,
            $cat,
            $camp,
            true,
            $inProgressButton,
            $user,
            $fullTrUser,
            $loginUserIsTranslator,
            $endpoint
        );

        // Keep only the date part if datetime is present
        if (str_contains($date, ":")) {
            $date = explode(" ", $date)[0];
        }

        if (empty($globalUsername)) {
            $buttons = '';
        }

        $displayCounter = ($isFullRow && !str_starts_with(strtolower($title), "video:"))
            ? "{$counter}.Full"
            : $counter;

        return <<<HTML
        <tr>
            <th class="num" scope="row">{$displayCounter}</th>
            <td class="link_container">
                <a target="_blank" href="{$mdwikiUrl}">{$title}</a>
            </td>
            <th>{$buttons}</th>
            <td style="text-align:center">{$traType}</td>
            <td class="num" style="text-align:left">{$enViews}</td>
            <td class="num" style="text-align:left">{$importance}</td>
            <td class="num" style="text-align:left">{$words}</td>
            <td class="num" style="text-align:left">{$refs}</td>
            <td>{$qidUrl}</td>
            <td>{$user}</td>
            <td>{$date}</td>
        </tr>
        HTML;
    }
}
```

---

### 10. `Tables/InProcessTable.php`

```php
<?php

namespace Results\GetResults27\Tables;

use Results\GetResults27\Rows\InProcessRowBuilder;

/**
 * Renders the table of pages currently being translated.
 */
class InProcessTable extends AbstractResultsTable
{
    private InProcessRowBuilder $rowBuilder;
    private string $langCode;
    private string $cat;
    private string $camp;
    private bool $inProgressButton;
    private bool $fullTrUser;
    private ?string $globalUsername;
    private array $titlesInfos;
    private string $endpoint;
    private bool $userCoord;

    public function __construct(
        string $langCode,
        string $cat,
        string $camp,
        bool $inProgressButton,
        bool $fullTrUser,
        ?string $globalUsername,
        array $titlesInfos,
        string $endpoint,
        bool $userCoord
    ) {
        $this->rowBuilder       = new InProcessRowBuilder();
        $this->langCode         = $langCode;
        $this->cat              = $cat;
        $this->camp             = $camp;
        $this->inProgressButton = $inProgressButton;
        $this->fullTrUser       = $fullTrUser;
        $this->globalUsername   = $globalUsername;
        $this->titlesInfos      = $titlesInfos;
        $this->endpoint         = $endpoint;
        $this->userCoord        = $userCoord;
    }

    public function render(array $items): string
    {
        $html = $this->startTable(true, $this->inProgressButton);
        $counter = 1;

        foreach ($items as $title => $inProcessData) {
            if (empty($title)) {
                continue;
            }

            $title = str_replace("_", " ", $title);
            $titleData = $this->titlesInfos[$title] ?? [];

            $traType = $inProcessData["translate_type"] ?? "lead";
            $isFull  = false;

            if (str_starts_with(strtolower($title), "video:")) {
                $traType = "all";
                $isFull  = true;
            }

            $html .= $this->rowBuilder->build(
                $title,
                $traType,
                $counter,
                $this->langCode,
                $this->cat,
                $this->camp,
                $inProcessData,
                $this->inProgressButton,
                $isFull,
                $this->fullTrUser,
                $this->globalUsername,
                $titleData,
                $this->endpoint,
                $this->userCoord
            );

            $counter++;
        }

        $html .= $this->endTable();
        return $html;
    }
}
```

---

### 11. Main entry point – `ResultsLoader.php`

```php
<?php

namespace Results\GetResults27;

use Results\GetResults27\Data\ResultsFetcher;
use Results\GetResults27\Helpers\CardRenderer;
use Results\GetResults27\Helpers\TranslateTypeLoader;
use Results\GetResults27\Tables\MissingTable;
use Results\GetResults27\Tables\ExistsTable;
use Results\GetResults27\Tables\InProcessTable;

use function SQLorAPI\GetDataTab\get_td_or_sql_full_translators;
use function SQLorAPI\GetDataTab\get_td_or_sql_titles_infos;
use function SQLorAPI\GetDataTab\get_endpoint;

/**
 * Main entry point for the 2026 results module.
 */
class ResultsLoader
{
    /**
     * Load and render the complete results page.
     */
    public function load(array $data): string
    {
        $camp         = $data["camp"] ?? '';
        $code         = $data["code"] ?? '';
        $cat          = $data["cat"] ?? '';
        $showExists   = (bool)($data["show_exists"] ?? false);
        $globalUser   = $data["global_username"] ?? null;
        $inProgressButton = (bool)($data["in_progress_translation_button"] ?? false);
        $traType      = $data["tra_type"] ?? "lead";
        $userCoord    = (bool)($data["user_coord"] ?? false);
        $test         = !empty($data["test"]);

        // Full translator check
        $fullTranslators = get_td_or_sql_full_translators();
        $fullTranslators = array_column($fullTranslators, "is_active", "user");
        $fullTrUser = ($fullTranslators[$globalUser] ?? 0) == 1;

        // Fetch data
        $fetcher = new ResultsFetcher($test);
        $results = $fetcher->get($cat, $code);

        // Helper data
        $titlesInfos     = array_column(get_td_or_sql_titles_infos(), null, "title");
        $noLeadTranslates = TranslateTypeLoader::load("no");
        $fullTranslates   = TranslateTypeLoader::load("full");
        $endpoint         = get_endpoint();

        $html = '';

        if ($test) {
            $html .= "code:{$code}<br>code_lang_name:" . ($data["code_lang_name"] ?? '') . "<br>";
        }

        // ----- Missing table -----
        $missingTable = new MissingTable(
            $code,
            $cat,
            $camp,
            $traType,
            $fullTrUser,
            $globalUser,
            $noLeadTranslates,
            $fullTranslates
        );

        $missingHtml = $missingTable->render($results["missing"]);
        $resLine = " Results: (" . count($results["missing"]) . ")";
        if ($test) {
            $resLine .= " test:";
        }

        $html .= CardRenderer::render($resLine, $missingHtml, $results["ix"]);

        // ----- In-process table -----
        $lenInProcess = count($results["inprocess"]);
        if ($lenInProcess > 0) {
            $inProcessTable = new InProcessTable(
                $code,
                $cat,
                $camp,
                $inProgressButton,
                $fullTrUser,
                $globalUser,
                $titlesInfos,
                $endpoint,
                $userCoord
            );

            $html .= CardRenderer::render(
                "In process: ({$lenInProcess})",
                $inProcessTable->render($results["inprocess"])
            );
        }

        // ----- Exists table -----
        $lenExists = count($results["exists"]);
        if ($lenExists > 1 && $showExists) {
            $existsTable = new ExistsTable(
                $code,
                $cat,
                $camp,
                $globalUser,
                $userCoord,
                $endpoint
            );

            $html .= CardRenderer::render(
                "Exists: ({$lenExists})",
                $existsTable->render($results["exists"])
            );
        }

        return $html;
    }
}
```

---

### 12. Backward-compatible wrappers

**`get_results_27.php`**

```php
<?php

namespace Results\GetResults27;

use Results\GetResults27\Data\ResultsFetcher;

/**
 * Backward-compatible function.
 */
function get_results_27(string $cat, string $code, bool $debug = false): array
{
    return (new ResultsFetcher($debug))->get($cat, $code);
}
```

**`index.php` (new clean version)**

```php
<?php

namespace Results\GetResults27;

/**
 * Public entry point – keeps the old function name.
 */
function results_loader_27(array $data): string
{
    return (new ResultsLoader())->load($data);
}
```

---

### 13. `include.php`

```php
<?php

// Data layer
include_once __DIR__ . "/Data/ResultsFetcher.php";

// Helpers
include_once __DIR__ . "/Helpers/CardRenderer.php";
include_once __DIR__ . "/Helpers/TranslateTypeLoader.php";

// Rows
include_once __DIR__ . "/Rows/MissingRowBuilder.php";
include_once __DIR__ . "/Rows/ExistsRowBuilder.php";
include_once __DIR__ . "/Rows/InProcessRowBuilder.php";

// Tables
include_once __DIR__ . "/Tables/AbstractResultsTable.php";
include_once __DIR__ . "/Tables/MissingTable.php";
include_once __DIR__ . "/Tables/ExistsTable.php";
include_once __DIR__ . "/Tables/InProcessTable.php";

// Main loader
include_once __DIR__ . "/ResultsLoader.php";
include_once __DIR__ . "/get_results_27.php";
include_once __DIR__ . "/index.php";
```

---
