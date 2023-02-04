<?PHP
//---
function make_col_sm_4($title, $table, $numb = '4') {
    return "
    <div class='col-md-$numb'>
      <div class='card'>
          <div class='card-header aligncenter' style='font-weight:bold;'>
              $title
          </div>
          <div class='card-body1 card2'>
            $table
          </div>
          <!-- <div class='card-footer'></div> -->
      </div>
      <br>
    </div>
    ";
};
//---
function make_col_sm_body($title, $subtitle, $table, $numb = '4') {
    return "
    <div class='col-md-$numb'>
        <div class='card'>
            <div class='card-header aligncenter1'>
                <span style='font-weight:bold;'>$title</span> $subtitle
            </div>
            <div class='card-body card2'>
                $table
            </div>
        </div>
        <br>
    </div>
    ";
};
//---
function test_print($s) {
    global $test;
    if ($test != '') { print $s; };
};
//---
function make_drop($uxutable, $code) {
    $ux =  "";
    //---
    foreach ( $uxutable AS $name => $cod ) {
        $cdcdc = $code == $cod ? "selected" : "";
        $ux .= "
		<option value='$cod' $cdcdc>$name</option>
		";
    };
    //---
	return $ux;
};
//---
function make_datalist_options($hyh) {
    //---
    $str = '';
    //---
    foreach ( $hyh AS $lange => $cod ) {
        $str .= "
            <option value='$cod'>$lange</option>";
    };
    //---
    return $str;
    //---
};
//---
function Get_it( $array, $key ) {
    $uu = isset($array[$key]) ? $array[$key] : $array->{$key};
    return $uu;
};
//---
function get_views($target, $lang, $pupdate) {
    $view = 0;
    //---
	if ($target == '') return 0;
    //---
	$start2 = $pupdate != '' ? str_replace('-', '', $pupdate) : '20190101';
    //---
    $url = 'https://wikimedia.org/api/rest_v1/metrics/pageviews/per-article/' . $lang . '.wikipedia/all-access/all-agents/' . rawurlencode($target) . '/daily/' . $start2 . '/2030010100';
    //---
    $output = file_get_contents( $url );
    //---
    $result = json_decode( $output, true );
    //---
    foreach ($result['items'] AS $da){
        $view += $da['views'];
    };
    //---
    // print($url.'<br>' );
    //---
    return $view;
};
//---
function make_view_by_number($target, $numb, $lang, $pupdate) {
    //---
    $numb2 = ($numb != '') ? $numb : "?";
    //---
	$start = $pupdate != '' ? $pupdate : '2019-01-01';
	$end = date("Y-m-d", strtotime("yesterday"));
    //---
    $url  = 'https://' . 'pageviews.wmcloud.org/?';
	$url .= http_build_query( array(
		'project' => "$lang.wikipedia.org",
		'platform' => 'all-access',
		'agent' => 'all-agents',
		'start' => $start,
		'end' => $end,
		'redirects' => '0',
		'pages' => $target,
	));
    //---
	$start2 = $pupdate != '' ? str_replace('-', '', $pupdate) : '20190101';
    $url2 = 'https://wikimedia.org/api/rest_v1/metrics/pageviews/per-article/' . $lang . '.wikipedia/all-access/all-agents/' . rawurlencode($target) . '/daily/' . $start2 . '/2030010100';
    //---
    $link = "<a target='_blank' href='$url'>$numb2</a>";
    if ($numb2 == '?' || $numb2 == 0 || $numb2 == '0') {
        $link = "<a target='_blank' name='toget' hrefjson='$url2' href='$url'>$numb2</a>";
    };
    //---
    return $link;
    };
//---
function make_mdwiki_title($tit) {
    $title = $tit;
    if ($title != '') {
        $title2 = rawurlencode( str_replace ( ' ' , '_' , $title ) );
        $title = '<a href="https://mdwiki.org/wiki/' . $title2 . '">' . $title . '</a>';
    };
    return $title;
};
//--- 
function make_cat_url ($ca) {
    $cat = $ca;
    if ($cat != '') {
        $cat2 = rawurlencode( str_replace ( ' ' , '_' , $cat ) );
        $cat = '<a href="https://mdwiki.org/wiki/Category:' . $cat2 . '">' . $cat . '</a>';
    };
    return $cat;
};
//--- 
function make_mdwiki_user_url($ud) {
    $user = $ud;
    if ($user != '') {
        $user2 = rawurlencode( str_replace ( ' ' , '_' , $user ) );
        $user = '<a href="https://mdwiki.org/wiki/User:' . $user2 . '">' . $user . '</a>';
    };
    return $user;
};
//--- 
function make_target_url($ta, $lang, $name='') {
    $target = $ta ;
	//---
	$nan = $target;
	if ($name != '') $nan = $name;
	//---
    if ($target != '') {
        $target2 = rawurlencode( str_replace ( ' ' , '_' , $target ) );
        $target = '<a href="https://' . $lang . '.wikipedia.org/wiki/' . $target2 . '">' . $nan . '</a>';
    };
    return $target;
};
//--- 

//--- 
?>