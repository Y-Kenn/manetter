<?php

require_once('access.php');
require_once('Manetter.php');
require_once('func_common.php');
debug('[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[');
debug('- LOADING-');
debug('[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[');

$ses_limit = 60*60*24;//セッションリミット　デフォルト１時間


$Manetter = new Manetter($key, $secret, $bearer, $dsn, $user, $password, $client_id, $client_secret, $redirect_uri);


?>
<?php
$site_title = 'ローディング - Manetter';
require('head.php');
?>

    <body>
        <?php require_once('frontHeader.php') ?>
        
        <div class="l-container__1colf">
            <div class="p-loading">
                <div class="p-loading__inner">
                    <?php
                        if(getUserAgent() === 'Chrome'){
                            echo '<i class="fa fa-spinner fa-spin-pulse p-loading__icon"></i>';
                        }else{
                            echo '<i class="fa-solid fa-arrows-spin p-loading__icon"></i>';
                        }
                    ?>
                    
                    <div class="p-loading__text">ロード中です。そのままお待ちください。</div>
                </div>
            </div>
            
        </div>
        

        <!--ロード１回目-->
        <?php if(!$_POST){ ?>
            <script>
                var url_query = location.search
                //OAuthでエラーがあった場合はログイン画面に遷移
                var error = url_query.indexOf('error=');
                if(error >= 0){
                    console.log('AOuth error');
                    console.log(error);
                    alert(error);
                    window.location.href = 'login.php';
                //エラーがない場合はAccessToken取得に向けてPOST
                }else{
                    var verify_start_index = url_query.indexOf('state=') + 'state='.length;
                    var verify_end_index = url_query.indexOf('&code=');
                    var verify_str = url_query.substring(verify_start_index, verify_end_index);
                    var code_index = url_query.indexOf('code=') + 'code='.length;
                    var code_str = url_query.substring(code_index);

                    var form = document.createElement('form');
                    form.method = 'POST';
                    form.action = '';
                    
                    var request_verify = document.createElement('input');
                    request_verify.type = 'hidden'; 
                    request_verify.name = 'verify';
                    request_verify.value = verify_str;
                    form.appendChild(request_verify);

                    var request_code = document.createElement('input');
                    request_code.type = 'hidden'; 
                    request_code.name = 'code';
                    request_code.value = code_str;
                    form.appendChild(request_code);

                    document.body.appendChild(form);
    　
                    console.log(form.submit());
                }

                
    　
            </script>
        <!--ロード２回目-->
        <?php }else{
            $token = $Manetter->getAccessToken($_POST['verify'], $_POST['code']);
            $my_info = $Manetter->getMyInfo();
            $twitter_id = $my_info['data']['id'];
            $u_id = $Manetter->TwitterDB->searchAccountByTwitterId($twitter_id);

            //ユーザでない場合
            if(!$u_id){
                $w_id = $Manetter->TwitterDB->searchWithdrawedAccountByTwitterId($twitter_id);
                if($w_id){
                    //退会済ユーザの場合、復帰
                    debug('退会済ユーザ');
                    $Manetter->TwitterDB->deleteFlgToZeroAll($twitter_id);
                    $u_id = $Manetter->TwitterDB->searchAccountByTwitterId($twitter_id);
                }else{
                    //ユーザ未登録の場合、登録
                    debug('未登録ユーザ');
                    $Manetter->TwitterDB->registUser($twitter_id);
                    $Manetter->TwitterDB->makeHist($twitter_id);
                    $u_id = $Manetter->TwitterDB->searchAccountByTwitterId($twitter_id);

                    //--Histの初期化--
                    $Manetter->setTokenToHeader($token['access_token']);
                    //→新規フォロワー
                    $followers_hist_db = $Manetter->TwitterDB->getAllColumn('followers_hist', $twitter_id);
                    $followers = $Manetter->getFollowers($twitter_id);
                    $new_followers_num = $Manetter->getNewFollowersNum($followers, $twitter_id);
                    $current_followers_num = count($followers['data']);
                    $data = array( 'last_id' => $followers['data'][0]['id'], 'followers_num' => $current_followers_num,);
                    $Manetter->TwitterDB->updateFollowersHist($data, $twitter_id);
                    //←新規フォロー
                    $following_hist_db = $Manetter->TwitterDB->getAllColumn('following_hist', $twitter_id);
                    $following = $Manetter->getFollowing($twitter_id);
                    $new_following_num = $Manetter->getNewFollowingNum($following, $twitter_id);
                    if($new_following_num < 0) $new_following_num = 0;
                    $current_following_num = count($following['data']);
                    $data = array( 'last_id' => $following['data'][0]['id'], 'following_num' => $current_following_num);
                    $Manetter->TwitterDB->updateFollowingHist($data, $twitter_id);
                    //→いいね・→リツイート共通の処理
                    $tweets = $Manetter->getTweets($twitter_id);
                    $new_reaction = $Manetter->getNewReactionNum($tweets, $twitter_id);
                    if($new_reaction['like_count'] < 0) $new_reaction['like_count'] = 0;
                    if($new_reaction['retweet_count'] < 0) $new_reaction['retweet_count'] = 0;
                    if($new_reaction['reply_count'] < 0) $new_reaction['reply_count'] = 0;
                    debug('NEW REACTION : ' .print_r($new_reaction, true));
                    $tweets_30days = $Manetter->getTweetsWithin($tweets, 60*60*24*30);
                    $base_id = $tweets_30days['data'][count($tweets_30days['data'])-1]['id'];
                    $base_nums = $Manetter->getAllReactionNum($tweets_30days);
                    //→いいね
                    $get_like_hist_db = $Manetter->TwitterDB->getAllColumn('get_like_hist', $twitter_id);
                    $data = array( 'base_id' => $base_id, 'base_num' => $base_nums['like_count']);
                    $Manetter->TwitterDB->updateGetLikeHist($data, $twitter_id);
                    //→リツイート
                    $get_retweet_hist_db = $Manetter->TwitterDB->getAllColumn('get_retweet_hist', $twitter_id);
                    $data = array( 'base_id' => $base_id, 'base_num' => $base_nums['retweet_count']);
                    $Manetter->TwitterDB->updateGetRetweetHist($data, $twitter_id);
                    //→リプライ
                    $get_reply_hist_db = $Manetter->TwitterDB->getAllColumn('get_reply_hist', $twitter_id);
                    $data = array( 'base_id' => $base_id, 'base_num' => $base_nums['reply_count']);
                    $Manetter->TwitterDB->updateGetReplyHist($data, $twitter_id);
                    //←いいね
                    $tweets_liking = $Manetter->getLikingTweet($twitter_id);
                    $new_liking_num = $Manetter->getNewLikingNum($tweets_liking, $twitter_id);
                    if($new_liking_num < 0) $new_liking_num = 0;
                    $like_hist_db = $Manetter->TwitterDB->getAllColumn('like_hist', $twitter_id);
                    $data = array( 'last_id' => $tweets_liking['data'][0]['id']);
                    $Manetter->TwitterDB->updateLikeHist($data, $twitter_id);
                    //--ここまでHistの初期化--
                }
                
            }
            $_SESSION['user_id'] = $u_id['id'];
            $_SESSION['twitter_id'] = $twitter_id;
            $_SESSION['login_date'] = time();
            $_SESSION['login_limit'] = $ses_limit;
            $Manetter->TwitterDB->updateToken($token['access_token'], $token['refresh_token'], $twitter_id);
            header('Location:index.php');

        } ?>

    </body>

</html>
