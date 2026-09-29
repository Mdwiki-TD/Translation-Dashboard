<?php

namespace App\SQLorAPI\GetDataTab;

use App\SQLorAPI\Titles\TitlesTable;
use App\SQLorAPI\Views\ViewsTable;
use App\SQLorAPI\Settings\SettingsTable;
use App\SQLorAPI\Categories\CategoriesTable;
use App\SQLorAPI\Users\UsersTable;
use App\SQLorAPI\Pages\PagesTable;

function get_titles_infos()
{
    return TitlesTable::getTitlesInfos();
}

function get_views($year, $lang)
{
    return ViewsTable::getViews($year, $lang);
}

function get_settings()
{
    return SettingsTable::getSettings();
}

function get_projects()
{
    return TitlesTable::getProjects();
}

function get_categories()
{
    return CategoriesTable::getCategories();
}

function get_categories_members($category)
{
    return CategoriesTable::getCategoriesMembers($category);
}

function get_users_no_inprocess()
{
    return UsersTable::getUsersNoInprocess();
}

function get_full_translators($column = null)
{
    return UsersTable::getFullTranslators($column);
}

function get_translate_type(): array
{
    return TitlesTable::getTranslateType();
}

function get_count_pages()
{
    return PagesTable::getCountPages();
}

function get_langs()
{
    return TitlesTable::getLangs();
}

function get_qids($list = null)
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
