<?PHP

namespace Results\GetResults2026;

use function Results\ResultsTableHtml\make_table_start;
use function Results\GetResults2026\Rows\make_one_row_new_inprocess;

function make_results_table_inprocess(
    $inprocess_table,
    $langcode,
    $cat,
    $camp,
    $inProgressBtn,
    $full_tr_user,
    $global_username,
    $titlesInfos,
    $endpoint,
    $user_coord
): string {
    //---
    // $inprocess_table = normalizeItems($inprocess_table);
    //---
    $frist = make_table_start(true, $inProgressBtn);
    //---
    $list = "";
    $cnt = 1;
    //---
    foreach ($inprocess_table as $title => $title_tab) {
        // ---
        if (empty($title)) continue;
        // ---
        $title = str_replace('_', ' ', $title);
        //---
        $title_data = $titlesInfos[$title] ?? [];
        //---
        // { "title": "Andes virus infection", "user": "Mr. Ibrahem", "lang": "ar", "cat": "RTT", "translate_type": "all", "word": 0, "add_date": "2026-05-21 00:00:00", "campaign": "Main", "autonym": "العربية" }
        $traType = $title_tab['translate_type'] ?? '';
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
            $title_tab,
            $inProgressBtn,
            $full,
            $full_tr_user,
            $global_username,
            $title_data,
            $endpoint,
            $user_coord
        );
        //--
        $list .= $row;
        //---
        $cnt++;
    };
    // ---
    $last = <<<HTML
        </tbody>
    </table>
    HTML;
    // ---
    return $frist . $list . $last;
}
