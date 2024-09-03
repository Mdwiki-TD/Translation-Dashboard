<?PHP

include_once 'Tables/langcode.php';

use function Actions\Html\make_mdwiki_title;
use function Actions\Html\make_talk_url;
use function Actions\Html\make_target_url;
use function Actions\MdwikiSql\fetch_query;
//---
$lang = $_GET['lang'] ?? 'All';
//---
if ($_SERVER['SERVER_NAME'] == 'localhost' && $lang == "All") {
    $lang = "ar";
}
//---
$table = (isset($_GET['table'])) ? $_GET['table'] : "pages";
//---
if ($lang !== 'All' && !isset($code_to_lang[$lang])) {
    $lang = 'All';
};
//---
function filter_recent($lang)
{
    global $code_to_lang;
    //---
    $tabes = [];
    //---
    foreach (fetch_query("select DISTINCT lang from pages;") as $tat => $tag) {
        $lag = strtolower($tag['lang']);
        //---
        $tabes[] = $lag;
        //---
    };
    //---
    ksort($tabes);
    //---
    $lang_list = "<option data-tokens='All' value='All'>All</option>";
    //---
    foreach ($tabes as $codr) {
        $langeee = $code_to_lang[$codr] ?? '';
        $selected = ($codr == $lang) ? 'selected' : '';
        $lang_list .= <<<HTML
            <option data-tokens='$codr' value='$codr' $selected>$langeee</option>
            HTML;
    };
    //---
    $langse = <<<HTML
        <select aria-label="Language code"
            class="selectpicker"
            id='lang'
            name='lang'
            placeholder='two letter code'
            data-live-search="true"
            data-container="body"
            data-live-search-style="begins"
            data-bs-theme="auto"
            data-style='btn active'
            data-width="90%"
            >
            $lang_list
        </select>
    HTML;
    //---
    $uuu = <<<HTML
        <div class="input-group">
            $langse
        </div>
    HTML;
    //---
    return $uuu;
}
//---
function filter_table($table)
{
    //---
    $tabes = [
        "pages" => ($table == "pages") ? 'selected' : '',
        "pages_users" => ($table == "pages_users") ? 'selected' : '',
    ];
    //---
    $l_list = "";
    //---
    foreach ($tabes as $table_name => $selected) {
        $label = ($table_name == "pages") ? "In main space" : "In user space";
        $l_list .= <<<HTML
            <option data-tokens='$table_name' value='$table_name' $selected>$label</option>
            HTML;
    };
    //---
    $uuu = <<<HTML
        <div class="input-group">
            <select aria-label="Language code"
                class="selectpicker"
                id='table'
                name='table'
                placeholder=''
                data-live-search="true"
                data-container="body"
                data-live-search-style="begins"
                data-bs-theme="auto"
                data-style='btn active'
                data-width="90%">
                $l_list
            </select>
        </div>
    HTML;
    //---
    return $uuu;
}
//---
$recent_table = <<<HTML
	<table class="table table-sm table-striped table-mobile-responsive table-mobile-sided" id="pages_table" style="font-size:90%;">
        <thead>
            <tr>
                <th>#</th>
                <th>User</th>
                <th>Lang.</th>
                <th>Title</th>
                <th>Translated</th>
                <th>Publication date</th>
                <th>Edit</th>
            </tr>
        </thead>
        <tbody>
HTML;
//---
function make_edit_icon($id, $title, $target, $lang, $user, $pupdate, $table)
{
    //---
    $edit_params = array(
        'id'   => $id,
        'title'  => $title,
        'target'  => $target,
        'lang'  => $lang,
        'user'  => $user,
        'pupdate' => $pupdate,
        'table' => $table,
        'nonav' => 1

    );
    //---
    if (isset($_REQUEST['test'])) {
        $edit_params['test'] = 1;
    }
    //---
    $edit_url = "coordinator.php?ty=translated/edit_page&" . http_build_query($edit_params);
    //---
    $onclick = 'pupwindow1("' . $edit_url . '")';
    //---
    return <<<HTML
    	<a class='btn btn-outline-primary btn-sm' onclick='$onclick'>Edit</a>
    HTML;
}
//---
function make_td($tabg, $nnnn, $table)
{
    //---
    $id       = $tabg['id'] ?? "";
    //---
    $user     = $tabg['user'] ?? "";
    $lang     = $tabg['lang'] ?? "";
    $md_title = trim($tabg['title']);
    $target   = trim($tabg['target']);
    $pupdate  = $tabg['pupdate'] ?? '';
    //---
    $mdwiki_title = make_mdwiki_title($md_title);
    //---
    $targe33 = make_target_url($target, $lang);
    //---
    $edit_icon = make_edit_icon($id, $md_title, $target, $lang, $user, $pupdate, $table);
    //---
    $laly = <<<HTML
        <tr>
            <td data-content='#'>
                $nnnn
            </td>
            <td data-content='User'>
                <a href='leaderboard.php?user=$user'>$user</a>
            </td>
            <td data-content='Lang.'>
                <a href='leaderboard.php?langcode=$lang'>$lang</a>
            </td>
            <td style='max-width:150px;' data-content='Title'>
                $mdwiki_title
            </td>
            <td style='max-width:150px;' data-content='Translated'>
                $targe33
            </td>
            <td data-content='Publication date'>
                $pupdate
            </td>
            <td data-content='Edit'>
                $edit_icon
            </td>
        </tr>
    HTML;
    //---
    return $laly;
};
//---

function get_recent_sql($lang, $table)
{
    $lang_line = '';
    //---
    if ($lang != '' && $lang != 'All') $lang_line = "and lang = '$lang'";
    //---
    $dd = fetch_query("select * from $table where target != '' $lang_line ORDER BY pupdate DESC;");
    //---
    // sort the table by add_date
    usort($dd, function ($a, $b) {
        return strtotime($b['add_date']) - strtotime($a['add_date']);
    });
    //---
    return $dd;
}
//---
$qsl_results = get_recent_sql($lang, $table);
//---
$noo = 0;
foreach ($qsl_results as $tat => $tabe) {
    //---
    $noo = $noo + 1;
    $recent_table .= make_td($tabe, $noo, $table);
    //---
};
//---
$recent_table .= <<<HTML
        </tbody>
    </table>
HTML;
//---
$filter_la = filter_recent($lang);
$filter_ta = filter_table($table);
//---
echo <<<HTML
<div class='card-header'>
    <form method='get' action='coordinator.php'>
        <input name='ty' value='translated' hidden/>
        <div class='row'>
            <div class='col-md-3'>
                <h4>Translated Pages:</h4>
            </div>
            <div class='col-md-3'>
                $filter_la
            </div>
            <div class='col-md-3'>
                $filter_ta
            </div>
            <div class='aligncenter col-md-2'>
                <input class='btn btn-outline-primary' type='submit' name='start' value='Filter' />
            </div>
        </div>
    </form>
</div>
<div class='card-body'>
HTML;
//---
echo $recent_table;
//---
?>
<script>
    $('#translated').addClass('active');
    $("#translated").closest('.mb-1').find('.collapse').addClass('show');

    $(document).ready(function() {
        var t = $('#pages_table').DataTable({
            // order: [[10	, 'desc']],
            // paging: false,
            lengthMenu: [
                [50, 100, 150],
                [50, 100, 150]
            ],
            // scrollY: 800
        });
    });
</script>
