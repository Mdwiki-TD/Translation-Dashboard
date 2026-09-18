<?PHP

namespace Results\ResultsTableHtml;

/*
Usage:

use function Results\ResultsTableHtml\make_table_start;

*/

function make_table_start($inprocess, $in_progress_translation_button)
{

    $Translate_th = "<th><span>Translate</span></th>";
    $type_th = ($inprocess) ? '<th class="spannowrap" style="text-align:center">Type</th>' : '';

    $table_classes = "display table_responsive_main";

    $inprocess_first = ($inprocess) ? '<th>user</th><th>date</th>' : '';

    $frist = <<<HTML
        <table class="table compact table-striped table_100 table_text_left $table_classes">
            <thead>
                <tr>
                    <th class="num">
                        #
                    </th>
                    <th class="spannowrap" style="text-align:center">
                        Title
                    $Translate_th
                    $type_th
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
                    <th class="spannowrap" style="text-align:center">
                        <span data-bs-toggle="tooltip" data-bs-title="Wikidata identifier">Qid</span>
                    </th>
                    $inprocess_first
                </tr>
            </thead>
            <tbody>
    HTML;

    return $frist;
}
