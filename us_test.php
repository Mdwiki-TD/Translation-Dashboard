<?PHP
//---
require('users.php');
//---
// make_user_table("Avicenno", "");
//---
$limit = isset($_GET["limit"]) ? $_GET["limit"] : 10;
//---
make_user_table("Subas Chandra Rout", "", $limit);
//---
?>