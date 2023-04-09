<div class='card-header'>
    <h4>Translations in process:</h4>
</div>
<div class='card-body'>
<?PHP
//---
function make_td($tabg, $nnnn) {
    //---
    global $code_to_lang, $Words_table, $views_sql, $user_name, $cat_to_camp;
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
    $pupdate  = $tabg['date'] ?? '';
    //---
    $lang2 = $code_to_lang[$llang] ?? $llang;
    //---
    // $ccat = make_cat_url( $cat );
    $ccat = $cat_to_camp[$cat] ?? $cat;
    //---
    // $worde = $word ?? $Words_table[$md_title];
    //---
    $nana = make_mdwiki_title( $md_title );
    //---
    // $mail_params = array( 'user' => $user, 'lang' => $llang, 'date' => $date, 'title' => $md_title, 'nonav' => '1');
    //---
    // $mail_url = "coordinator.php?ty=msg&" . http_build_query( $mail_params );
    //---
	// $onclick = 'pupwindow("' . $mail_url . '")';
    //---
    // $mail = "<a class='btn btn-primary btn-sm' onclick='$onclick'>Email</a>";
    //---
    $laly = '
        <tr>
            <td>' . $nnnn   . '</td>
            <td><a target="" href="leaderboard.php?user=' . $user . '">' . $user . '</a></td>
            <td><a target="" href="leaderboard.php?langcode=' . $llang . '">' . $lang2 . '</a>' . '</td>
            <td style="max-width:150px;">' . $nana  . '</td>
            <td>' . $ccat  . '</td>
            <td>' . $date . '</td>
            '; 
    //---
    $laly .= '
        </tr>';
    //---
    return $laly;
};
//---
$quaa = "select * from pages where target = ''
ORDER BY date DESC
limit 100;
";
$dd = execute_query($quaa);
//---
$sato = '
	<table class="table table-sm table-striped soro" style="font-size:90%;">
    <thead>
        <tr>
            <th>#</th>
            <th>User</th>
            <th><span data-toggle="tooltip" title="Language">Lang.</span></th>
            <th>Title</th>
            <th>Campaign</th>
            <th>Date</th>
        </tr>
    </thead>
    <tbody>
';
//---
$noo = 0;
foreach ( $dd AS $tat => $tabe ) {
    //---
    $noo = $noo + 1;
    $sato .= make_td($tabe, $noo);
    //---
};
//---
$sato .= '
    </tbody>
</table>
';
print $sato;
//---
?>
<script>
function pupwindow(url) {
	window.open(url, 'popupWindow', 'width=850,height=550,scrollbars=yes');
};
</script>