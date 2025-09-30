<?PHP

namespace Results\ResultsTable\Rows;

/*
Usage:

use function Results\ResultsTable\Rows\make_td_rows_responsive;

*/

function make_td_rows_responsive($full, $inprocess, $tds)
{
    //---
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
    // $cnt2 = $full ? "$cnt.Full" : $cnt;
    $cnt2 = $full && (strtolower(substr($title, 0, 6)) != 'video:') ? "$cnt.Full" : $cnt;
    //---
    $td_rows = <<<HTML
        <th class='num' scope="row">
            $cnt2
        </th>
        <td class='link_container'>
            <a target='_blank' href='$mdwiki_url'>$title</a>
        </td>
        <th class=''>
            $tab
        </th>
        <td class='num' style="text-align: left">
            $pviews
        </td>
        <td class='num' style="text-align: left">
            $asse
        </td>
        <td class='num' style="text-align: left">
            $words
        </td>
        <td class='num' style="text-align: left">
            $refs
        </td>
        <td>
            $qid
        </td>
    HTML;
    //---
    if ($inprocess) {
        $td_rows .= <<<HTML
            <td>
                $_user_
            </td>
            <td>
                $_date_
            </td>
        HTML;
    };
    //---
    $td_rows = "<tr class=''>$td_rows</tr>";
    //---
    return $td_rows;
}
