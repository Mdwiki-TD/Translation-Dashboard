<?php

namespace App\Leaderboard\Helpers\Filters;

class FilterForm
{
    public static function DropdownNew(string $title, array $tab, string $cat, string $id): string
    {
        $options = "";

        foreach ($tab as $item) {
            if (empty($item)) continue;
            $se = ($cat == $item) ? 'selected' : '';

            $options .= <<<HTML
                <option value='$item' $se>$item</option>
            HTML;
        }

        return <<<HTML
            <select dir="ltr" id="$id" name="$id" class="form-select" data-bs-theme="auto">
                <option value='all'>$title: All</option>
                $options
            </select>
        HTML;
    }

    public static function makeFilterHtml(
        array $filterData,
        array $gData,
        string $filterPage,
        ?bool $addcampDropdown = null
    ): string {
        // Extract array values safely using null coalescing operator to prevent undefined index notices
        $lang     = $filterData['lang'] ?? '';
        $user     = $filterData['user'] ?? '';
        $year     = $filterData['year'] ?? '';
        $campaign = $filterData['camp'] ?? '';

        $yearDropdown = self::DropdownNew('Year', $gData["years"], $year, 'year');

        if ($filterPage === 'user') {

            $langsDropdown = self::DropdownNew('Lang', $gData["langs"], $lang, 'lang');

            $Dropdown = <<<HTML
                <div class="col-6">
                    $langsDropdown
                </div>
                <div class="col-6">
                    $yearDropdown
                </div>
            HTML;

            if ($addcampDropdown) {
                $campDropdown  = self::DropdownNew('Camp', $gData["camps"], $campaign, 'camp');
                $Dropdown .= <<<HTML
                    <div class="col-4">
                        $campDropdown
                    </div>
                HTML;
            }

            // Hidden inputs are required for GET forms to preserve query parameters
            $hidden = <<<HTML
                <input type="hidden" name="get" value="users" />
                <input type="hidden" name="user" value="$user" />
            HTML;
        } else {

            $Dropdown = <<<HTML
                <div class="col-12">
                    $yearDropdown
                </div>
            HTML;

            // Hidden inputs are required for GET forms to preserve query parameters
            $hidden = <<<HTML
                <input type="hidden" name="get" value="langs" />
                <input type="hidden" name="langcode" value="$lang" />
            HTML;
        }

        return <<<HTML
            <form method="GET" action="leaderboard.php" class="border rounded">
                $hidden
                <div class='container mt-3'>
                    <div class='row g-1'>
                        $Dropdown
                        <div class="col-12 mt-1">
                            <button type="submit" class="btn btn-sm btn-outline-primary w-100">Filter</button>
                        </div>
                    </div>
                </div>
            </form>
        HTML;
    }

    public static function leadRow(
        array $table1,
        string $graph,
        string $mainTitle,
        array $filterData,
        array $gData,
        string $filterPage
    ): string {
        // $table1 = ['total_articles' => $total_articles, 'total_words' => $total_words, 'total_views' => $total_views];

        $total_articles = number_format($table1['total_articles'] ?? 0);
        $total_words    = number_format($table1['total_words'] ?? 0);
        $total_views    = number_format($table1['total_views'] ?? 0);

        $table1_html = <<<HTML
            <div class="text-muted">
                Articles: <strong>$total_articles</strong> &nbsp;
                Words: <strong>$total_words</strong> &nbsp;
                Pageviews: <strong><span id="hrefjsontoaddzz">$total_views</span></strong>
            </div>
        HTML;

        $filterForm = self::makeFilterHtml($filterData, $gData, $filterPage);

        return <<<HTML
            <div class='container-fluid'>
                <div class='row g-1'>
                    <div class='col-lg-4 col-md-12 border_debug border rounded'>
                        <div class="d-flex align-items-center justify-content-center" style="height: 100%">
                            <div class="list-group">
                                $mainTitle
                                <div class="d-flex align-items-center justify-content-center " style="height: 100%">
                                    $table1_html
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class='col-lg-5 col-md-6'>
                        <div class="position-relative py-1 border rounded">
                            $graph
                        </div>
                    </div>
                    <div class='col-lg-3 col-md-6 border_debug'>
                        $filterForm
                    </div>
                </div>
            </div>
        HTML;
    }
}
