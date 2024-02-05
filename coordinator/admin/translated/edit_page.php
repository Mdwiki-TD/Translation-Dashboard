<?php
//---
if (user_in_coord == false) {
	echo "<meta http-equiv='refresh' content='0; url=index.php'>";
	exit;
};
//---
?>
</div>
<script> 
    $('#mainnav').hide();
    $('#maindiv').hide();
</script>
<div class="container-fluid">
<?PHP
//---
if (isset($_REQUEST['test'])) {
	ini_set('display_errors', 1);
	ini_set('display_startup_errors', 1);
	error_reporting(E_ALL);
};
//---
include_once 'functions.php';
//---
$tabs = array();
//---
$id         = $_REQUEST['id'] ?? '';
$title      = $_REQUEST['title'] ?? '';
$target     = $_REQUEST['target'] ?? '';
$lang       = $_REQUEST['lang'] ?? '';
$user       = $_REQUEST['user'] ?? '';
$pupdate    = $_REQUEST['pupdate'] ?? '';
//---
echo <<<HTML
<div class='card'>
    <div class='card-header'>
        <h4>Edit Page (id: $id)</h4>
    </div>
    <div class='card-body'>
HTML;
//---
function edit_page($id, $title, $target, $lang, $user, $pupdate) {
    //---
    $qua = "UPDATE pages
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
function echo_form($id, $title, $target, $lang, $user, $pupdate) {
    echo <<<HTML
        <form action='coordinator.php?ty=translated/edit_page&nonav=120' method='POST'>
            <input type='text' id='id' name='id' value='$id' hidden/>
            <input name='edit' value="1" hidden/>
            <div class='container'>
                <div class='row'>
                    <div class='col-md-3'>
                        <div class='input-group mb-3'>
                            <div class='input-group-prepend'>
                                <span class='input-group-text'>Title</span>
                            </div>
                            <input class='form-control' type='text' id='title' name='title' value='$title' required/>
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
                            <input class='form-control' type='text' id='target' name='target' value='$target' required/>
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
                                <span class='input-group-text'>pupdate</span>
                            </div>
                            <input class='form-control' type='text' id='pupdate' name='pupdate' value='$pupdate' required/>
                        </div>
                    </div>
                    <div class='col-md-2'>
                        <input class='btn btn-primary' type='submit' value='send'/>
                    </div>
                </div>
            </div>
        </form>
    HTML;
}
//---
if ( isset($_REQUEST['edit']) ) {
    edit_page($id, $title, $target, $lang, $user, $pupdate);
} else {
    echo_form($id, $title, $target, $lang, $user, $pupdate);
}
//---
echo <<<HTML
    </div>
</div>
HTML;
//---
