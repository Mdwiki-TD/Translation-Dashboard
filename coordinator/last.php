<script src="sorttable.js"></script>
<h4>Most recent translation:</h4>
<?PHP
//--
//===
function make_td($tabg, $nnnn) {
    // ------------------
    global $code_to_lang, $Words_table, $views_sql;
    // ------------------
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
    $pupdate  = isset($tabg['pupdate']) ? $tabg['pupdate'] : '';
    // ------------------
    $views_number = isset($views_sql[$targe]) ? $views_sql[$targe] : '?';
    // ------------------
    $lang2 = isset($code_to_lang[$llang]) ? $code_to_lang[$llang] : $llang;
    //---
    $ccat = make_cat_url( $cat );
    //---
    $worde = isset($word) ? $word : $Words_table[$md_title];
    //---
    $nana = make_mdwiki_title( $md_title );
    //---
    $targe33 = make_target_url( $targe , $llang );
    //---
    $view = make_view_by_number($targe , $views_number, $llang) ;
    //---
    $laly = '
        <tr>
            <td>' . $nnnn   . '</td>
            <td><a target="" href="users.php?user=' . $user . '">' . $user . '</a>' . '</td>
            <td><a target="" href="langs.php?langcode=' . $llang . '">' . $lang2 . '</a>' . '</td>
            <td>' . $nana  . '</td>
            <!-- <td>' . $date  . '</td> -->
            <td>' . $ccat  . '</td>
            <!-- <td>' . $worde . '</td> -->
            <td>' . $targe33 . '</td>
            <td>' . $pupdate . '</td>
            <!-- <td>' . $view . '</td> -->
            '; 
    //---
    $laly .= '
        </tr>';
    //---
    return $laly;
};
//---
$quaa = "select * from pages where target != ''
ORDER BY pupdate DESC
limit 20
;";
$dd = quary2($quaa);
//===
$sato = '
	<table class="table table-striped sortable alignleft" style="font-size:90%;">
        <tr>
            <th>#</th>
            <th>User</th>
            <th><span data-toggle="tooltip" title="Language">Lang.</span></th>
            <th>Title</th>
            <th>Category</th>
            <!-- <th>Words</th> -->
            <th>Translated</th>
            <th>Date</th>
            <!-- <th>Views</th> -->
        </tr>
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
$sato .= '</table>';
print $sato;
//---
?>
<script>
$(document).ready( function () {
  $('[data-toggle="tooltip"]').tooltip();   
});
</script>