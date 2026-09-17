<?PHP

namespace Results\GetResults2026;

use function Results\ResultsTableHtml\make_table_start;
use function Results\GetResults2026\Rows\make_one_row_new_inprocess;

function make_results_table_inprocess(
    $inprocessTable,
    $langCode,
    $cat,
    $camp,
    $inProgressButton,
    $fullTrUser,
    $globalUsername,
    $titlesInfos,
    $endpoint,
    $userCoord
): string {

    // $inprocessTable = normalizeItems($inprocessTable);

    $frist = make_table_start(true, $inProgressButton);

    $list = "";
    $counter = 1;

    foreach ($inprocessTable as $title => $titleTab) {

        if (empty($title)) {
            continue;
        }

        $title = str_replace("_", " ", $title);

        $titleData = $titlesInfos[$title] ?? [];

        // { "title": "Andes virus infection", "user": "Mr. Ibrahem", "lang": "ar", "cat": "RTT", "translate_type": "all", "word": 0, "add_date": "2026-05-21 00:00:00", "campaign": "Main", "autonym": "العربية" }
        $traType = $titleTab['translate_type'] ?? '';

        $isFull = false;

        if (strtolower(substr($title, 0, 6)) == 'video:') {
            $traType = 'all';
            $isFull = true;
        };

        $row = make_one_row_new_inprocess(
            $title,
            $traType,
            $counter,
            $langCode,
            $cat,
            $camp,
            $titleTab,
            $inProgressButton,
            $isFull,
            $fullTrUser,
            $globalUsername,
            $titleData,
            $endpoint,
            $userCoord
        );
        //--
        $list .= $row;

        $counter++;
    };

    $last = <<<HTML
        </tbody>
    </table>
    HTML;

    return $frist . $list . $last;
}
