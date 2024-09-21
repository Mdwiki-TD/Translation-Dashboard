<?php
//---
if (user_in_coord == false) {
    echo "<meta http-equiv='refresh' content='0; url=index.php'>";
    exit;
};
//---
include_once 'actions/functions.php';
//---
use function Actions\MdwikiSql\execute_query;
use function Actions\Html\add_quotes;
//---
echo '</div><script>
    $("#mainnav").hide();
    $("#maindiv").hide();
</script>
<div class="container-fluid">';
//---
if (isset($_REQUEST['test'])) {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
};
//---
$id         = $_REQUEST['id'] ?? '';
$title      = $_REQUEST['title'] ?? '';
$target     = $_REQUEST['target'] ?? '';
$lang       = $_REQUEST['lang'] ?? '';
$user       = $_REQUEST['user'] ?? '';
$pupdate    = $_REQUEST['pupdate'] ?? '';
$table      = $_REQUEST['table'] ?? 'pages';
//---
echo <<<HTML
<div class='card'>
    <div class='card-header'>
        <h4>Edit Page (id: $id, table: $table)</h4>
    </div>
    <div class='card-body'>
HTML;
//---
function delete_page($id, $table)
{
    $qua = "DELETE FROM $table WHERE id = ?";
    // ---
    $params = [$id];
    // ---
    execute_query($qua, $params);
    //---
    // green text success
    echo <<<HTML
        <div class='alert alert-success' role='alert'>Page updated<br>
            window will close in 3 seconds
        </div>
        <!-- close window after 3 seconds -->
        <script>
            setTimeout(function() {
                window.close();
            }, 3000);
        </script>
    HTML;
}
function edit_page($id, $title, $target, $lang, $user, $pupdate, $table)
{
    //---
    $qua = "UPDATE $table
    SET
        title = ?,
        target = ?,
        lang = ?,
        user = ?,
        pupdate = ?
    WHERE
        id = ?
    ";
    $params = [$title, $target, $lang, $user, $pupdate, $id];
    //---
    execute_query($qua, $params);
    //---
    if (isset($_REQUEST['test'])) {
        echo "<pre>$qua</pre>";
        echo "<pre>$params</pre>";
    }
    //---
    // green text success
    echo <<<HTML
        <div class='alert alert-success' role='alert'>Page updated<br>
            window will close in 3 seconds
        </div>
        <!-- close window after 3 seconds -->
        <script>
            setTimeout(function() {
                window.close();
            }, 3000);
        </script>
    HTML;
}
//---

function echo_form($id, $title, $target, $lang, $user, $pupdate, $table)
{
    $test_line = (isset($_REQUEST['test'])) ? "<input name='test' value='1' hidden/>" : "";

    $title2 = add_quotes($title);
    $target2 = add_quotes($target);
    //---
    echo <<<HTML
        <form action='coordinator.php?ty=translated/edit_page&nonav=120' method='POST'>
            <input type='text' id='id' name='id' value='$id' hidden/>
            <input name='edit' value="1" hidden/>
            <input name='table' value="$table" hidden/>
            $test_line
            <div class='container'>
                <div class='row'>
                    <div class='col-md-3'>
                        <div class='input-group mb-3'>
                            <div class='input-group-prepend'>
                                <span class='input-group-text'>Title</span>
                            </div>
                            <input class='form-control' type='text' id='title' name='title' value=$title2 required/>
                        </div>
                    </div>
                    <div class='col-md-3'>
                        <div class='input-group mb-3'>
                            <div class='input-group-prepend'>
                                <span class='input-group-text'>lang</span>
                            </div>
                            <input class='form-control' type='text' id='lang' name='lang' value='$lang' required/>
                        </div>
                    </div>
                    <div class='col-md-3'>
                        <div class='input-group mb-3'>
                            <div class='input-group-prepend'>
                                <span class='input-group-text'>target</span>
                            </div>
                            <input class='form-control' type='text' id='target' name='target' value=$target2 required/>
                        </div>
                    </div>
                    <div class='col-md-3'>
                        <div class='input-group mb-3'>
                            <div class='input-group-prepend'>
                                <span class='input-group-text'>user</span>
                            </div>
                            <input class='form-control' type='text' id='user' name='user' value='$user' required/>
                        </div>
                    </div>
                    <div class='col-md-3'>
                        <div class='input-group mb-3'>
                            <div class='input-group-prepend'>
                                <span class='input-group-text'>Publication date</span>
                            </div>
                            <input class='form-control' type='text' id='pupdate' name='pupdate' value='$pupdate' placeholder='YYYY-MM-DD' required/>
                        </div>
                    </div>
                </div>
                <div class='row'>
                    <div class='col-6'>
                        <input class='btn btn-outline-primary' type='submit' value='send'/>
                    </div>
                    <div class='col-6'>
                        <input class='btn btn-danger btn-sm' type='button' value='Delete' onclick="window.location.href='coordinator.php?ty=translated/edit_page&table=$table&nonav=120&delete=$id&id=$id'"/>
                    </div>
                </div>
            </div>
        </form>
    HTML;
}
//---
if (isset($_REQUEST['delete'])) {
    delete_page($_REQUEST['delete'], $table);
} elseif (isset($_REQUEST['edit'])) {
    edit_page($id, $title, $target, $lang, $user, $pupdate, $table);
} else {
    echo_form($id, $title, $target, $lang, $user, $pupdate, $table);
}
//---
echo <<<HTML
    </div>
</div>
HTML;
//---
