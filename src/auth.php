<?PHP
//---
$stats = http_build_query($_GET);
//---
HEADER("Location: /auth/index.php?$stats");
