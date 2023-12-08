<?PHP
//---
function sort_py_PageViews($items, $en_views_tab) {
    $dd = [];
    foreach ($items as $t) {
        $t = str_replace('_', ' ', $t);
        $kry = $en_views_tab[$t] ?? 0;
        $dd[$t] = $kry;
    }
    arsort($dd);
    return $dd;
}

function sort_py_importance($items, $Assessments_table, $Assessments_fff) {
    $empty = $Assessments_fff['Unknown'] ?? '';
    $dd = [];
    foreach ($items as $t) {
        $t = str_replace('_', ' ', $t);
        $aa = $Assessments_table[$t] ?? null;
        if (isset($aa)) {
            $kry = $Assessments_fff[$aa] ?? $empty;
        }
        $dd[$t] = $kry;
    }
    arsort($dd);
    return $dd;
}
//---
function make_results_table($items, $cod, $cat, $words_tab, $ref_tab, $Imps_tab, $Assessments_fff, $tra_type, $en_views_tab, $tra_btn, $sql_qids, $inprocess=false ) {

    $Refs_word    = 'Refs.';
    $Words_word   = 'Words';
    $Translate_th = "<th tt='h_len'>Translate</th>";
    //---
    $in_process = array();
    $inprocess_first = '';
    if ( $inprocess ) {
        $inprocess_first = '<th>user</th><th>date</th>';
        $in_process = $items;
        $items = array_keys($items);
        if ($tra_btn != '1') $Translate_th = '<th></th>';
    };
    //---
	$frist = <<<HTML
    <!-- <div class="table-responsive"> -->
    <table class="table compact sortable table-striped table-mobile-responsive table-mobile-sided" id="main_table">
        <thead>
            <tr>
                <th class="num">#</th>
                <th class="spannowrap" tt="h_title">Title</th>
                $Translate_th
                <th class="spannowrap" tt="h_len"><span data-toggle="tooltip" title="Page views in last month in English Wikipedia">Views</span></th>
                <th class="spannowrap" tt="h_len"><span data-toggle="tooltip" title="Page important from medicine project in English Wikipedia">Importance</span></th>
                <th class="spannowrap" tt="h_len"><span data-toggle="tooltip" title="number of word of the article in mdwiki.org">$Words_word</span></th>
                <th class="spannowrap" tt="h_len"><span data-toggle="tooltip" title="number of reference of the article in mdwiki.org">$Refs_word</span></th>
                <th class="spannowrap" tt="h_len"><span data-toggle="tooltip" title="Wikidata identifier">Qid</span></th>
                $inprocess_first
            </tr>
        </thead>
        <tbody>
    HTML;
    //---
    $dd = array();
    $dd = sort_py_PageViews($items, $en_views_tab);
    // $dd = sort_py_importance($items, $Imps_tab, $Assessments_fff);
    //---
    $list = "" ;
    $cnt = 1 ;
    foreach ( $dd AS $v => $gt) {
        if ( $v == '' ) continue;
        $title = str_replace ( '_' , ' ' , $v );
        $title2 = rawurlEncode($title);
        $cat2 = rawurlEncode($cat);
        $mdwiki_url = "//mdwiki.org/wiki/" . str_replace('+', '_', $title2);
        $pageviews = $en_views_tab[$title] ?? 0; 
        $qid = $sql_qids[$title] ?? "";
        $qid = ($qid != '') ? "<a class='inline' target='_blank' href='https://wikidata.org/wiki/$qid'>$qid</a>" : '&nbsp;';

        $word = $words_tab[$title] ?? 0; 
        $refs = $ref_tab[$title] ?? 0; 
        
        $asse = $Imps_tab[$title] ?? '';

        if ( $asse == '' ) $asse = 'Unknown';
        $params = array(
            "title" => $title2,
            "code" => $cod,
            // "username" => global_username,
            "cat" => $cat2,
            "type" => $tra_type
            );
        $translate_url = $mdwiki_url;
        $tab = <<<HTML
            <a role='button' class='btn btn-primary' onclick='login()'>
                <i class='fas fa-sign-in-alt fa-sm fa-fw mr-1'></i><span class='navtitles'>Login</span>
            </a>
            HTML;
        if (global_username != '') {
            $translate_url = 'translate.php?' . http_build_query($params);
            $tab = "<a href='$translate_url' class='btn btn-primary btn-sm'>Translate</a>";
        }
        //---
        $inprocess_tds = '';
        $_user_ = $in_process[$v]['user'] ?? '';
        $_date_ = $in_process[$v]['date'] ?? '';
        //---
        if ( $inprocess ) {
            $inprocess_tds = <<<HTML
                <td class='hide_on_mobile_cell' data-content="user">$_user_</td>
                <td class='hide_on_mobile_cell' data-content="Date">$_date_</td>
            HTML;
            if ($tra_btn != '1') {
                $tab = '';
                $translate_url = $mdwiki_url;
            };
        };
        //---
        // Define an array to store the values
        $data = array(
            array("Views", $pageviews),
            array("Importance", $asse),
            array("Words", $word),
            array("Refs.", $refs),
            array("Qid", $qid)
        );
        if ( $inprocess ) {
            // add User : $_user_ and Date : $_date_
            $data[] = array("User", $_user_);
            $data[] = array("Date", $_date_);
        };

        // Initialize an empty string to store the generated HTML
        $nq_ths = '';

        // Loop through the array and generate the HTML
        foreach ($data as $item) {
            $nq_ths .= <<<HTML
                <div class="d-table-row">
                    <span class="d-table-cell px-2" style="color:#54667a;">{$item[0]}</span>
                    <span class="d-table-cell px-2" style='font-weight: normal;'>{$item[1]}</span>
                </div>
            HTML;
        }
        //---
        $nq = <<<HTML
            <div class="d-table table-striped">
                $nq_ths
            </div>
        HTML;
        //---
        $div_id = "t_$cnt";
        if ( $inprocess ) $div_id .= '_in';
        //---
        $list .= <<<HTML
            <tr class="">
                <th class='num hide_on_mobile_cell' scope="row" data-content="#">$cnt</th>
                <td class='link_container spannowrap'data-content="$cnt">
                    <a target='_blank' href='$mdwiki_url' class="hide_on_mobile">$title</a>
                    <a target='_blank' href='$translate_url' class="only_on_mobile"><b>$title</b></a>
                    <a class="only_on_mobile" style="float:right" data-bs-toggle="collapse" href="#$div_id" role="button" aria-expanded="false" aria-controls="$div_id">+</a>
                </td>
                <th class=''>
                    <span class='hide_on_mobile'>$tab</span>
                    <div class='collapse' id="$div_id">
                        <div class='only_on_mobile'>$nq</div>
                    </div>
                </th>
                <td class='num hide_on_mobile_cell' data-content="Views">$pageviews</td>
                <td class='num hide_on_mobile_cell' data-content="Importance">$asse</td>
                <td class='num hide_on_mobile_cell' data-content="Words">$word</td>
                <td class='num hide_on_mobile_cell' data-content="Refs.">$refs</td>
                <td class='hide_on_mobile_cell' data-content="Qid">$qid</td>
                $inprocess_tds
            </tr>
            HTML;
        $cnt++ ;
    };
    $script = '' ;
    if ($script =='3') $script = '';
    $last = <<<HTML
        </tbody>
    </table>
    <!-- </div> -->
    HTML;
    return $frist . $list . $last . $script ;
    }

?>

