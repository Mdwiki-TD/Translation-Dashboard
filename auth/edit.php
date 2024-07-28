<?php

include_once __DIR__ . '/send_edit.php';

$title = 'وب:ملعب';

$summary = 'Hello World';

$wiki = 'ar';

$text = "new!";

$editit = do_edit($title, $text, $summary, $wiki);

echo "\n== You made an edit ==\n\n";

print_r($editit);
