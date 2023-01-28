<?php
//---
$msg        = isset($_REQUEST['msg'])   ? $_REQUEST['msg']      : '';
$email_to   = isset($_REQUEST['email_to']) ? $_REQUEST['email_to']    : '';
$email_from = isset($_REQUEST['email_from']) ? $_REQUEST['email_from']    : '';
$username   = isset($_REQUEST['username'])  ? $_REQUEST['username']: '';
$msg_title  = isset($_REQUEST['msg_title'])  ? $_REQUEST['msg_title']: 'Wiki Project Med Translation Dashboard';

$ccme       = isset($_REQUEST['ccme']) ? 1 : 0;
$cc_to      = isset($_REQUEST['cc_to'])   ? $_REQUEST['cc_to']      : '';
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
$tool_email = $my_ini['mdwiki_Username'];
$tool_pass = $my_ini['mdwiki_Password'];
//---
if ($msg != '' && $email_to != '' ) {
    echo "
    <script> 
        $('#mainnav').hide();
    </script>
    ";
    //---
    $mail = new PHPMailer();
    $mail->isSMTP();
    $mail->SMTPDebug = false;
    $mail->SMTPAuth = true;
    //---
    $mail->CharSet  ="utf-8";
    //---
    $mail->Username = $tool_email;
    $mail->Password = $tool_pass;
    //---
    $mail->SMTPSecure = 'tls';
    $mail->Host = 'smtp.office365.com';
    $mail->Port = 587;
    //---
    // Encryption method: STARTTLS
    //Recipients
    //---
    $mail->SMTPKeepAlive = true;
    $mail->Mailer = "smtp";
    //---
    // Content
    $mail->isHTML(true);                                  // Set e-mail format to HTML
    $mail->Subject = $msg_title;
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
    $mail->setFrom($tool_email, "WikiProjectMed");
    $mail->addAddress($email_to);
    $mail->addReplyTo($tool_email);
    //---
    $mail->Body    = $msg1;
    // $mail->AltBody = 'You prefer plain text, no problem.';

    if ($ccme == 1 && $cc_to != '') {
        $mail->addCC($cc_to);
    };

    if($mail->send()){
        echo "<p style='color: green;'>Your message send to $email_to successfully...</p>";
    } else {
        echo "<p style='color: red;'>Oops, something went wrong. Please try again later..</p>";
        echo 'Mailer Error: ' . $mail->ErrorInfo;
    };
};
?>