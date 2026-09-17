<?PHP

namespace Results\GetResults2026;

function make_results_table_exists_2026(
    $items,
    $langcode,
    $cat,
    $camp,
    $globalUsername,
    $userCoord,
    $endpoint
) {
    //---
    $list = "";
    //---
    $cnt = 1;
    $countTranslated = 0;
    $countTranslatedBefore = 0;
    //---
    foreach ($items as $title => $targetTab) {

        if (empty($title)) continue;

        $title = str_replace('_', ' ', $title);
        //---
        if ($targetTab["via"] === "td") {
            $countTranslated += 1;
        } else {
            $countTranslatedBefore += 1;
        }
        //---
        $row = make_one_row_exists_2026(
            $title,
            $cnt,
            $langcode,
            $cat,
            $camp,
            $targetTab,
            $globalUsername,
            $userCoord,
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
                        Translated ($countTranslated)
                    </th>
                    <th class="">
                        Translated before ($countTranslatedBefore)
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
