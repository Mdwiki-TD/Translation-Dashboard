<div class='card-header'>
    <h4>Most recent translations:</h4>
</div>
<div class='card-body'>
<?PHP
//---
$sato = <<<HTML
	<table class="table table-sm table-striped" id="last_tabel" style="font-size:90%;">
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
    $md_title = $tabg['title'];
    $cat      = $tabg['cat'];
    $word     = $tabg['word'];
    $targe    = $tabg['target'];
    $pupdate  = $tabg['pupdate'] ?? '';
    $add_date = $tabg['add_date'] ?? '';
    //---
    $views_number = $views_sql[$targe] ?? '?';
    //---
    $lang2 = $code_to_lang[$llang] ?? $llang;
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
        <td>$nnnn</td>
        <td><a target='' href='leaderboard.php?user=$user'>$user</a></td>
        <td>$mail_icon</td>
        <td><a target='' href='leaderboard.php?langcode=$llang'>$lang2</a></td>
        <td style='max-width:150px;'>$nana</td>
        <!-- <td>$date</td> -->
        <td>$ccat</td>
        <!-- <td>$worde</td> -->
        <td style='max-width:150px;'>$targe33</td>
        <td>$pupdate</td>
        <td>$view</td>
        <td><a target='_blank' href='../fixwikirefs.php?title=$targe2&lang=$llang'>fix</a></td>
        <td>$add_date</td>
    </tr>
    HTML;
    //---
    return $laly;
};
//---
$dd0 = execute_query("select * from pages where target != '' ORDER BY pupdate DESC limit 100;");
//---
$dd1 = execute_query("select * from pages where target != '' ORDER BY add_date DESC limit 100");
//---
// merage the two arrays without duplicates
$dd2 = array_unique(array_merge($dd0, $dd1), SORT_REGULAR);
//---
// sort the table by add_date
usort($dd2, function($a, $b) {
    return strtotime($b['add_date']) - strtotime($a['add_date']);
});
//---
$noo = 0;
foreach ( $dd2 AS $tat => $tabe ) {
    //---
    $noo = $noo + 1;
    $sato .= make_td($tabe, $noo);
    //---
};
//---
$sato .= <<<HTML
        </tbody>
    </table>
HTML;
print $sato;
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
	lengthMenu: [[25, 50, 100], [25, 50, 100]],
    // scrollY: 800
	});
} );

</script>