<?PHP
require('last.php');
//---
/*
$new_q = "INSERT INTO users (username, email, wiki) SELECT DISTINCT user, '', '' from pages
WHERE NOT EXISTS (SELECT 1 FROM users WHERE username = user)";
//---
// quary2($new_q);
//---
$nn = 0;
foreach(quary2('SELECT count(DISTINCT user) as c from pages;') as $k => $tab) $nn = $tab['c'];
//---
echo "<h4>Users: ($nn user):</h4>";
//---
*/
?>