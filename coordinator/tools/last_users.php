<?PHP
//---
include_once 'langcode.php';
//---
$recent_table = <<<HTML
	<table class="table table-sm table-striped table-mobile-responsive table-mobile-sided" id="last_tabel" style="font-size:90%;">
        <thead>
            <tr>
                <th>#</th>
                <th>User</th>
                <th>Lang</th>
                <th>Title</th>
                <th>Translated</th>
                <th>Publication date</th>
                <th>Fixref</th>
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
    //---
    $llang    = $tabg['lang'];
    $md_title = trim($tabg['title']);
    $cat      = $tabg['cat'];
    $word     = $tabg['word'];
    $targe    = trim($tabg['target']);
    $pupdate  = $tabg['pupdate'] ?? '';
    $add_date = $tabg['add_date'] ?? '';
    //---
    $username = $user;
    // $username is the first word of the user if length > 15
    if (strlen($user) > 15) {
        $username = explode(' ', $user);
        $username = $username[0];        
    }
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
    $laly = <<<HTML
        <tr>
            <td data-content='#'>
                $nnnn
            </td>
            <td data-content='User'>
                <a href='leaderboard.php?user=$user'>
                    $username
                </a>
            </td>
            <td data-content='Lang'>
                <a href='leaderboard.php?langcode=$llang'>$lang2</a>
            </td>
            <td style='max-width:150px;' data-content='Title'>
                $nana
            </td>
            <td style='max-width:150px;' data-content='Translated'>
                $targe33
            </td>
            <td data-content='Publication date'>
                $pupdate
            </td>
            <td data-content='Fixref'>
                <a target='_blank' href="../fixwikirefs.php?title=$targe2&lang=$llang">Fix</a>
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
function get_recent_sql() {
    $lang_line = '';
    //---
    // pages_users (title, lang, user, pupdate, target, add_date)
    //---
    $dd0 = execute_query("select * from pages_users where target != '' ORDER BY pupdate DESC limit 100;");
    //---
    // sort the table by add_date
    usort($dd0, function($a, $b) {
        // return strtotime($b['add_date']) - strtotime($a['add_date']);
        return strtotime($b['pupdate']) - strtotime($a['pupdate']);
    });
    //---
    return $dd0;    
}
//---
$qsl_results = get_recent_sql();
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
echo <<<HTML
<div class='card-header'>
    <h4>Recent translations in user space:</h4>
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
	order: [[7	, 'desc']],
    // paging: false,
	lengthMenu: [[100, 150, 200], [100, 150, 200]],
    // scrollY: 800
	});
} );

</script>