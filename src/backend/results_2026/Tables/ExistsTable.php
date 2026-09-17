<?PHP

namespace Results\GetResults2026;

function make_results_table_exists_2026(
    $items,
    $langcode,
    $cat,
    $camp,
    $global_username,
    $user_coord,
    $endpoint
) {
    //---
    $list = "";
    //---
    $cnt = 1;
    $count_translated = 0;
    $count_translated_before = 0;
    //---
    foreach ($items as $title => $target_tab) {

        if (empty($title)) continue;

        $title = str_replace('_', ' ', $title);
        //---
        if ($target_tab["via"] === "td") {
            $count_translated += 1;
        } else {
            $count_translated_before += 1;
        }
        //---
        $row = make_one_row_exists_2026(
            $title,
            $cnt,
            $langcode,
            $cat,
            $camp,
            $target_tab,
            $global_username,
            $user_coord,
            $endpoint
        );
        //---
        $list .= $row;
        //---
        $cnt++;
    };

    $th22 = <<<HTML
        <th class="spannowrap" style="text-align: center">
            <span data-bs-toggle="tooltip" data-bs-title="Page views in last month in English Wikipedia">Views</span>
        </th>
        <th class="spannowrap" style="text-align: center">
            <span data-bs-toggle="tooltip" data-bs-title="Page important from medicine project in English Wikipedia">Importance</span>
        </th>
        <th class="spannowrap" style="text-align: center">
            <span data-bs-toggle="tooltip" data-bs-title="number of words of the article in mdwiki.org">Words</span>
        </th>
        <th class="spannowrap" style="text-align: center">
            <span data-bs-toggle="tooltip" data-bs-title="number of references of the article in mdwiki.org">Refs.</span>
        </th>
    HTML;

    $th22 = "";

    $table = <<<HTML
        <table class="table compact table-striped table_100 table_text_left table_responsive display">
            <thead>
                <tr>
                    <th class="num">
                        #
                    </th>
                    <th class="spannowrap" style="text-align: center">
                        Title
                    </th>
                    <th class="">
                        <span class=''>Translate</span>
                    </th>
                    <th class="">
                        Translated ($count_translated)
                    </th>
                    <th class="">
                        Translated before ($count_translated_before)
                    </th>
                    $th22
                    <th class="spannowrap" style="text-align: center">
                        <span data-bs-toggle="tooltip" data-bs-title="Wikidata identifier">Qid</span>
                    </th>
                </tr>
            </thead>
            <tbody>
                $list
            </tbody>
        </table>
    HTML;

    return $table;
}
