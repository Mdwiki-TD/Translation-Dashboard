<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://getbootstrap.com/docs/4.4/examples/floating-labels/floating-labels.css">

    <title>PHPMailer</title>
  </head>
  <body>
<?php
if ($_GET['test'] != '') {
    // echo(__file__);
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
    $ts_pw = posix_getpwuid(posix_getuid());
	echo var_dump($ts_pw);
};
//---
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

// Composer autoload
require 'vendor/autoload.php';
//---
include_once('td_config.php');
$my_ini = Read_ini_file('my_config.ini');
//---
$myboss_emails = array(
	"Mr. Ibrahem" =>	$my_ini['Ibrahem_email'],
	"Doc James" =>		$my_ini['James_email']
);
//---
$oMail = new PHPMailer();
$oMail->isSMTP();
$oMail->SMTPDebug = false;
$oMail->SMTPAuth = true;
$oMail->SMTPSecure = 'tls';
$oMail->Host = 'smtp.office365.com';
$oMail->Port = 587;
$oMail->Username = $my_ini['Username'];
$oMail->Password = $my_ini['Password'];
// Encryption method: STARTTLS
//Recipients


$msg_title = 'Wiki Project Med Translation Dashboard';
//---
// Content
$oMail->isHTML(true);                                  // Set e-mail format to HTML
$oMail->Subject = $msg_title;
//---
$ccme = isset($_REQUEST['ccme']) ? 1 : 0;
//---
$msg    = isset($_REQUEST['msg'])   ? $_REQUEST['msg']      : '';
$email  = isset($_REQUEST['email']) ? $_REQUEST['email']    : '';
$lang   = isset($_REQUEST['lang'])  ? $_REQUEST['lang']     : '';
$username   = isset($_REQUEST['username'])  ? $_REQUEST['username']     : '';
//---
//---
$myboss = isset($myboss_emails[$username]) ? $myboss_emails[$username] : $myboss_emails["Mr. Ibrahem"];
//---
$msg = "
<!DOCTYPE html>
<html lang='en' dir='ltr' style='
        font-family: sans-serif;
        line-height: 1.15;
        -webkit-text-size-adjust: 100%;
        -webkit-tap-highlight-color: transparent;'>

  <head>
    <title>Translation Dashboard</title>
  </head>

  <body dir='ltr' style='
        margin: 0;
        font-size: 1rem;
        font-weight: 400;
        line-height: 1.5;
        color: #212529;
        text-align: left;
        background-color: #fff;
        padding-bottom: 10px;
        padding-top: 10px;
        padding-right: 30px;
        padding-left: 30px'>
$msg
</body>
</html>";
//---
$oMail->setFrom($myboss);
$oMail->addAddress($email);
$oMail->addReplyTo($myboss);


$oMail->Body    = $msg;
// $oMail->AltBody = 'You prefer plain text, no problem.';

if ($ccme == 1) {
	$oMail->addCC($myboss);
};

if($oMail->send()){
	echo "<p style='color: green;'>Your message send to $email successfully...</p>";
} else {
	echo "<p style='color: red;'>Oops, something went wrong. Please try again later..</p>";
    echo 'Mailer Error: ' . $oMail->ErrorInfo;
};
?>
  </body>
</html>