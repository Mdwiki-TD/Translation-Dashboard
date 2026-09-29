<?php

namespace App\SQLorAPI\Funcs;

use App\SQLorAPI\Categories\CategoriesTable;

function missing_by_lang_and_category($lang_code, $category)
{
    return CategoriesTable::missingByLangAndCategory($lang_code, $category);
}

function exists_by_lang_and_category($lang_code, $category)
{
    return CategoriesTable::existsByLangAndCategory($lang_code, $category);
}

function count_category_members($category)
{
    return CategoriesTable::countCategoryMembers($category);
}

function statics_by_category($category)
{
    return CategoriesTable::staticsByCategory($category);
}
