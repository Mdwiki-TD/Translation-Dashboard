<?PHP
//---
use function Actions\MdwikiSql\fetch_query;
use function Actions\TDApi\get_td_api;
use function Actions\TDApi\compare_it;
//---
echo <<<HTML
    <div class='card-header'>
        <h4>Translations in process</h4>
    </div>
    <div class='card-body'>
HTML;
//---
/*

صفحات مكررة

SELECT A.id as id1, A.title as title1, A.user as user1, A.target as target1,
B.id as id2, B.title as title2, B.user as user2, B.target as target2
 from pages A, pages B
where A.target = ''
and A.lang = B.lang
and A.title = B.title
and B.target != ''
;

للحذف:
SELECT A.id from pages A, pages B where A.target = '' and A.lang = B.lang and A.title = B.title and B.target != '';
*/
//---
$text = <<<HTML
<table class='table table-striped compact soro table-mobile-responsive table-mobile-sided'>
    <thead>
        <tr>
            <th>#</th>
            <th class='spannowrap'>User</th>
            <th>Articles</th>
        </tr>
    </thead>
    <tbody>

HTML;
//---
$user_process_tab = array();
//---
$sql_t = 'select DISTINCT user, count(target) as count from pages where target = "" group by user order by count desc';
//---
// $sql_result = fetch_query($sql_t);
//---
$sql_result1 = get_td_api(array('get' => 'count_pages', 'target_empty' => 1));
//---
// compare_it($sql_result, $sql_result1);
//---
$n = 0;
//---
foreach ($sql_result1 as $k => $t) {
    $user  = $t['user'] ?? "";
    $count = $t['count'] ?? "";
    $user_process_tab[$user] = $count;
    if ($user != 'test' && !empty($user) && $count > 0) {
        //---
        $n++;
        //---
        $use = rawurlEncode($user);
        $use = str_replace('+', '_', $use);
        //---
        $text .= <<<HTML
        <tr>
            <td data-content='#'>
                $n
            </td>
            <td data-content='User'>
                <a href='leaderboard.php?user=$use'>$user</a>
            </td>
            <td data-content='Articles'>
                $count
            </td>
        </tr>
        HTML;
    };
};
//---
$text .= <<<HTML
	</tbody>
	</table>
HTML;
//---
echo $text;
    //---
