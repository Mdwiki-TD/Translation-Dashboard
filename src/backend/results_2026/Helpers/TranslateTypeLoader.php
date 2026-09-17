<?PHP

namespace Results\GetResults2026;

use function SQLorAPI\GetDataTab\get_td_or_sql_translate_type;

function load_translate_type($ty)
{
    static $full_translates = [];
    static $no_lead_translates = [];

    if (empty($full_translates)) {
        $rere = get_td_or_sql_translate_type();
        //---
        foreach ($rere as $k => $tab) {
            // if tt_full == 1 then add tt_title to $full_translates
            if ($tab['tt_full'] == 1) {
                $full_translates[] = $tab['tt_title'];
            }
            if ($tab['tt_lead'] == 0) {
                $no_lead_translates[] = $tab['tt_title'];
            }
        }
    }
    // ---
    $tab = ($ty == 'full') ? $full_translates : $no_lead_translates;
    // ---
    return $tab;
}
