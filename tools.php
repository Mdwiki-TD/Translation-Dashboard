<?PHP
//---
$stats = http_build_query($_GET);
//---
HEADER("Location: /tdc/index.php?$stats");
