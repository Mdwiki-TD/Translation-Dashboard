<?php
namespace Actions\LoadRequest;

use Tables\SqlTables\TablesSql;
use Tables\Langs\LangsTables;

function load_request()
{
    $code = htmlspecialchars($_GET['code'] ?? '', ENT_QUOTES, 'UTF-8');
    if ($code == 'undefined') $code = "";
    $code = LangsTables::$L_lang_to_code[$code] ?? $code;
    $code_lang_name = LangsTables::$L_code_to_lang[$code] ?? '';
    $cat  = htmlspecialchars($_GET['cat'] ?? 'All', ENT_QUOTES, 'UTF-8');
    if ($cat == 'undefined') $cat = "";
    $camp = htmlspecialchars($_GET['camp'] ?? 'All', ENT_QUOTES, 'UTF-8');
    if (empty($cat) && !empty($camp)) {
        $cat = TablesSql::$s_camp_to_cat[$camp] ?? $cat;
    }
    if (!empty($cat) && empty($camp)) {
        $camp = TablesSql::$s_cat_to_camp[$cat] ?? $camp;
    }
    return [
        'code' => $code,
        'cat' => $cat,
        'camp' => $camp,
        'code_lang_name' => $code_lang_name
    ];
}