<?PHP
//---
$stats = http_build_query($_GET, '', '&', PHP_QUERY_RFC3986);
//---
HEADER("Location: /Translation_Dashboard/translate_med/index.php?$stats");
