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
$user   = $_REQUEST['user'] ?? '';
$wiki   = $_REQUEST['wiki'] ?? '';
$project= $_REQUEST['project'] ?? '';
$email  = $_REQUEST['email'] ?? '';
$id     = $_REQUEST['id'] ?? '';
//---
echo <<<HTML
<div class='card'>
    <div class='card-header'>
        <h4>Edit Users</h4>
    </div>
    <div class='card-body'>
HTML;
//---
function send_user($id, $user, $project, $wiki, $email) {
    //---
    if ($user != '') {
        //---
        $user = trim($user);
        $email     = trim($email);
        $wiki      = trim($wiki);
        $project   = trim($project);
        //---
        sql_add_user($user, $email, $wiki, $project, $id);
        //---
    };
    //---
    // green text success
    echo <<<HTML
        <div class='alert alert-success' role='alert'>User "$user" information updated<br>
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
function echo_form($user, $wiki, $project, $email, $id) {
    //---
    global $projects_title_to_id;
    //---
	$project_line = make_project_to_user($projects_title_to_id, $project);
	//---
    echo <<<HTML
        <form action='coordinator.php?ty=Emails/edit_user&nonav=120' method='POST'>
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
                                <span class='input-group-text'>User</span>
                            </div>
                            <input class='form-control' type='text' id='user' name='user' value='$user' required/>
                        </div>
                    </div>
                    <div class='col-md-3'>
                        <div class='input-group mb-3'>
                            <div class='input-group-prepend'>
                                <span class='input-group-text'>email</span>
                            </div>
                            <input class='form-control' type='text' id='email' name='email' value='$email'/>
                        </div>
                    </div>
                    <div class='col-md-3'>
                        <div class='input-group mb-3'>
                            <div class='input-group-prepend'>
                                <span class='input-group-text'>wiki</span>
                            </div>
                            <input class='form-control' type='text' id='wiki' name='wiki' value='$wiki'/>
                        </div>
                    </div>
                    <div class='col-md-3'>
                        <div class='input-group mb-3'>
                            <div class='input-group-prepend'>
                                <span class='input-group-text'>project</span>
                            </div>
                            <select name='project' class='form-select options'>$project_line</select>
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
if ( isset($_REQUEST['edit']) ) {
    send_user($id, $user, $project, $wiki, $email);
    //---
} else {
    echo_form($user, $wiki, $project, $email, $id);
}
//---
echo <<<HTML
    </div>
</div>
HTML;
//---
