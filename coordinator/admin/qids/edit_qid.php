<?php
//---
if (user_in_coord == false) {
    echo "<meta http-equiv='refresh' content='0; url=index.php'>";
    exit;
};
//---
include_once 'actions/functions.php';
//---
echo '</div><script>
    $("#mainnav").hide();
    $("#maindiv").hide();
</script>
<div class="container-fluid">';
//---
//---
if (isset($_REQUEST['test'])) {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
};
//---
$tabs = array();
//---
$title  = $_REQUEST['title'] ?? '';
$qid    = $_REQUEST['qid'] ?? '';
$id     = $_REQUEST['id'] ?? '';
//---
echo <<<HTML
<div class='card'>
    <div class='card-header'>
        <h4>Edit Qid</h4>
    </div>
    <div class='card-body'>
HTML;
//---
function send_qid($id, $title, $qid)
{
    //---
    $qua = "UPDATE qids
    SET
        title = ?,
        qid = ?
    WHERE
        id = ?
    ";
    $params = [$title, $qid, $id];
    //---
    execute_query($qua, $params);
    //---
    // green text success
    echo <<<HTML
        <div class='alert alert-success' role='alert'>Qid updated<br>
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

function echo_form($id, $title, $qid)
{
    echo <<<HTML
        <form action='coordinator.php?ty=qids/edit_qid&nonav=120' method='POST'>
            <input name='edit' value="1" hidden/>
            <div class='container'>
                <div class='row'>
                    <div class='col-md-3'>
                        <div class='input-group mb-3'>
                            <div class='input-group-prepend'>
                                <span class='input-group-text'>Id</span>
                            </div>
                            <input class='form-control' type='text' value='$id' disabled/>
                            <input class='form-control' type='text' id='id' name='id' value='$id' hidden/>
                        </div>
                    </div>
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
                                <span class='input-group-text'>Qid</span>
                            </div>
                            <input class='form-control' type='text' id='qid' name='qid' value='$qid' required/>
                        </div>
                    </div>
                    <div class='col-md-2'>
                        <input class='btn btn-outline-primary' type='submit' value='send'/>
                    </div>
                </div>
            </div>
        </form>
    HTML;
}
//---
if (isset($_REQUEST['edit'])) {
    send_qid($id, $title, $qid);
} else {
    echo_form($id, $title, $qid);
}
//---
echo <<<HTML
    </div>
</div>
HTML;
//---
