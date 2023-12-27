<?PHP
//---
include_once 'langcode.php';
//---
$lang = $_GET['lang'] ?? 'all';
//---
if ($lang !== 'all' && !isset($code_to_lang[$lang])) {
    $lang = 'all';
};
//---
function filter_recent($lang) {
    global $code_to_lang;
    //---
    $tabes = [];
    //---
    foreach ( execute_query("select DISTINCT lang from pages;") AS $tat => $tag ) {
        $lag = strtolower($tag['lang']);
        //---
        $tabes[] = $lag;
        //---
    };
    //---
    ksort($tabes);
    //---
    $lang_list = '';
    //---
    foreach ( $tabes as $codr ) {
        $langeee = $code_to_lang[$codr] ?? '';
		$selected = ($codr == $lang) ? 'selected' : '';
        $lang_list .= <<<HTML
            <option data-tokens='$codr' value='$codr' $selected>$langeee</option>
            HTML;
    };
    //---
    $langse = <<<HTML
        <select 
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
$recent_table = <<<HTML
	<table class="table table-sm table-striped table-mobile-responsive table-mobile-sided" id="last_tabel" style="font-size:90%;">
        <thead>
            <tr>
                <th>#</th>
                <th>User</th>
                <th></th>
                <th><span data-toggle="tooltip" title="Language">Lang.</span></th>
                <th>Title</th>
                <th><span data-toggle="tooltip" title="Campaign">Camp.</span></th>
                <!-- <th>Words</th> -->
                <th>Translated</th>
                <th>Date</th>
                <th>Views</th>
                <th>fixref</th>
                <th>add_date</th>
            </tr>
        </thead>
        <tbody>
HTML;
//---
function make_td($tabg, $nnnn) {
    //---
    global $code_to_lang, $Words_table, $views_sql, $cat_to_camp;
    //---
    $id       = $tabg['id'];
    $date     = $tabg['date'];
    //---
    //return $date . '<br>';
    //---
    $user     = $tabg['user'];
    $llang    = $tabg['lang'];
    $md_title = trim($tabg['title']);
    $cat      = $tabg['cat'];
    $word     = $tabg['word'];
    $targe    = trim($tabg['target']);
    $pupdate  = $tabg['pupdate'] ?? '';
    $add_date = $tabg['add_date'] ?? '';
    //---
    $views_number = $views_sql[$targe] ?? '?';
    //---
    // $lang2 = $code_to_lang[$llang] ?? $llang;
    $lang2 = $llang;
    //---
    // $ccat = make_cat_url( $cat );
    $ccat = $cat_to_camp[$cat] ?? $cat;
    //---
    $worde = $word ?? $Words_table[$md_title];
    //---
    $nana = make_mdwiki_title( $md_title );
    //---
    $targe33 = make_target_url( $targe, $llang );
	$targe2  = urlencode($targe);
    //---
    $view = make_view_by_number($targe, $views_number, $llang, $pupdate);
    //---
    if (user_in_coord != false) {
        $mail_icon = make_mail_icon($tabg);
    };
    //---
    $laly = <<<HTML
        <tr>
            <td data-content='#'>
                $nnnn
            </td>
            <td data-content='User'>
                <a target='' href='leaderboard.php?user=$user'>$user</a>
            </td>
            <td data-content=''>
                $mail_icon
            </td>
            <td data-content='Lang.'>
                <a target='' href='leaderboard.php?langcode=$llang'>$lang2</a>
            </td>
            <td style='max-width:150px;' data-content='Title'>
                $nana
            </td>
            <!-- <td>$date</td> -->
            <td data-content='Campaign'>
                $ccat
            </td>
            <!-- <td>$worde</td> -->
            <td style='max-width:150px;' data-content='Translated'>
                $targe33
            </td>
            <td data-content='Date'>
                $pupdate
            </td>
            <td data-content='Views'>
                $view
            </td>
            <td data-content='fixref'>
                <a target='_blank' href="../fixwikirefs.php?title=$targe2&lang=$llang">fix</a>
            </td>
            <td data-content='add_date'>
                $add_date
            </td>
        </tr>
    HTML;
    //---
    return $laly;
};
//---
function get_recent_sql($lang) {
    $lang_line = '';
    //---
    if ($lang != '' && $lang != 'all') $lang_line = "and lang = '$lang'";
    //---
    $dd0 = execute_query("select * from pages where target != '' $lang_line ORDER BY pupdate DESC limit 100;");
    $dd1 = execute_query("select * from pages where target != '' $lang_line ORDER BY add_date DESC limit 100");
    //---
    // merage the two arrays without duplicates
    $dd2 = array_unique(array_merge($dd0, $dd1), SORT_REGULAR);
    //---
    // sort the table by add_date
    usort($dd2, function($a, $b) {
        return strtotime($b['add_date']) - strtotime($a['add_date']);
    });
    //---
    return $dd2;    
}
//---
$qsl_results = get_recent_sql($lang);
//---
$noo = 0;
foreach ( $qsl_results AS $tat => $tabe ) {
    //---
    $noo = $noo + 1;
    $recent_table .= make_td($tabe, $noo);
    //---
};
//---
$recent_table .= <<<HTML
        </tbody>
    </table>
HTML;
//---
$uuu = filter_recent($lang);
//---
echo <<<HTML
<div class='card-header'>
    <form method='get' action='coordinator.php'>
        <input name='ty' value='last' hidden/>
        <div class='row'>
            <div class='col-md-4'>
                <h4>Most recent translations:</h4>
            </div>
            <div class='col-md-4'>
                $uuu
            </div>
            <div class='aligncenter col-md-2'>
                <input class='btn btn-primary' type='submit' name='start' value='Filter' />
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
function pupwindow(url) {
	window.open(url, 'popupWindow', 'width=850,height=550,scrollbars=yes');
};

$(document).ready( function () {
	var t = $('#last_tabel').DataTable({
	order: [[10	, 'desc']],
    // paging: false,
	lengthMenu: [[50, 100, 150], [50, 100, 150]],
    // scrollY: 800
	});
} );

</script>