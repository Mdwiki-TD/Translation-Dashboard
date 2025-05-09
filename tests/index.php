<?PHP
//---
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include_once __DIR__ . '/../header.php';
echo <<<HTML
    <div class='container'>
    <div class='card'>
        <div class='card-header'>
        </div>
        <div class='card-body mb-0'>

    HTML;
//---
// disply all php files in the tests folder
$files = glob(__DIR__ . '/*.php');
//---
foreach ($files as $file) {
    if (strpos($file, 'index.php') === false) {
        $name = basename($file);
        echo "<a href='$name'>" . $name . "</a><br>";
    }
}
//---
echo <<<HTML
        </div>
    </div>
    </div>
    <br>
    HTML;
//---
include_once __DIR__ . '/../footer.php';
