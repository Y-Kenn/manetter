<?php

require_once('access.php');
require_once('func_common.php');
require_once('Manetter.php');
debug('[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[');
debug('- LIKE SUPPORT -');
debug('[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[');

require_once('auth.php');

$twitter_id = $_SESSION['twitter_id'];

$Manetter = new Manetter($key, $secret, $bearer, $dsn, $user, $password, $client_id, $client_secret, $redirect_uri);
$token = $Manetter->TwitterDB->getRegisteredToken($twitter_id);
$Manetter->setTokenToHeader($token['access_token']);
$my_info = $Manetter->getMyInfo();
//ツイッターで作成済のリストを取得
$lists = $Manetter->getLists($twitter_id);
//リストIDとツイートの取得数がGETされている場合
if(isset($_GET['list_id']) && isset($_GET['tweets_num'])){
    if($_GET['list_id'] === 'follow'){
        $list_members = $Manetter->getFollowing($twitter_id);
    }elseif($_GET['list_id'] === 'follower'){
        $list_members = $Manetter->getFollowers($twitter_id);
    }else{
        $list_members = $Manetter->getListMembers($_GET['list_id']);
    }

    if(isset($list_members['data'])){
        //次回から入力不要にするためcookie作成
        setcookie('list_id', $_GET['list_id'], time()+60*60*24*30);
        setcookie('tweets_num', $_GET['tweets_num'], time()+60*60*24*30);
        $list_id_prev = $_GET['list_id'];
        $tweets_num_prev = $_GET['tweets_num'];

        //ツイート取得に時間がかかるため、token_limitに余裕を持ってリフレッシュする
        if($_SESSION['token_limit'] < 6000) $Manetter->refreshToken();

        $list_members_num = count($list_members['data']);
        $tweets = $Manetter->getMembersTweets($list_members);
        if(isset($tweets['data'])){
            $tweets_match_user = $Manetter->mergeUserData($tweets);
            $tweets_removed_mine = $Manetter->removeTweetsByAuthorId($tweets_match_user, $twitter_id);
            $tweets_removed_mine_liking =$Manetter->removeLikingTweets($tweets_removed_mine, $twitter_id);
            $tweets_reshaped = $Manetter->reshapeByAuthorId($tweets_removed_mine_liking);
            $selected_tweets = $Manetter->selectTweetsByMembers($tweets_reshaped, $list_members, 'like_count', $_GET['tweets_num']);
        }
        
    }
    
}

//GET送信が無い場合、cookieからセレクトボックス・ナンバーボックスに入力
if(!isset($list_id_prev) && isset($_COOKIE['list_id'])){
    $list_id_prev = $_COOKIE['list_id'];
    $tweets_num_prev = $_COOKIE['tweets_num'];
}

?>
<?php
$site_title = 'いいねサポート - Manetter';
require('head.php');
?>

    <body>
        <div class="l-container--2col__fluid">
            <!--左ヘッダ-->
            <?php require('header.php'); ?>
            <!--メイン-->
            <div class="l-container--2col__main">
                <div class="c-page">
                    
                    <div>
                        <h1 class="c-page__title"><i class="fa-solid fa-heart-circle-check u-color--like u-icon--shadow"></i><span class="u-margin--l--5">いいねサポート</span></h1>
                    </div>
                    <div class="c-page__description">
                        <p>リストの各メンバーからツイートを取得します。いいねボタンを押すことでいいねを送ることができます。
                            <br>
                            ツイートを取得したいリストを選択し、取得する数を入力してください。
                        </p>
                    </div>
                    <!--フォーム-->
                    <form action="" method="get">
                        
                        <div class="c-page__row_parts">
                            <!--セレクトボックス-->
                            <span class="c-selectbox u-margin--l--10">
                                <select name="list_id" id="js-selectbox_list">
                                    <option value="" selected disabled>リストを選択してください</option>
                                    <?php foreach($lists['data'] as $one_list){ ?>
                                        <option value="<?php echo $one_list['id']; ?>" <?php if($one_list['id'] === $list_id_prev) echo 'selected'; ?>><?php echo $one_list['name']; ?></option>
                                    <?php }; ?>
                                    <option value="follow" <?php if($list_id_prev === 'follow') echo 'selected'; ?>>フォロー</option>
                                    <option value="follower" <?php if($list_id_prev === 'follower') echo 'selected'; ?>>フォロワー</option>
                                </select>
                            </span>
                        </div>
                        <div class="c-page__row_parts">
                            <!--ナンバーボックス-->
                            <input type="number" name="tweets_num" id="js-numberbox_tweets" min="1" max="1000" placeholder="10 - 1000" value ="<?php if(isset($tweets_num_prev)) echo $tweets_num_prev; ?>" class="c-numberbox u-margin--l--10">
                        </div>

                        <div class="c-page__row_parts">
                            <button type="submit" id="js-submit" disabled class="c-button c-button--large c-button--white">ツイート取得</button>                            
                        </div>
                    </form>                    
                    
                    <div class="c-page__fluid_parts">
                        <div class="c-tweet_card__inner">
                        <?php if(isset($selected_tweets)){ ?>
                            <?php foreach($selected_tweets['data'] as $one_tweet){ ?>
                                <div class="c-tweet_card__item">
                                    <a href="<?php echo 'https://twitter.com/' .$one_tweet['username'] .'/status' .'/' . $one_tweet['id'] ;?>" target="_new">
                                        <div class="c-tweet__header">
                                            <img src="<?php echo $one_tweet['profile_image_url'];  ?>" class="c-tweet__profile_img">
                                            <div>
                                                <div class="c-tweet__name"><?php echo $one_tweet['name']; ?></div>
                                                <div class="c-tweet__screen_name"><?php echo '@' .$one_tweet['username']; ?></div>
                                            </div>
                                        </div>
                                        <div class="c-tweet__body">
                                            <div class="c-tweet__text"><?php echo $one_tweet['text']; ?></div>
                                        </div>
                                        <div class="c-tweet__footer">
                                            <div class="c-tweet__reaction">
                                                <i class="fa-solid fa-heart"></i>
                                                <span><?php echo $one_tweet['public_metrics']['like_count']; ?></span>
                                                <i class="fa-solid fa-retweet"></i>
                                                <span><?php echo $one_tweet['public_metrics']['retweet_count']; ?></span>
                                                <i class="fa-solid fa-message"></i>
                                                <span><?php echo $one_tweet['public_metrics']['reply_count']; ?></span>
                                            </div>
                                            <span class="c-tweet__date"><?php echo $Manetter->toJapanTime($one_tweet['created_at']); ?></span>
                                        </div>
                                    </a>
                                    
                                    <div class="c-tweet_card__button">
                                        <button type="button" name="like_id" value="<?php echo $one_tweet['id'] ?>" class="js-button--like c-button c-button--small c-button--like">いいね</button>
                                    </div>

                                </div>
                            <?php } ?>
                        <?php }; ?>
                        </div>
                    </div>
                </div>
                
            </div>
        </div>
        <script
            src="https://code.jquery.com/jquery-3.2.1.min.js"
            integrity="sha256-hwg4gsxgFZhOsEEamdOYGBf13FyQuiTwlAQgxVSNgt4="
            crossorigin="anonymous">
        </script>
        <script>
            $(function(){
                $(".js-nav__likeSupport").addClass("js-nav--active");
            });
        </script>
        <script src="likeSupport.js"></script>
        <script src="app.js"></script>
    </body>
</html>