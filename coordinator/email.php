</div>
<div id='yeye' class="container-fluid">
<?PHP
//---
$hoste = 'https://tools-static.wmflabs.org/cdnjs';
if ( $_SERVER['SERVER_NAME'] == 'localhost' )  $hoste = 'https://cdnjs.cloudflare.com';
#---
// echo "<link href='$hoste/ajax/libs/froala-editor/4.0.16/css/froala_editor.pkgd.min.css' rel='stylesheet' type='text/css' /><script type='text/javascript' src='$hoste/ajax/libs/froala-editor/4.0.16/js/froala_editor.pkgd.min.js'></script>";
//---
echo "<script src='$hoste/ajax/libs/summernote/0.8.20/summernote-bs4.min.js'></script>
<link rel='stylesheet' href='$hoste/ajax/libs/summernote/0.8.20/summernote-bs4.min.css' type='text/css' media='screen' charset='utf-8'>";
//---
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
$items = get_cat_members( 'RTT', '1', $lang, false );
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
    $sugust = $v;
    break;
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
$HERE = "<a target='_blank' href='$here_url'>HERE</a>";
//---
$title2  = make_mdwiki_title($title);
$sugust2 = make_mdwiki_title($sugust);
//---
// print tabs values
$msg = '
Thank you for your prior translation of <b>"' . $title2 . '"</b> into language "' . $lang . '".<br>
Since this translation has gone live on "' . $date . '" it has been read by "' . $views . '" people.<br>

Would you be interested in translating "' . $sugust2 . '"? If so, simply click ' . $HERE . '.<br>

Once again thank you for improving access to knowledge.<br>
';
//---
$mag = "
<!DOCTYPE html>
<html lang='en'>
<head>
  <title>Translation Dashboard</title>
</head>
<header>
<nav class='navbar navbar-expand-md bg-light navbar-light'>
    <a class='navbar-brand' href='https://mdwiki.toolforge.org/Translation_Dashboard' style='color:blue;'><img style='width: 25px;' src='https://upload.wikimedia.org/wikipedia/commons/thumb/5/58/Wiki_Project_Med_Foundation_logo.svg/400px-Wiki_Project_Med_Foundation_logo.svg.png' alt='Wiki Project Med Foundation logo'> Wiki Project Med Translation Dashboard</a>
  </nav>
</header>
<style>

html {
    font-family: sans-serif;
    line-height: 1.15;
    -webkit-text-size-adjust: 100%;
    -webkit-tap-highlight-color: transparent;
}
body {
    margin: 0;
    font-family: -apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,'Helvetica Neue',Arial,'Noto Sans','Liberation Sans',sans-serif,'Apple Color Emoji','Segoe UI Emoji','Segoe UI Symbol','Noto Color Emoji';
    font-size: 1rem;
    font-weight: 400;
    line-height: 1.5;
    color: #212529;
    text-align: left;
    background-color: #fff;
    padding-bottom: 10px;
    padding-top: 10px;
    padding-right: 30px;
    padding-left: 30px
  }
  
a {
    color: #007bff;
    text-decoration: none;
    background-color: transparent;
}
.card-body {
    -ms-flex: 1 1 auto;
    flex: 1 1 auto;
    min-height: 1px;
    padding: 1.25rem;
}
.card-footer:last-child {
    border-radius: 0 0 calc(0.25rem - 1px) calc(0.25rem - 1px);
}

.card-footer {
    padding: 0.75rem 1.25rem;
    background-color: rgba(0,0,0,.03);
    border-top: 1px solid rgba(0,0,0,.125);
}
img {
    vertical-align: middle;
    border-style: none;
}
.container {
    padding-right: 5px;
    padding-left: 5px;
    max-width: 95%;
}
.bg-light {
    background-color: #f8f9fa!important;
}
.navbar {
    position: relative;
    display: -ms-flexbox;
    display: flex;
    -ms-flex-wrap: wrap;
    flex-wrap: wrap;
    -ms-flex-align: center;
    align-items: center;
    -ms-flex-pack: justify;
    justify-content: space-between;
    padding: 0.5rem 1rem;
}
article, aside, figcaption, figure, footer, header, hgroup, main, nav, section {
    display: block;
}

.card-header {
    padding: 0.75rem 1.25rem;
    margin-bottom: 0;
    background-color: rgba(0,0,0,.03);
    border-bottom: 1px solid rgba(0,0,0,.125);
}

.card {
    position: relative;
    display: -ms-flexbox;
    display: flex;
    -ms-flex-direction: column;
    flex-direction: column;
    min-width: 0;
    word-wrap: break-word;
    background-color: #fff;
    background-clip: border-box;
    border: 1px solid rgba(0,0,0,.125);
    border-radius: 0.25rem;
}
.navbar-brand {
    display: inline-block;
    padding-top: 0.3125rem;
    padding-bottom: 0.3125rem;
    margin-right: 1rem;
    font-size: 1.25rem;
    line-height: inherit;
    white-space: nowrap;
}

</style>
<body>
<br>
<div class='container'>
  <div class='card'>
    <div class='card-header'><h3>Dear $user:</h3></div>
    <div class='card-body'>$msg</div> 
    <div class='card-footer'></div>
  </div>
</div>

</body>
</html>
";
//---
//---
// foreach ($tabs AS $k => $v) print "*$k: $v<br>";
//---
print "
<form class='' action='send.php' method='POST'>
<input type='hidden' name='test' value='$test'/>
<input type='hidden' name='lang' value='$lang'/>
<input type='hidden' name='nonav' value='1'/>
<br>

<div class=''>
    <label class='mr-sm-2' for='email'>Email for user $user:</label> <input type='text' class='' id='email' name='email' value='' required>
</div>

<div class='form-group form-check'>
    <label class='form-check-label'>
        <input class='form-check-input' type='checkbox' name='ccme'> Send me copy</input>
    </label>
</div>
<textarea id='msge' name='msge'>
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
	$('#msge').summernote({
		placeholder: 'Hello Bootstrap 4',
		tabsize: 6,
		height: 350
	});
</script>
