<?PHP

namespace Results\ResultsTable\Rows;

/*
Usage:

use function Results\ResultsTable\Rows\make_td_rows_mobile;
use function Results\ResultsTable\Rows\make_mobile_table;

*/

function make_td_rows_mobile($full, $inprocess, $mobile_table, $tds)
{
    //---
    $translate_url = $tds["translate_url"];
    $mdwiki_url = $tds["mdwiki_url"];
    $cnt    = $tds["cnt"];
    $tab    = $tds["tab"];
    $pviews = $tds["pageviews"];
    $asse   = $tds["asse"];
    $words  = $tds["words"];
    $refs   = $tds["refs"];
    $qid    = $tds["qid"];
    $title  = $tds["title"];
    $_user_ = $tds["user"];
    $_date_ = $tds["date"];
    //---
    $cnt2 = $full ? "$cnt.Full" : $cnt;
    $div_id = "t_$cnt";
    if ($inprocess) $div_id .= '_in';
    if ($full) $div_id .= '_full';
    //---
    $td_rows = <<<HTML
        <th class='num hide_on_mobile_cell' scope="row" data-content="$cnt2" data-sort="$cnt">
            $cnt2
        </th>
        <td class='link_container' data-content="$cnt2">
            <a target='_blank' href='$mdwiki_url' class='hide_on_mobile'>$title</a>
            <a target='_blank' href='$translate_url' class="only_on_mobile"><b>$title</b></a>
            <a class="only_on_mobile" style="float:right" data-bs-toggle="collapse" href="#$div_id" role="button" aria-expanded="false" aria-controls="$div_id"><i class="fas fa-plus"></i></a>
        </td>
        <th class=''>
            <span class='hide_on_mobile'>$tab</span>
            <div class='collapse' id="$div_id">
                <div class='only_on_mobile'>$mobile_table</div>
            </div>
        </th>
        <td class='num hide_on_mobile_cell' data-content="Views">
            $pviews
        </td>
        <td class='num hide_on_mobile_cell' data-content="Importance">
            $asse
        </td>
        <td class='num hide_on_mobile_cell' data-content="Words">
            $words
        </td>
        <td class='num hide_on_mobile_cell' data-content="Refs.">
            $refs
        </td>
        <td class='hide_on_mobile_cell' data-content="Qid">
            $qid
        </td>
    HTML;
    //---
    if ($inprocess) {
        $td_rows .= <<<HTML
            <td class='hide_on_mobile_cell' data-content="user">
                $_user_
            </td>
            <td class='hide_on_mobile_cell' data-content="Date">
                $_date_
                </td>
        HTML;
    };
    //---
    $td_rows = "<tr class=''>$td_rows</tr>";
    //---
    return $td_rows;
}

function make_mobile_table($inprocess, $full_translate_url, $full_tr_user, $tds)
{
    //---
    $asse   = $tds["asse"];
    $words  = $tds["words"];
    $refs   = $tds["refs"];
    $qid    = $tds["qid"];
    $_user_ = $tds["user"];
    $_date_ = $tds["date"];
    $pviews = $tds["pageviews"];
    //---
    // Define an array to store the values
    $data = array(
        array("Views", $pviews),
        array("Importance", $asse),
        array("Words", $words),
        array("Refs.", $refs),
        array("Qid", $qid)
    );
    if ($inprocess) {
        // add User : $_user_ and Date : $_date_
        $data[] = array("User", $_user_);
        $data[] = array("Date", $_date_);
    };

    // Initialize an empty string to store the generated HTML
    $nq_ths = '';

    // if ($full_tr_user && !$inprocess) {
    if ($full_tr_user && !$inprocess) {
        $nq_ths = <<<HTML
                <div class="d-table-row">
                    <span class="d-table-cell px-2" style="color:#54667a;">Full Translate</span>
                    <span class="d-table-cell px-2" style='font-weight: normal;'><a class='inline' target='_blank' href='$full_translate_url'>Translate</a></span>
                </div>
            HTML;
    }

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
    $nxqe = <<<HTML
            <div class="d-table table-striped">
                $nq_ths
            </div>
        HTML;
    //---
    return $nxqe;
}
