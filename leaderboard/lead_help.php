<?PHP
//---
function make_td_fo_user($tabb, $number, $view_number, $word, $page_type = 'users', $tab_ty='a') {
    //---
    global $cat_to_camp;
    //---
    $mdtitle = $tabb['title'];
    $user    = $tabb['user'];
    $date    = $tabb['date'];
    $lang    = $tabb['lang'];
    $cat     = $tabb['cat'];
    $pupdate = $tabb['pupdate'];
    //---
    $word = number_format($word);
    //---
    $nana = make_mdwiki_title( $mdtitle );
    //---
    $ccat = make_cat_url($cat);
    $campaign = isset($cat_to_camp[$cat]) ? $cat_to_camp[$cat] : '';
    if ( $campaign != '') {
        $ccat = "<a href='leaderboard.php?camp=$campaign'>$campaign</a>";
    };
    //---
    $tran_type = isset($tabb['translate_type']) ? $tabb['translate_type'] : '';
    if ($tran_type == 'all') { 
        $tran_type = 'Whole article';
    };
    //---
    if ($page_type == 'users') {
        $urll = "<a href='leaderboard.php?langcode=$lang'><span style='white-space: nowrap;'>$lang</span></a>";
    } else {
        $use = rawurlEncode($user);
        $use = str_replace ( '+' , '_' , $use );
        //---
        $urll = "<a href='leaderboard.php?user=$use'><span style='white-space: nowrap;'>$user</span></a>";
        //---
    };
    //---
    $udate = $pupdate;
    //---
    if ($tab_ty == 'pending') {
        $udate = $date;
        $target_link = 'Pending';
        $td_views = '';
    } else {
        $target  = $tabb['target'];
        //---
        $view = make_view_by_number($target, $view_number, $lang, $pupdate);
        //---
        $target_link = make_target_url($target, $lang);
        //---
        $td_views = "<td data-sort='$view_number'>$view</td>";
    };
    //---
    $year = substr($udate,0,4);
    //---
    $laly = "
        <tr class='filterDiv show2 $year'>
            <td>$number</td>
            <td>$urll</td>
            <td>$nana</td>
            <td>$ccat</td>
            <td>$word</td>
            <td>$tran_type</td>
            <td>$target_link</td>
            <td class='spannowrap'>$udate</td>
            $td_views
        </tr>
        ";
    //---
    return $laly;
    //---
};
//---
function make_table_lead($dd, $tab_type='a', $views_table = array(), $page_type='users', $user='', $lang='') {
    //---
    global $Words_table;
    //---
    $total_words = 0;
    $total_views = 0;
    //---
    $user_or_lang = ($page_type == 'users') ? 'Lang.' : 'User';
    //---
    $tab_views = ($tab_type == 'pending') ? '' : '<th>Views</th>';
    $th_Date   = ($tab_type == 'pending') ? 'Start date' : 'Date';
    //---
    $sato = "
        <table class='table table-striped compact soro'>
            <thead>
                <tr>
                    <th>#</th>
                    <th>$user_or_lang</th>
                    <th>Title</th>
                    <th>Campaign</th>
                    <th>Words</th>
                    <th>type</th>
                    <th>Translated</th>
                    <th>$th_Date</th>
                    $tab_views
                </tr>
            </thead>
            <tbody>";
    //---
    $noo = 0;
    foreach ( $dd AS $tat => $tabe ) {
        //---
        $noo += 1;
        //---
        $target  = $tabe['target'];
        $view_number = isset($views_table[$target]) ? $views_table[$target] : 0;
        $total_views += $view_number;
        //---
        $mdtitle = $tabe['title'];
        $word2 = isset($Words_table[$mdtitle]) ? $Words_table[$mdtitle] : 0;
        $word = isset($tabe['word']) ? $tabe['word'] : 0;
        //---
        if ( $word < 1 ) $word = $word2;
        //---
        $total_words += $word;
        //---
        $sato .= make_td_fo_user($tabe, $noo, $view_number, $word, $page_type = $page_type, $tab_ty=$tab_type);
        //---
    };
    //---
    $sato .= '
        </tbody>
    </table>';
    //---
    $table1 = "<table class='table table-sm table-striped' style='width:70%;'>
    <tr><td>Words: </td><td>$total_words</td></tr>
    <tr><td>Pageviews: </td><td><span id='hrefjsontoadd'>$total_views</span></td></tr>
    </table>";
    //---
    $arra = array('table1' => $table1, 'table2' => $sato );
    //---
    return $arra;
    //---
};
//---
?>