<?php 


ini_set( 'display_errors', 0 );
ini_set('error_reporting', E_ALL);
ini_set('log_errors','on'); 
ini_set('error_log', dirname(__FILE__) .'/php.log');
ini_set("date.timezone", "Asia/Tokyo");
session_save_path("/var/tmp/");
ini_set('session.gc_maxlifetime', 60*60*24*30);
ini_set('session.cookie_lifetime', 60*60*24*30);
session_start();
session_regenerate_id();



////////////////////////////////
//デバッグログ
////////////////////////////////
$debug_flg = true;

function debug($str){
    global $debug_flg;
    if($debug_flg){
        error_log('Debug : ' . $str);
    }
}

////////////////////////////////
//グローバル変数
////////////////////////////////
$err_msg = array();

////////////////////////////////
//ユーティリティ
////////////////////////////////
function objectSearch($search, $array, $key){
    return $array[array_search($search, array_column($array, $key))];
}

function getUserAgent(){
    $ua = getenv('HTTP_USER_AGENT');
    
    if (strstr($ua, 'Edge') || strstr($ua, 'Edg')) {
        $name =  "Edge";
    } elseif (strstr($ua, 'Trident') || strstr($ua, 'MSIE')) {
        $name =  "IE";
    } elseif (strstr($ua, 'OPR') || strstr($ua, 'Opera')) {
        $name =  "Opera";
    } elseif (strstr($ua, 'Chrome')) {
        $name =  "Chrome";
    } elseif (strstr($ua, 'Firefox')) {
        $name =  "Firefox";
    } elseif (strstr($ua, 'Safari')) {
        $name =  "Safari";
    } else {
        $name =  "Unknown";
    }

    return $name;
}

 

?>