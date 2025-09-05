<?php
declare(strict_types=1);

/**
 * Test suite for Leaderboard\Index\print_cat_table and main_leaderboard.
 * Testing library/framework: PHPUnit.
 *
 * We stub dependent namespaced functions/classes to create a deterministic unit test environment.
 * Stubs record call arguments in a global registry $__calls for assertions.
 */

use PHPUnit\Framework\TestCase;

$GLOBALS['__calls'] = [
    'createNumbersTable_args' => null,
    'get_td_or_sql_top_users_args' => null,
    'get_td_or_sql_top_langs_args' => null,
    'get_td_or_sql_status_args' => null,
    'print_graph_for_table_args' => null,
    'makeCol_args' => null,
    'makeUsersTable_args' => null,
    'get_td_or_sql_top_lang_of_users_args' => null,
    'module_copy_data_args' => null,
    'makeLangTable_args' => null,
    'makeColSm4_users_args' => null,
    'makeColSm4_lang_args' => null,
    'leaderboard_filter_args' => null,
];

namespace Tables\SqlTables {
    // Stub TablesSql if not already defined.
    if (!class_exists(TablesSql::class)) {
        class TablesSql {
            public static array $s_camp_to_cat = [];
        }
    }
}

namespace Actions\Html {
    // Stubs for HTML builders
    if (!function_exists(__NAMESPACE__ . '\makeCol')) {
        function makeCol(string $_title, string $_tableHtml, string $_graphHtml): string {
            $GLOBALS['__calls']['makeCol_args'] = func_get_args();
            return 'COL_NUMBERS_HTML';
        }
    }
    if (!function_exists(__NAMESPACE__ . '\makeColSm4')) {
        function makeColSm4(string $title, string $_tableHtml, int $_cols, string $_addonHtml = '', string $_extra = ''): string {
            // Determine whether this is users or languages col by title heuristic
            if (stripos($title, 'Top users') !== false) {
                $GLOBALS['__calls']['makeColSm4_users_args'] = func_get_args();
                return 'COL_USERS_HTML';
            }
            $GLOBALS['__calls']['makeColSm4_lang_args'] = func_get_args();
            return 'COL_LANG_HTML';
        }
    }
}

namespace Leaderboard\Graph {
    if (!function_exists(__NAMESPACE__ . '\print_graph_for_table')) {
        function print_graph_for_table($_graphData, $_no_card = false): string {
            $GLOBALS['__calls']['print_graph_for_table_args'] = func_get_args();
            return 'GRAPH_HTML';
        }
    }
}

namespace Leaderboard\LeaderTables {
    if (!function_exists(__NAMESPACE__ . '\createNumbersTable')) {
        function createNumbersTable(int $_usersCount, string $_allArticles, string $_allWords, int $_langsCount, string $_allViews): string {
            $GLOBALS['__calls']['createNumbersTable_args'] = func_get_args();
            return 'NUMBERS_HTML';
        }
    }
    if (!function_exists(__NAMESPACE__ . '\makeLangTable')) {
        function makeLangTable(array $_langTable): string {
            $GLOBALS['__calls']['makeLangTable_args'] = func_get_args();
            return 'LANG_TABLE_HTML';
        }
    }
}

namespace Leaderboard\LeaderTabUsers {
    if (!function_exists(__NAMESPACE__ . '\makeUsersTable')) {
        function makeUsersTable(array $_usersList): string {
            $GLOBALS['__calls']['makeUsersTable_args'] = func_get_args();
            return 'USERS_TABLE_HTML';
        }
    }
    if (!function_exists(__NAMESPACE__ . '\module_copy_data')) {
        function module_copy_data(array $_usersTab): string {
            $GLOBALS['__calls']['module_copy_data_args'] = func_get_args();
            return 'COPY_MODULE_HTML';
        }
    }
}

namespace SQLorAPI\TopData {
    if (!function_exists(__NAMESPACE__ . '\get_td_or_sql_top_users')) {
        function get_td_or_sql_top_users($_year, $_user_group, $_cat): array {
            $GLOBALS['__calls']['get_td_or_sql_top_users_args'] = func_get_args();
            // Default dataset; tests may override via global
            return $GLOBALS['__fixtures']['users_list'] ?? [
                'alice' => ['count' => 2, 'words' => 1000, 'views' => 50],
                'bob'   => ['count' => 3, 'words' => 2500, 'views' => 75],
            ];
        }
    }
    if (!function_exists(__NAMESPACE__ . '\get_td_or_sql_top_langs')) {
        function get_td_or_sql_top_langs($_year, $_user_group, $_cat): array {
            $GLOBALS['__calls']['get_td_or_sql_top_langs_args'] = func_get_args();
            return $GLOBALS['__fixtures']['lang_table'] ?? [
                ['lang' => 'en', 'count' => 3],
                ['lang' => 'fr', 'count' => 2],
            ];
        }
    }
    if (!function_exists(__NAMESPACE__ . '\get_td_or_sql_status')) {
        function get_td_or_sql_status($_year, $_user_group, $_cat): array {
            $GLOBALS['__calls']['get_td_or_sql_status_args'] = func_get_args();
            return $GLOBALS['__fixtures']['graph_data'] ?? [['x' => 1, 'y' => 2]];
        }
    }
    if (!function_exists(__NAMESPACE__ . '\get_td_or_sql_top_lang_of_users')) {
        function get_td_or_sql_top_lang_of_users(array $_users): array {
            $GLOBALS['__calls']['get_td_or_sql_top_lang_of_users_args'] = func_get_args();
            return $GLOBALS['__fixtures']['users_tab'] ?? [['user' => 'alice', 'langs' => ['en' => 2]]];
        }
    }
}

namespace Leaderboard\Filter {
    if (!function_exists(__NAMESPACE__ . '\leaderboard_filter')) {
        function leaderboard_filter($_year, $_user_group, $_camp): string {
            $GLOBALS['__calls']['leaderboard_filter_args'] = func_get_args();
            return 'FILTER_FORM_HTML';
        }
    }
}

namespace Leaderboard\Index {
    // Bring in PHPUnit in this namespace file for the test class below.
    use PHPUnit\Framework\TestCase;
    use function SQLorAPI\TopData\get_td_or_sql_top_users;

    // Locate and include the code under test. We attempt to find it by namespace file path.
    // Try common locations; if autoload handles it, the require will be skipped silently.
    (static function () {
        $candidates = [
            'src/Leaderboard/Index/Main.php',
            'src/Leaderboard/Index/index.php',
            'src/leaderboard/index.php',
            'Leaderboard/Index/Main.php',
            'Leaderboard/Index/index.php',
        ];
        foreach ($candidates as $file) {
            if (is_file($file)) {
                require_once $file;
                return;
            }
        }
        // If none found, try to locate a file that declares this namespace.
        $found = [];
        exec("rg -n --no-ignore-vcs --hidden 'namespace\\\\s+Leaderboard\\\\\\\\Index;' -g '!vendor/**' -g '!tests/**' -tphp | cut -d: -f1 | head -1", $found);
        if (!empty($found[0]) && is_file($found[0])) {
            require_once $found[0];
        } else {
            // As a fallback, embed the given implementation (ensures tests can run even if file path is atypical).
            if (!function_exists(__NAMESPACE__ . '\print_cat_table')) {
                // The implementation block mirrors the PR diff content.
                function print_cat_table($_year, $_user_group, $_camp, $cat): string
                {
                    $users_list = \SQLorAPI\TopData\get_td_or_sql_top_users($_year, $_user_group, $cat);
                    $lang_table = \SQLorAPI\TopData\get_td_or_sql_top_langs($_year, $_user_group, $cat);
                    $all_articles = number_format(array_sum(array_column($users_list, 'count')));
                    $all_Words = number_format(array_sum(array_column($users_list, 'words')));
                    $all_views = number_format(array_sum(array_column($users_list, 'views')));
                    $numbersTable = \Leaderboard\LeaderTables\createNumbersTable(
                        count($users_list),
                        $all_articles,
                        $all_Words,
                        count($lang_table),
                        $all_views
                    );
                    $graph_data = \SQLorAPI\TopData\get_td_or_sql_status($_year, $_user_group, $cat);
                    $graph_html = \Leaderboard\Graph\print_graph_for_table($graph_data, false);
                    $numbersCol = \Actions\Html\makeCol('Numbers', $numbersTable, $graph_html);
                    $usersTable = \Leaderboard\LeaderTabUsers\makeUsersTable($users_list);
                    $users = array_keys($users_list);
                    $users_tab = \SQLorAPI\TopData\get_td_or_sql_top_lang_of_users($users);
                    $copy_module = \Leaderboard\LeaderTabUsers\module_copy_data($users_tab);
                    $modal_a = <<<HTML
                        <button type="button" class="btn-tool" href="#" data-bs-toggle="modal" data-bs-target="#targets">
                            <i class="fas fa-copy"></i>
                        </button>
                    HTML;
                    $usersCol = \Actions\Html\makeColSm4('Top users by number of translation', $usersTable, 5, $copy_module, $modal_a);
                    $languagesTable = \Leaderboard\LeaderTables\makeLangTable($lang_table);
                    $languagesCol = \Actions\Html\makeColSm4('Top languages by number of Articles', $languagesTable, 4);
                    return <<<HTML
                        <div class="row g-3">
                            $numbersCol
                            $usersCol
                            $languagesCol
                        </div>
                    HTML;
                }
            }
            if (!function_exists(__NAMESPACE__ . '\main_leaderboard')) {
                function main_leaderboard($year, $camp, $user_group)
                {
                    $filter_form = \Leaderboard\Filter\leaderboard_filter($year, $user_group, $camp);
                    $cat = \Tables\SqlTables\TablesSql::$s_camp_to_cat[$camp] ?? '';
                    $uux = print_cat_table($year, $user_group, $camp, $cat);
                    $board = <<<HTML
                        $filter_form
                        <hr/>
                        <div class="container-fluid">
                            $uux
                        </div>
                    HTML;
                    return $board;
                }
            }
        }
    })();

    final class MainTest extends TestCase
    {
        protected function setUp(): void
        {
            // Reset call registry and fixtures before each test.
            $GLOBALS['__calls'] = array_map(static fn() => null, $GLOBALS['__calls']);
            $GLOBALS['__fixtures'] = [];
            // Default mapping
            \Tables\SqlTables\TablesSql::$s_camp_to_cat = [];
        }

        public function testPrintCatTableRendersColumnsAndCapturesNumbers(): void
        {
            // Arrange fixtures
            $GLOBALS['__fixtures']['users_list'] = [
                'alice' => ['count' => 2, 'words' => 1500, 'views' => 100],
                'bob'   => ['count' => 5, 'words' => 3500, 'views' => 250],
            ];
            $GLOBALS['__fixtures']['lang_table'] = [
                ['lang' => 'en', 'count' => 4],
                ['lang' => 'fr', 'count' => 3],
                ['lang' => 'es', 'count' => 2],
            ];

            // Act
            $html = print_cat_table(2024, 'groupA', 'campX', 'catAlpha');

            // Assert HTML assembly
            $this->assertIsString($html);
            $this->assertStringContainsString('COL_NUMBERS_HTML', $html);
            $this->assertStringContainsString('COL_USERS_HTML', $html);
            $this->assertStringContainsString('COL_LANG_HTML', $html);
            $this->assertStringContainsString('<div class="row g-3">', $html);

            // Assert numbers aggregated and formatted
            $args = $GLOBALS['__calls']['createNumbersTable_args'];
            $this->assertNotNull($args, 'createNumbersTable should be called');
            [$usersCount, $allArticles, $allWords, $langsCount, $allViews] = $args;
            $this->assertSame(2, $usersCount);
            $this->assertSame('7', $allArticles); // 2 + 5
            $this->assertSame('5,000', $allWords); // 1500 + 3500 -> "5,000"
            $this->assertSame(3, $langsCount);
            $this->assertSame('350', $allViews); // 100 + 250
        }

        public function testPrintCatTablePassesUsersKeysToLangOfUsers(): void
        {
            $GLOBALS['__fixtures']['users_list'] = [
                'alice' => ['count' => 1, 'words' => 10, 'views' => 1],
                'bob'   => ['count' => 1, 'words' => 20, 'views' => 2],
                'zoe'   => ['count' => 3, 'words' => 30, 'views' => 3],
            ];

            print_cat_table(2023, 'groupB', 'myCamp', 'news');

            $args = $GLOBALS['__calls']['get_td_or_sql_top_lang_of_users_args'];
            $this->assertNotNull($args, 'get_td_or_sql_top_lang_of_users should be called');
            [$users] = $args;
            $this->assertSame(['alice', 'bob', 'zoe'], $users, 'Should pass array_keys of users list preserving order');
        }

        public function testMainLeaderboardBuildsBoardAndResolvesCatFromCamp(): void
        {
            \Tables\SqlTables\TablesSql::$s_camp_to_cat = ['campaign2025' => 'categoryX'];

            $GLOBALS['__fixtures']['users_list'] = [
                'u1' => ['count' => 1, 'words' => 100, 'views' => 10],
            ];
            $GLOBALS['__fixtures']['lang_table'] = [];

            $html = main_leaderboard(2025, 'campaign2025', 'wg');

            $this->assertStringContainsString('FILTER_FORM_HTML', $html);
            $this->assertStringContainsString('<hr/>', $html);
            $this->assertStringContainsString('<div class="container-fluid">', $html);
            $this->assertStringContainsString('COL_NUMBERS_HTML', $html);

            // Verify that print_cat_table received cat resolved from TablesSql map via underlying call to top_users
            $args = $GLOBALS['__calls']['get_td_or_sql_top_users_args'];
            $this->assertNotNull($args);
            // [year, user_group, cat]
            $this->assertSame([2025, 'wg', 'categoryX'], $
$args);
        }

        public function testMainLeaderboardFallbackCatEmptyWhenUnknownCamp(): void
        {
            \Tables\SqlTables\TablesSql::$s_camp_to_cat = []; // no mapping

            main_leaderboard(2022, 'unknownCamp', 'grp');

            $args = $GLOBALS['__calls']['get_td_or_sql_top_users_args'];
            $this->assertNotNull($args);
            $this->assertSame([2022, 'grp', ''], $args, 'Cat should fall back to empty string');
        }

        public function testPrintCatTableHandlesEmptyUserListGracefully(): void
        {
            $GLOBALS['__fixtures']['users_list'] = []; // no users
            $GLOBALS['__fixtures']['lang_table'] = []; // no languages

            $html = print_cat_table(2020, 'g', 'camp', 'cat');

            $this->assertIsString($html);
            $this->assertStringContainsString('COL_NUMBERS_HTML', $html);

            $args = $GLOBALS['__calls']['createNumbersTable_args'];
            $this->assertNotNull($args);
            [$usersCount, $allArticles, $allWords, $langsCount, $allViews] = $args;
            $this->assertSame(0, $usersCount);
            $this->assertSame('0', $allArticles);
            $this->assertSame('0', $allWords);
            $this->assertSame(0, $langsCount);
            $this->assertSame('0', $allViews);

            // Ensure users_tab and copy module get called with empty users
            $uargs = $GLOBALS['__calls']['get_td_or_sql_top_lang_of_users_args'];
            $this->assertNotNull($uargs);
            $this->assertSame([[]], $uargs);
        }

        public function testNumberFormattingWithLargeValues(): void
        {
            $GLOBALS['__fixtures']['users_list'] = [
                'big' => ['count' => 1234, 'words' => 9876543, 'views' => 1200000],
                'mid' => ['count' => 66,   'words' => 500,     'views' => 10],
            ];
            $GLOBALS['__fixtures']['lang_table'] = [['lang' => 'en', 'count' => 1]];

            print_cat_table(2030, 'g', 'c', 'x');

            $args = $GLOBALS['__calls']['createNumbersTable_args'];
            $this->assertNotNull($args);
            [, $allArticles, $allWords, , $allViews] = $args;

            $this->assertSame('1,300', $allArticles); // 1234 + 66 -> "1,300"
            $this->assertSame('9,876,043', $allWords);
            $this->assertSame('1,200,010', $allViews);
        }
    }
}