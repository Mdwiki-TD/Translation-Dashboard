<?PHP
include_once 'tables.php';
include_once 'functions.php';
include_once 'langcode.php';
include_once 'getcats.php';
include_once 'sql_tables.php'; // $sql_qids $cat_titles $cat_to_camp $camp_to_cat
// $cat_to_camp
$articles_to_camps = [];
$camps_to_articles = [];
//---
foreach ($cat_to_camp as $cat => $camp) {
    $camps_to_articles[$camp] = [];
    $members = get_cat_from_cache($cat);
    //---
    foreach ($members as $member) {
        if (!in_array($member, $camps_to_articles[$camp])) {
            $camps_to_articles[$camp][] = $member;
        }
        //---
        if (!isset($articles_to_camps[$member])) $articles_to_camps[$member] = [];
        //---
        $articles_to_camps[$member][] = $camp;
    }
};
//---
function camps_list() {
    global $articles_to_camps;
    $table = <<<HTML
        <table class='table table-striped sortable'>
            <thead>
                <tr>
                    <th>Member</th>
                    <th>lenth</th>
                    <th>Campaigns</th>
                </tr>
            </thead>
            <tbody>
    HTML;
    foreach ($articles_to_camps as $member => $camps) {
        sort($camps);
        $articles_to_camps[$member] = $camps;
        $count = count($camps);
        $table .= <<<HTML
                <tr>
                    <td>$member</td>
                    <td>$count</td>
                    <td>
                        <ul>
        HTML;
        foreach ($camps as $camp) {
            $table .= <<<HTML
                            <li>$camp</li>
        HTML;
        };
        $table .= <<<HTML
                        </ul>
                    </td>
                </tr>
        HTML;
    };
    $table .= <<<HTML
            </tbody>
        </table>
    HTML;
    //---
    echo $table;
//---
}
