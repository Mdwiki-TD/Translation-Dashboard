<?PHP
//---
$stats = http_build_query($_GET, '', '&', PHP_QUERY_RFC3986);
//---
HEADER("Location: /auth/index.php?$stats");
