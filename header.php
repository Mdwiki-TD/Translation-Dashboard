<?php
//--------------------
// include_once('functions.php');
require('login5.php');
require('head0.php');
//--------------------
$SCRIPT_NAME = htmlspecialchars( $_SERVER['SCRIPT_NAME'] ) ; 
//--------------------
// $input_login_str = '<a class="btn w3-button w3-round-large w3-red inputsubmit" href="login5.php?action=login">Login</a>';
$input_login_str_1 = "<a class='btn btn-default navbar-right' id='login-btn' href='login5.php?action=login'><span class='glyphicon glyphicon-log-in'></span> Login </a>";
//--------------------
$input_login_str = '<input type="submit" class="btn btn-default glyphtest glyphtest-log-in" name="action" value="login"/>';
//--------------------
$input_login_str = '<button type="submit" class="btn btn-default" name="action" value="login"><span class="glyphicon glyphicon-log-in"></span> Login </button>';
//--------------------
$logout_line = '
    <div class="form-group navbar-form navbar-right">
        <label>You are logged in as: ' . $username . '</label>
        <a class="btn btn-default logged-in form-control" id="logout-btn"  href="index.php?action=logout">
            <span class="glyphicon glyphicon-log-out"></span> Logout
        </a>
    </div>
';
//--------------------<a class='w3-button w3-round-large w3-blue' href='translate.php?title=$title2&code=$code'>Translate</a>
$login_line = "
    <form method='GET' action='login5.php' class='form-inline'>
    <div class='form-group navbar-form navbar-right'>
        " . $input_login_str . "<!-- <a class='btn btn-default navbar-right' id='login-btn' href='login5.php?action=login'><span class='glyphicon glyphicon-log-in'></span> Login </a> -->
        
    </div>";
//--------------------
/*
$login_line222 = "<form class='navbar-form navbar-right' method='submit' action='login5.php?action=login'>
    <button class='btn btn-default' id='login-btn'>
        <span class='glyphicon glyphicon-log-in'></span> Login </button>
</form>";
*/
//--------------------
$form_start_done = false;
//--------------------
//print "<li><a href='$SCRIPT_NAME?action=identify'>identify</a></li>";
if ( $username != '' ) {
    print $logout_line;
} else {
    $form_start_done = true;
    print $login_line;
};

?>
            </div><!-- /.navbar-collapse -->
        </div><!-- /.container-fluid -->
    </nav>
</header>

<main id="body">