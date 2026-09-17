<?PHP

namespace Results\GetResults2026;

function make_results_table_exists_2026(
    $items,
    $langCode,
    $cat,
    $camp,
    $globalUsername,
    $userCoord,
    $endpoint
) {


    $countTranslated       = 0;
    $countTranslatedBefore = 0;
    $rowsHtml              = '';
    $counter               = 1;

    foreach ($items as $title => $data) {
        if (empty($title)) {
            continue;
        }

        $title = str_replace("_", " ", $title);

        if ($data["via"] === "td") {
            $countTranslated++;
        } else {
            $countTranslatedBefore++;
        }

        $rowsHtml .= make_one_row_exists_2026(
            $title,
            $counter,
            $langCode,
            $cat,
            $camp,
            $data,
            $globalUsername,
            $userCoord,
            $endpoint
        );

        $counter++;
    };

    $th22 = <<<HTML
        <th class="spannowrap" style="text-align:center">
            <span data-bs-toggle="tooltip" data-bs-title="Page views in last month in English Wikipedia">Views</span>
        </th>
        <th class="spannowrap" style="text-align:center">
            <span data-bs-toggle="tooltip" data-bs-title="Page important from medicine project in English Wikipedia">Importance</span>
        </th>
        <th class="spannowrap" style="text-align:center">
            <span data-bs-toggle="tooltip" data-bs-title="number of words of the article in mdwiki.org">Words</span>
        </th>
        <th class="spannowrap" style="text-align:center">
            <span data-bs-toggle="tooltip" data-bs-title="number of references of the article in mdwiki.org">Refs.</span>
        </th>
    HTML;

    $th22 = "";

    return <<<HTML
        <table class="table compact table-striped table_100 table_text_left table_responsive display">
            <thead>
                <tr>
                    <th class="num">
                        #
                    </th>
                    <th class="spannowrap" style="text-align:center">
                        Title
                    </th>
                    <th>
                        Translate
                    </th>
                    <th>
                        Translated ({$countTranslated})
                    </th>
                    <th>
                        Translated before ({$countTranslatedBefore})
                    </th>
                    $th22
                    <th class="spannowrap" style="text-align:center">
                        <span data-bs-toggle="tooltip" data-bs-title="Wikidata identifier">Qid</span>
                    </th>
                </tr>
            </thead>
            <tbody>
                {$rowsHtml}
            </tbody>
        </table>
    HTML;
}
