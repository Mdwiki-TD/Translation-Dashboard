<?PHP
//---
$stats = http_build_query($_GET);
//---
HEADER("Location: /Translation_Dashboard/translate_med/index.php?$stats");
