<?php
//---
$Debug = isset($_REQUEST['Debug']) ? true : false;
$msg        = $_REQUEST['msg'] ?? '';
$email_to   = $_REQUEST['email_to'] ?? '';
$email_from = $_REQUEST['email_from'] ?? '';
$msg_title  = $_REQUEST['msg_title'] ?? 'Wiki Project Med Translation Dashboard';

$ccme       = isset($_REQUEST['ccme']) ? 1 : 0;
$cc_to      = $_REQUEST['cc_to'] ?? '';
//---
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;
//---
// Composer autoload
require_once __DIR__ . '/vendor/autoload.php';
//---
include_once 'td_config.php';
$my_ini     = Read_ini_file('my_config.ini');
$tool_email = $my_ini['mdwiki_Username'];
$tool_pass  = $my_ini['mdwiki_Password'];
$tool_host  = $my_ini['mail_host'];
$tool_port  = $my_ini['mail_port'];
$tool_tls   = $my_ini['mail_tls'];
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
    $mail->SMTPDebug = $Debug;
    $mail->SMTPAuth = true;
    //---
    $mail->CharSet  ="utf-8";
    //---
    $mail->Username = $tool_email;
    $mail->Password = $tool_pass;
    //---
    $mail->SMTPSecure = $tool_tls;
    $mail->Host = $tool_host;
    $mail->Port = $tool_port;
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
    $msg1 = <<<HTML
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
        </html>
        HTML;
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