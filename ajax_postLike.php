<?php 

require_once('access.php');
require_once('Manetter.php');

debug('[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[');
debug('- AJAX POST LIKE -');
debug('[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[');




if(isset($_SESSION['login_date']) && isset($_POST['like_id'])){
    if($_SESSION['login_date'] + $_SESSION['login_limit'] > time()){
        $_SESSION['login_date'] = time();
        $twitter_id = $_SESSION['twitter_id'];
        debug('TWITTER ID : ' .print_r($twitter_id, true));
        $Manetter = new Manetter($key, $secret, $bearer, $dsn, $user, $password, $client_id, $client_secret, $redirect_uri);
        $token = $Manetter->TwitterDB->getRegisteredToken($twitter_id);
        $Manetter->setTokenToHeader($token['access_token']);
        
        $tweet_id = $_POST['like_id'];

        $respons = $Manetter->postLike($twitter_id, $tweet_id);
        $result = $respons;
        debug('RESPONSE : ' .print_r($result, true));
    }else{
        $result = array(
            //セッション切れ
            'status' => 1001
        );
        debug('RESPONSE : ' .print_r($result, true));
    }
    
}else{
    $result = array(
        //セッション無し
        'status' => 1002
    );
}

debug('JSON ENCODE : ' .print_r(gettype(json_encode($result)), true));
echo json_encode($result);