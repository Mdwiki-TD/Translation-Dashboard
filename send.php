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
$msg  = $_REQUEST['msg'];
$email = $_REQUEST['email'];
$lang = $_REQUEST['lang'];
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
// Always set content-type when sending HTML email
$headers = "MIME-Version: 1.0" . "\r\n";
$headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";

// More headers
$headers .= "From: <$myboss>" . "\r\n";

if ($ccme == 1) $headers .= "Cc: $myboss" . "\r\n";

if (mail($email, $msg_title, $mag, $headers)) {
	echo "<p style='color: green;'>Your message send to $email successfully...</p>";
} else {
	echo "<p style='color: red;'>Oops, something went wrong. Please try again later..</p>";
};
//---
?>