<?PHP
//---
require('header.php');
require('tables.php');
include_once('functions.php');
include_once('getcats.php');
//---
if ($_REQUEST['test'] != '') {
	// echo(__file__);
	ini_set('display_errors', 1);
	ini_set('display_startup_errors', 1);
	error_reporting(E_ALL);
};
//---
$ccme = isset($_REQUEST['ccme']) ? 1 : 0;
//---
$msg   = $_REQUEST['msg'];
$email = $_REQUEST['email'];
$lang  = $_REQUEST['lang'];
//---
$msg_title = 'Wiki Project Med Translation Dashboard';
//---
$myboss_emails = array(
	"Mr. Ibrahem" => "ibrahem.al-radaei@outlook.com",
	"Doc James" => "jmh649@gmail.com"
);
//---
$myboss =isset($myboss_emails[$username]) ? $myboss_emails[$username] : $myboss_emails["Mr. Ibrahem"];
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
// Always set content-type when sending HTML email
$headers = "MIME-Version: 1.0" . "\r\n";
$headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";

// More headers
$headers .= "From: <$myboss>" . "\r\n";

$headers2 = array(
    'From' => $myboss,
    'MIME-Version' => '1.0',
    'Content-type' => 'text/html;charset=UTF-8',
    // 'Reply-To' => 'webmaster@example.com',
    // 'X-Mailer' => 'PHP/' . phpversion()
);

if ($ccme == 1) {
	$headers .= "Cc: $myboss" . "\r\n";
	$headers2['Cc'] = $myboss;
};

if (mail($email, $msg_title, $msg, $headers2)) {
	echo "<p style='color: green;'>Your message send to $email successfully...</p>";
} else {
	echo "<p style='color: red;'>Oops, something went wrong. Please try again later..</p>";
};
//---
?>