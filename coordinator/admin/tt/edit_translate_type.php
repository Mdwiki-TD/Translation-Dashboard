<?php
//---
if (user_in_coord == false) {
    echo "<meta http-equiv='refresh' content='0; url=index.php'>";
    exit;
};
//---
include_once 'actions/functions.php';

use function Actions\MdwikiSql\insert_to_translate_type;
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
$tabs = array();
//---
$title  = (isset($_REQUEST['title'])) ? rawurldecode($_REQUEST['title']) : "";
$lead    = $_REQUEST['lead'] ?? '';
$full    = $_REQUEST['full'] ?? '';
$id     = $_REQUEST['id'] ?? '';
//---
echo <<<HTML
<div class='card'>
    <div class='card-header'>
        <h4>Edit Translate type</h4>
    </div>
    <div class='card-body'>
HTML;
//---
function send_qid($id, $title, $lead, $full)
{
    //---
    insert_to_translate_type($title, $lead, $full, $tt_id = $id);
    //---
    // green text success
    echo <<<HTML
        <div class='alert alert-success' role='alert'>Translate type updated<br>
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
function echo_form($title, $lead, $full, $id)
{
    $lead_checked = ($lead == 1 || $lead == "1") ? 'checked' : '';
    $full_checked = ($full == 1 || $full == "1") ? 'checked' : '';
    //---
    $title2 = add_quotes($title);
    //---
    echo <<<HTML
        <form action='coordinator.php?ty=tt/edit_translate_type&nonav=120' method='POST'>
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
                            <input class='form-control' type='text' id='title' name='title' value=$title2 required/>
                        </div>
                    </div>
                    <div class='col-md-3'>
                        <div class='row'>
                            <div class='col'>
                                <div class='input-group mb-3'>
                                    <div class='input-group-prepend'>
                                        <span class='input-group-text'>Lead</span>
                                    </div>
                                    <div class='form-check form-switch'>
                                        <input type='hidden' name='lead' value='0'>
                                        <input class='form-check-input' type='checkbox' name='lead' value='1' $lead_checked>
                                    </div>
                                </div>
                            </div>
                            <div class='col'>
                                <div class='input-group mb-3'>
                                    <div class='input-group-prepend'>
                                        <span class='input-group-text'>Full</span>
                                    </div>
                                    <div class='form-check form-switch'>
                                        <input type='hidden' name='full' value='0'>
                                        <input class='form-check-input' type='checkbox' name='full' value='1' $full_checked>
                                    </div>
                                </div>
                            </div>
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
    send_qid($id, $title, $lead, $full);
    //---
} else {
    echo_form($title, $lead, $full, $id);
}
//---
echo <<<HTML
    </div>
</div>
HTML;
//---
