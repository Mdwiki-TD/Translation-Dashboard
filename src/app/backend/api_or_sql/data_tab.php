<?php

namespace App\SQLorAPI\GetDataTab;

use App\SQLorAPI\Titles\TitlesTable;
use App\SQLorAPI\Views\ViewsTable;
use App\SQLorAPI\Settings\SettingsTable;
use App\SQLorAPI\Categories\CategoriesTable;
use App\SQLorAPI\Users\UsersTable;
use App\SQLorAPI\Pages\PagesTable;

function get_td_or_sql_titles_infos()
{
    return TitlesTable::getTdOrSqlTitlesInfos();
}

function get_td_or_sql_views($year, $lang)
{
    return ViewsTable::getTdOrSqlViews($year, $lang);
}

function get_td_or_sql_settings()
{
    return SettingsTable::getTdOrSqlSettings();
}

function get_td_or_sql_projects()
{
    return TitlesTable::getTdOrSqlProjects();
}

function get_td_or_sql_categories()
{
    return CategoriesTable::getTdOrSqlCategories();
}

function get_td_or_sql_categories_members($category)
{
    return CategoriesTable::getTdOrSqlCategoriesMembers($category);
}

function get_td_or_sql_qids()
{
    return TitlesTable::getTdOrSqlQids();
}

function get_td_or_sql_users_no_inprocess()
{
    return UsersTable::getTdOrSqlUsersNoInprocess();
}

function get_td_or_sql_full_translators($column = null)
{
    return UsersTable::getTdOrSqlFullTranslators($column);
}

function get_td_or_sql_translate_type(): array
{
    return TitlesTable::getTdOrSqlTranslateType();
}

function get_td_or_sql_count_pages()
{
    return PagesTable::getCountPages();
}

function get_td_or_sql_langs()
{
    return TitlesTable::getTdOrSqlLangs();
}

function get_qids($list)
{
    return TitlesTable::getQids($list);
}

function get_camps_to_cat()
{
    return CategoriesTable::getCampsToCat();
}

function get_endpoint_old()
{
    return SettingsTable::getEndpointOld();
}

function get_endpoint()
{
    return SettingsTable::getEndpoint();
}
