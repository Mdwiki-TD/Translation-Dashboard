<?PHP
//---
$stats = http_build_query($_GET);
//---
HEADER("Location: index.php?$stats");
