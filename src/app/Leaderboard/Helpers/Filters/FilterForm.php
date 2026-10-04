<?php

namespace App\Leaderboard\Helpers\Filters;

use App\SQLorAPI\PagesTable;

class FilterForm
{
    public static function DropdownNew(string $title, array $tab, string $cat, string $id): string
    {
        $options = "";

        foreach ($tab as $dd) {
            if (empty($dd)) continue;
            $se = ($cat == $dd) ? 'selected' : '';

            $options .= <<<HTML
                <option value='$dd' $se>$dd</option>
            HTML;
        }

        return <<<HTML
            <select dir="ltr" id="$id" name="$id" class="form-select" data-bs-theme="auto">
                <option value='all'>$title: All</option>
                $options
            </select>
        HTML;
    }

    public static function make_filter_html(array $data, string $filter_page): string
    {

        // $filter_data = ["user" => "", "lang" => $mainlang, "year" => $year_y];

        $lang     = $data['lang'];
        $year     = $data['year'];
        $user     = $data['user'];
        $camp     = $data['camp'];

        if ($filter_page == 'user') {
            $years = (PagesTable::getInstance())->getUserYears($user);
            $langs = (PagesTable::getInstance())->getUserLangs($user);
            $camps = (PagesTable::getInstance())->getUserCamps($user);

            $langsDropdown = self::DropdownNew('Lang', $langs, $lang, 'lang');
            $yearDropdown  = self::DropdownNew('Year', $years, $year, 'year');

            // $campDropdown  = DropdownNew('Camp', $camps, $camp, 'camp');
            // <!-- <div class="col-4"> $campDropdown </div> -->

            $Dropdown = <<<HTML
                <div class="col-6">
                    $langsDropdown
                </div>
                <div class="col-6">
                    $yearDropdown
                </div>
            HTML;

            $hidden = "<input type='hidden' name='get' value='users' /><input type='hidden' name='user' value='$user' />";
        } else {
            $years = (PagesTable::getInstance())->getLangYears($lang);

            $yearDropdown = self::DropdownNew('Year', $years, $year, 'year');

            $Dropdown = <<<HTML
                <div class="col-12">
                    $yearDropdown
                </div>
            HTML;

            $hidden = "<input type='hidden' name='get' value='langs' /><input type='hidden' name='langcode' value='$lang' />";
        }

        return <<<HTML
            <form method="get" action="leaderboard.php" class="border rounded">
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

    public static function make_table1_html(array $table1): string
    {

        // $table1 = ['total_articles' => $total_articles, 'total_words' => $total_words, 'total_views' => $total_views];
        $total_articles = number_format($table1['total_articles']);
        $total_words = number_format($table1['total_words']);
        $total_views = number_format($table1['total_views']);

        return <<<HTML
            <div class="text-muted">
                Articles: <strong>$total_articles</strong> &nbsp;
                Words: <strong>$total_words</strong> &nbsp;
                Pageviews: <strong><span id="hrefjsontoadd">$total_views</span></strong>
            </div>
            HTML;
    }

    public static function lead_row(array $table1, string $graph, string $main_title, array $filter_data, string $filter_page): string
    {
        $table1_html = self::make_table1_html($table1);
        $filter_form = self::make_filter_html($filter_data, $filter_page);

        return <<<HTML
            <div class='container-fluid'>
                <div class='row g-1'>
                    <div class='col-lg-4 col-md-12 border_debug border rounded'>
                        <div class="d-flex align-items-center justify-content-center" style="height: 100%">
                            <div class="list-group">
                                $main_title
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
                        $filter_form
                    </div>
                </div>
            </div>
        HTML;
    }
}
