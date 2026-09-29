<?php

namespace App\SQLorAPI\Funcs;

use App\SQLorAPI\Pages\PagesTable;
use App\SQLorAPI\Users\UsersTable;
use App\SQLorAPI\Views\ViewsTable;

function get_lang_pages_by_cat($lang, $cat)
{
    return PagesTable::getLangPagesByCat($lang, $cat);
}

function get_coordinators()
{
    return UsersTable::getCoordinators();
}

function get_user_pages($user_main, $year_y, $lang_y)
{
    return PagesTable::getUserPages($user_main, $year_y, $lang_y);
}

function get_pages_with_pupdate()
{
    return PagesTable::getPagesWithPupdate();
}

function get_graph_data()
{
    return ViewsTable::getGraphData();
}

function get_lang_pages($lang, $year_y)
{
    return PagesTable::getLangPages($lang, $year_y);
}

function get_user_views($user, $year_y, $lang_y)
{
    return ViewsTable::getUserViews($user, $year_y, $lang_y);
}

function get_lang_views($mainlang, $year_y)
{
    return ViewsTable::getLangViews($mainlang, $year_y);
}

function get_lang_years($mainlang)
{
    return PagesTable::getLangYears($mainlang);
}

function get_user_years($user)
{
    return PagesTable::getUserYears($user);
}

function get_user_langs($user)
{
    return PagesTable::getUserLangs($user);
}

function get_user_camps($user)
{
    return PagesTable::getUserCamps($user);
}
