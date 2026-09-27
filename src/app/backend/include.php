<?PHP
include_once __DIR__ . '/CurrentUser.php';

include_once __DIR__ . '/settings.php';
include_once __DIR__ . '/include_first/include.php';

# MdwikiSql
include_once __DIR__ . '/MdwikiSql/Database.php';
include_once __DIR__ . '/MdwikiSql/mdwiki_sql.php';
include_once __DIR__ . '/MdwikiSql/db_insert.php';

# api_calls
include_once __DIR__ . '/api_calls/wiki_api.php';

# td_api_wrap
include_once __DIR__ . '/td_api_wrap/td_api.php';

# api_or_sql classes
include_once __DIR__ . '/api_or_sql/ApiOrSqlService.php';
include_once __DIR__ . '/api_or_sql/PagesTable.php';
include_once __DIR__ . '/api_or_sql/ViewsTable.php';
include_once __DIR__ . '/api_or_sql/CategoriesTable.php';
include_once __DIR__ . '/api_or_sql/InProcessTable.php';
include_once __DIR__ . '/api_or_sql/UsersTable.php';
include_once __DIR__ . '/api_or_sql/LeaderboardTable.php';
include_once __DIR__ . '/api_or_sql/SettingsTable.php';
include_once __DIR__ . '/api_or_sql/TitlesTable.php';

# api_or_sql functions (delegating wrappers)
include_once __DIR__ . '/api_or_sql/funcs.php';
include_once __DIR__ . '/api_or_sql/index.php';
include_once __DIR__ . '/api_or_sql/process_data.php';

include_once __DIR__ . '/api_or_sql/data_tab.php';
include_once __DIR__ . '/api_or_sql/get_lead.php';
include_once __DIR__ . '/api_or_sql/new_sql_tables.php';
include_once __DIR__ . '/api_or_sql/top.php';

# tables
include_once __DIR__ . '/tables/langcode.php';

# others
include_once __DIR__ . '/others/helps.php';
include_once __DIR__ . '/others/tr_link.php';

# results_27
include_once __DIR__ . "/results_27/include.php";
