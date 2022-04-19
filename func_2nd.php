<?PHP
//==========================
function make_col_sm_4($title, $table, $numb = '4') {
    return "
    <div class='col-sm-$numb'>
      <div class='panel panel-default'>
          <div class='panel-heading aligncenter' style='font-size:110%;font-weight:bold;'>
              $title
          </div>
          <div class='panel-body' style='padding:5px 0px 5px 5px; max-height:330px; overflow: auto;'>
            $table
          </div>
      </div>
    </div>
    ";
};
//========================== 
function test_print($s) {
    global $test;
    if ($test != '') { print $s; };
};
//==========================
function make_datalist($lang_to_code,$code_lang_name,$code) {
    // global $lang_to_code,$code_lang_name,$code;
    //--------------------
    $coco = $code_lang_name;
    if ( $coco == '') { $coco = $code ; };
    //--------------------
    $str = '';
    //--------------------
    $str .= "
    <input size=25 list='Languages' class='span2' type='text' placeholder='two letter code' name='code' id='code' value='$coco'>";
    //--------------------
    $str .= '
        <datalist id="Languages">';
    //--------------------
    foreach ( $lang_to_code AS $lange => $cod ) {
        $str .= "
            <option value='$cod'>$lange</option>";
    };
    //--------------------
    $str .= '
        </datalist>
    </input>
    ' ;
    //--------------------
    return $str;
    //--------------------
};
//==========================
function make_drop($lang_to_code,$code) {
    // global $lang_to_code,$code;
    print '<select dir="ltr" id="code" class="form-control custom-select">';
    //--------------------
    foreach ( $lang_to_code AS $lange => $cod ) {
        $cdcdc = $code == $cod ? "selected" : "";
        print "<option id='$cod' $cdcdc>$lange</option>";
    };
    //--------------------
    print '
        </select>
    ' ;
};
//==========================
function Get_it( $array, $key ) {
    $uu = $array[$key] != '' ? $array[$key] : $array->{$key};
    return $uu;
};
//==========================
function make_view_by_number($target , $numb, $lang) {
    //---------------
    $numb2 = ($numb != '') ? $numb : "?";
    //---------------
    $urln = 'https://' . 'pageviews.toolforge.org/?project='. $lang .'.wikipedia.org&platform=all-access&agent=all-agents&redirects=0&range=this-year&pages=' . rawurlEncode($target);
    //---------------
    $link = '<a target="_blank" href="' . $urln . '">' . $numb2 . '</a>';
    //---------------
    return $link ;
    };
//==========================
function make_mdwiki_title($tit) {
    $title = $tit;
    if ($title != '') {
        $title2 = rawurlencode( str_replace ( ' ' , '_' , $title ) );
        $title = '<a href="https://mdwiki.org/wiki/' . $title2 . '">' . $title . '</a>';
    };
    return $title;
};
//========================== 
function make_cat_url ($ca) {
    $cat = $ca;
    if ($cat != '') {
        $cat2 = rawurlencode( str_replace ( ' ' , '_' , $cat ) );
        $cat = '<a href="https://mdwiki.org/wiki/Category:' . $cat2 . '">Category:' . $cat . '</a>';
    };
    return $cat;
};
//========================== 
function make_mdwiki_user_url($ud) {
    $user = $ud;
    if ($user != '') {
        $user2 = rawurlencode( str_replace ( ' ' , '_' , $user ) );
        $user = '<a href="https://mdwiki.org/wiki/User:' . $user2 . '">' . $user . '</a>';
    };
    return $user;
};
//========================== 
function make_target_url ($ta , $lang) {
    $target = $ta ;
    if ($target != '') {
        $target2 = rawurlencode( str_replace ( ' ' , '_' , $target ) );
        $target = '<a href="https://' . $lang . '.wikipedia.org/wiki/' . $target2 . '">' . $target . '</a>';
    };
    return $target;
};
//========================== 

//========================== 
?>