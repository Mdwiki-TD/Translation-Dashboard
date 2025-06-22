<?PHP

use function Leaderboard\Camps\get_articles_to_camps;

function camps_list()
{
    // ---
    $articles_to_camps = get_articles_to_camps();
    // ---
    $table = <<<HTML
        <table class='table table-striped sortable'>
            <thead>
                <tr>
                    <th>Member</th>
                    <th>Length</th>
                    <th>Campaigns</th>
                </tr>
            </thead>
            <tbody>
    HTML;
    // ---
    foreach ($articles_to_camps as $member => $camps) {
        // ---
        sort($camps);
        // ---
        $count = count($camps);
        // ---
        $table .= <<<HTML
            <tr>
                <td>$member</td>
                <td>$count</td>
                <td>
                    <ul>
        HTML;
        // ---
        foreach ($camps as $camp) {
            $table .= "<li>$camp</li>";
        };
        // ---
        $table .= <<<HTML
                    </ul>
                </td>
            </tr>
        HTML;
        // ---
    };
    // ---
    $table .= <<<HTML
            </tbody>
        </table>
    HTML;
    // ---
    echo $table;
    // ---
}

function camps_list2()
{
    $articles_to_camps = get_articles_to_camps();

    $shared_counts = [];

    foreach ($articles_to_camps as $article => $camps) {
        $camps = array_unique($camps);
        sort($camps); // ترتيب أبجدي لضمان تطابق المفاتيح
        $pair_key = implode(', ', $camps);

        if (!isset($shared_counts[$pair_key])) {
            $shared_counts[$pair_key] = 0;
        }
        $shared_counts[$pair_key]++;
    }


    // sort shared_counts by value
    arsort($shared_counts);

    // بناء الجدول
    $table = <<<HTML
        <table class='table table-striped ttxx'>
            <thead>
                <tr>
                    <th>Campaign</th>
                    <th>Articles</th>
                </tr>
            </thead>
            <tbody>
    HTML;

    foreach ($shared_counts as $pair => $count) {
        $table .= <<<HTML
            <tr>
                <td>$pair</td>
                <td>$count</td>
            </tr>
        HTML;
    }

    $table .= <<<HTML
            </tbody>
        </table>
    HTML;

    echo <<<HTML
        <div class="container-fluid col-9">
            <div class="card card2">
                <div class="card-header">
                    <span class="card-title" style="font-weight:bold;">
                        Camps
                    </span>
                    <div class="card-tools">
                        <button type="button" class="btn-tool" data-card-widget="collapse"><i
                                class="fas fa-minus"></i></button>
                    </div>
                </div>
                <div class="card-body1 card2">
                    $table
                </div>
            </div>
        </div>
    HTML;;
}

function campaigns_with_articles_table()
{
    $articles_to_camps = get_articles_to_camps();

    $grouped = [];

    foreach ($articles_to_camps as $article => $camps) {
        $camps = array_unique($camps);
        sort($camps);
        $key = implode(', ', $camps);

        if (!isset($grouped[$key])) {
            $grouped[$key] = [];
        }

        $grouped[$key][] = $article;
    }

    ksort($grouped);

    $table = <<<HTML
        <h3>إحصائية الحملات والمقالات</h3>
        <table class="table table-bordered sortable">
            <thead>
                <tr>
                    <th>الحملات</th>
                    <th>عدد المقالات</th>
                </tr>
            </thead>
            <tbody>
    HTML;

    foreach ($grouped as $campaign_key => $articles) {
        $article_count = count($articles);
        $article_list_id = md5($campaign_key); // معرف فريد لكل مجموعة

        $article_items = "";
        foreach ($articles as $a) {
            $article_items .= "<li>$a</li>";
        }

        $table .= <<<HTML
            <tr>
                <td>
                    <a href="#" onclick="toggleList('$article_list_id'); return false;">$campaign_key</a>
                    <ul id="$article_list_id" style="display: none; margin-top: 10px;">$article_items</ul>
                </td>
                <td>$article_count</td>
            </tr>
        HTML;
    }

    $table .= <<<HTML
            </tbody>
        </table>

        <script>
            function toggleList(id) {
                var el = document.getElementById(id);
                el.style.display = (el.style.display === 'none') ? 'block' : 'none';
            }
        </script>
    HTML;

    echo $table;
}

camps_list2();
// ---
campaigns_with_articles_table();

echo <<<HTML
<script>
	$('.ttx').DataTable({
		stateSave: false,
		paging: false,
		info: false,
		searching: false
	});
</script>
HTML;
