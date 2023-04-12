<?PHP
//---
$display_type = $_REQUEST['display_type'];
if (isset($display_type)) {
    $value = ($display_type == '0') ? false : true;
    set_configs('conf.json', 'allow_type_of_translate', $value);
    $conf = get_configs('conf.json');
};
//---
?>