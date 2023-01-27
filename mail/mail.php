<?php
//---
$msg        = isset($_REQUEST['msg'])   ? $_REQUEST['msg']      : '';
$email      = isset($_REQUEST['email']) ? $_REQUEST['email']    : '';
$username   = isset($_REQUEST['username'])  ? $_REQUEST['username']: '';
//---
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;
//---
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
if ($msg != '' && $email != '' ) {
    echo "
    <script> 
        $('#mainnav').hide();
    </script>
    ";
    //---
    $mail = new PHPMailer();
    $mail->isSMTP();
    $mail->SMTPDebug = true;
    $mail->SMTPAuth = true;
    //---
    $mail->CharSet  ="utf-8";
    //---
    // $fofo = 'Username';
    $fofo = 'mdwiki_Username';
    //---
    if ($fofo == 'mdwiki_Username') {
        $mail->Host = 'smtp.gmail.com';
        //---
        // $mail->Username = $my_ini['mdwiki_Username'];
        // $mail->Password = $my_ini['mdwiki_Password'];
        //---
        $mail->Username = "mdwiki.org@gmail.com";
        $mail->Password = "wikimedibrahem";
        //---
        $mail->SMTPSecure = 'tls';
        $mail->Port = 587;
        //---
    } elseif ($fofo == 'Username') {
        $mail->Username = $my_ini['Username'];
        $mail->Password = $my_ini['Password'];
        //---
        $mail->SMTPSecure = 'tls';
        $mail->Host = 'smtp.office365.com';
        $mail->Port = 587;
        //---
    };
    // Encryption method: STARTTLS
    //Recipients
    $msg_title = 'Wiki Project Med Translation Dashboard';
    //---
    $mail->SMTPKeepAlive = true;
    $mail->Mailer = "smtp";
    //---
    // Content
    $mail->isHTML(true);                                  // Set e-mail format to HTML
    $mail->Subject = $msg_title;
    //---
    $ccme = isset($_REQUEST['ccme']) ? 1 : 0;
    //---
    $myboss = isset($myboss_emails[$username]) ? $myboss_emails[$username] : $myboss_emails["Mr. Ibrahem"];
    //---
    $msg1 = "
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
    $mail->setFrom($myboss);
    $mail->addAddress($email);
    $mail->addReplyTo($myboss);
    //---
    $mail->Body    = $msg1;
    // $mail->AltBody = 'You prefer plain text, no problem.';

    if ($ccme == 1) {
        $mail->addCC($myboss);
    };

    if($mail->send()){
        echo "<p style='color: green;'>Your message send to $email successfully...</p>";
    } else {
        echo "<p style='color: red;'>Oops, something went wrong. Please try again later..</p>";
        echo 'Mailer Error: ' . $mail->ErrorInfo;
    };
};
?>