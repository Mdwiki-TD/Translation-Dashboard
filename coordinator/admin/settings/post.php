<?PHP
//---
use function Actions\MdwikiSql\update_settings;
//---
if (isset($_POST['se'])) {
    // var_dump(json_encode($_POST));
    $se   = $_POST['se'] ?? [];
    foreach ($se as $index => $n) {
        //---
        // {"id_":"1","title_":"allow_type_of_translate","displayed_":"Allow whole translate","value_":"0","type_":"check"}
        //---
        $id       = $_POST["id_$n"] ?? '';
        $title    = $_POST["title_$n"] ?? '';
        $displayed= $_POST["displayed_$n"] ?? '';
        $type     = $_POST["type_$n"] ?? '';
        $value    = $_POST["value_$n"] ?? '';
        //---
        if (empty($title) || empty($displayed) || empty($type)) continue;
        //---
        $re = update_settings($id, $title, $displayed, $value, $type);
    }
}
