</div>
<div id='yeye' class="container-fluid">
<?PHP
//---
$hoste = 'https://tools-static.wmflabs.org/cdnjs';
if ( $_SERVER['SERVER_NAME'] == 'localhost' )  $hoste = 'https://cdnjs.cloudflare.com';
//---
// echo "<link href='$hoste/ajax/libs/froala-editor/4.0.16/css/froala_editor.pkgd.min.css' rel='stylesheet' type='text/css' /><script type='text/javascript' src='$hoste/ajax/libs/froala-editor/4.0.16/js/froala_editor.pkgd.min.js'></script>";
//---
echo "<script src='$hoste/ajax/libs/summernote/0.8.20/summernote-lite.min.js'></script>
<link rel='stylesheet' href='$hoste/ajax/libs/summernote/0.8.20/summernote-lite.min.css' type='text/css' media='screen' charset='utf-8'>";
//--- aa
// require('header.php');
require('tables.php');
include_once('functions.php');
include_once('getcats.php');
//---
$tabs = array();
//---
$title  = $_REQUEST['title'];
$test   = $_REQUEST['test'];
$date   = $_REQUEST['date'];
$user   = $_REQUEST['user'];
$lang   = $_REQUEST['lang'];
$target = $_REQUEST['target'];
$views  = get_views($target, $lang);
//---
$items = get_cat_members( 'RTT', '1', $lang, false, $use_cash=true );
$items_missing = isset($items['missing']) ? $items['missing'] : array();
//---
$dd = array();
//---
foreach ( $items_missing AS $t ) {
    $t = str_replace ( '_' , ' ' , $t );
    $kry = isset($enwiki_pageviews_table[$t]) ? $enwiki_pageviews_table[$t] : 0; 
    $dd[$t] = $kry;
};
//---
arsort($dd);
//---
// $sugust = array_rand($items_missing);
$sugust = '';
foreach ( $dd AS $v => $gt) {
	if ($v != $title) {
		$sugust = $v;
		break;
	};
};
//---    //---
$here_params = array( 
    'username' => rawurlencode($user), 
    'code' => $lang, 
    'cat' => 'RTT', 
    'type' => 'lead', 
    'title' => rawurlencode($sugust)
    );
//---
$here_url = "https://mdwiki.toolforge.org/Translation_Dashboard/translate.php?" . http_build_query( $here_params );
//---
$HERE = "<a target='_blank' href='$here_url'><b>HERE</b></a>";
//---
$email = '';
//---
foreach ( quary2("select username, email from users where username = '$user';") AS $Key => $ta ) {
	if ($ta['username'] == $user) {
		$email = $ta['email'];
		break;
	};
};
//---
$title2  = make_mdwiki_title($title);
$sugust2 = make_mdwiki_title($sugust);
//---
$url_views_2 = 'https://' . 'pageviews.wmcloud.org/?project='. $lang .'.wikipedia.org&platform=all-access&agent=all-agents&redirects=0&range=all-time&pages=' . rawurlEncode($target);
//---
// $views2 = "<font color='#0000ff'>$views people</font>";
$views2 = "<a target='_blank' href='$url_views_2'><font color='#0000ff'>$views people</font></a>";
//---
$lang2 = isset($lang_code_to_en[$lang]) ? $lang_code_to_en[$lang] : $lang;
$lang2 = make_target_url($target, $lang, $name=$lang2);
//---
// print tabs values
$msg = "
<font color='#0000ff'>Thank you</font> for your prior translation of $title2 into $lang2.<br>
Since this translation has gone live on <font color='#311873'>$date</font> it has been read by $views2.<br>
Would you be interested in translating $sugust2? If so, simply click $HERE.<br>
Once again thank you for improving access to knowledge.<br>
";
//---
$mag = "

    <div dir='ltr' style='
        background-color: #f8f9fa !important;
        position: relative;
        display: -ms-flexbox;
        display: flex;
        -ms-flex-wrap: wrap;
        flex-wrap: wrap;
        -ms-flex-align: center;
        align-items: center;
        -ms-flex-pack: justify;
        justify-content: space-between;
        padding: 0.5rem 1rem;'>
      <table>
	  <tbody>
        <tr>
          <td>
            <img style='width: 40px;
			hight: auto;
			display: inline-block;
			margin-right: 1rem;
			vertical-align: middle;
			border-style: none;' src='https://upload.wikimedia.org/wikipedia/commons/thumb/5/58/Wiki_Project_Med_Foundation_logo.svg/400px-Wiki_Project_Med_Foundation_logo.svg.png' alt='Wiki Project Med Foundation logo'>
          </td>
          <td>
            <a style='display: inline-block;
			padding-top: 0.3125rem;
			padding-bottom: 0.3125rem;
			margin-right: 1rem;
			font-size: 1.2rem;
			line-height: inherit;
			color: #007bff;
			text-decoration: none;
			background-color: transparent;
			/* white-space: nowrap;*/
			' href='https://mdwiki.toolforge.org/Translation_Dashboard'>Wiki Project Med Translation Dashboard</a>
          </td>
        </tr>
      </tbody>
      </table>
    </div>
    <br>
    <div style=' padding-right: 5px; padding-left: 5px; max-width: 95%;'>
		<div style='
			position: relative;
			display: -ms-flexbox;
			display: flex;
			-ms-flex-direction: column;
			flex-direction: column;
			min-width: 0;
			word-wrap: break-word;
			background-color: #fff;
			background-clip: border-box;
			border: 1px solid rgba(0, 0, 0, .125);
			border-radius: 0.25rem;'>
			<table>
			  <tbody>
				<tr>
				  <td>
					<div style='padding: 0.75rem 1.25rem 0.1rem;margin-bottom: 0;background-color: rgba(0,0,0,.03);border-bottom: 1px solid rgba(0,0,0,.125);'>
					  <h3>Dear $user:</h3>
					</div>
				  </td>
				</tr>
				<tr>
				  <td>
					<div style='-ms-flex: 1 1 auto;flex: 1 1 auto;min-height: 1px;padding: 1.25rem;'>
					  $msg
					</div>
				  </td>
				</tr>
				<tr>
				  <td>
					<div style='padding: 0.75rem 1.25rem;background-color: rgba(0,0,0,.03);border-top: 1px solid rgba(0,0,0,.125);'></div>
				  </td>
				</tr>
			  </tbody>
			</table>
		</div>
    </div>
";
//---
//---
// foreach ($tabs AS $k => $v) print "*$k: $v<br>";
//---
$send_page = 'mailer/index.php';
$send_page = 'send.php';
//---
print "
<form class='' action='$send_page' method='POST'>
<input type='hidden' name='test' value='$test'/>
<input type='hidden' name='lang' value='$lang'/>
<input type='hidden' name='nonav' value='1'/>
<br>

<div class=''>
    <label class='mr-sm-2' for='email'>Email for user $user:</label> <input type='text' class='' id='email' name='email' value='$email' required>
</div>

<div class='form-group form-check'>
    <label class='form-check-label'>
        <input class='form-check-input' type='checkbox' name='ccme'> Send me copy</input>
    </label>
</div>
<textarea id='msg' name='msg'>
$mag
</textarea>
<button type='submit' name='send' value='send' class='btn btn-primary'>Send</button>
</form>
";
//---
?>

<script> 
	$('#mainnav').hide();
	$('#maindiv').hide();
	$('#msg').summernote({
		placeholder: 'Hello Bootstrap 4',
		tabsize: 6,
		// width: 370,
		height: 350
	});
</script>
