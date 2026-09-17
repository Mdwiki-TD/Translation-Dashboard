<?PHP

namespace Results\GetResults2026;

use function Results\ResultsTableHtml\make_table_start;
use function Results\GetResults2026\Rows\make_one_row_new_inprocess;

function make_results_table_inprocess(
    $inprocessTable,
    $langcode,
    $cat,
    $camp,
    $inProgressBtn,
    $fullTrUser,
    $globalUsername,
    $titlesInfos,
    $endpoint,
    $userCoord
): string {
    //---
    // $inprocessTable = normalizeItems($inprocessTable);
    //---
    $frist = make_table_start(true, $inProgressBtn);
    //---
    $list = "";
    $cnt = 1;
    //---
    foreach ($inprocessTable as $title => $titleTab) {

        if (empty($title)) continue;

        $title = str_replace('_', ' ', $title);
        //---
        $titleData = $titlesInfos[$title] ?? [];
        //---
        // { "title": "Andes virus infection", "user": "Mr. Ibrahem", "lang": "ar", "cat": "RTT", "translate_type": "all", "word": 0, "add_date": "2026-05-21 00:00:00", "campaign": "Main", "autonym": "العربية" }
        $traType = $titleTab['translate_type'] ?? '';
        //---
        $full = false;
        //---
        if (strtolower(substr($title, 0, 6)) == 'video:') {
            $traType = 'all';
            $full = true;
        };
        //---
        $row = make_one_row_new_inprocess(
            $title,
            $traType,
            $cnt,
            $langcode,
            $cat,
            $camp,
            $titleTab,
            $inProgressBtn,
            $full,
            $fullTrUser,
            $globalUsername,
            $titleData,
            $endpoint,
            $userCoord
        );
        //--
        $list .= $row;
        //---
        $cnt++;
    };

    $last = <<<HTML
        </tbody>
    </table>
    HTML;

    return $frist . $list . $last;
}
