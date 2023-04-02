<?php

require_once('access.php');
require_once('Manetter.php');
debug('[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[');
debug('- HOME -');
debug('[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[');

require_once('auth.php');

$twitter_id = $_SESSION['twitter_id'];
debug('SESSION:'.print_r($_SESSION, true));
$Manetter = new Manetter($key, $secret, $bearer, $dsn, $user, $password, $client_id, $client_secret, $redirect_uri);
$token = $Manetter->TwitterDB->getRegisteredToken($twitter_id);
debug('TOKEN:'.print_r($token, true));
$Manetter->setTokenToHeader($token['access_token']);
debug('HEADER :'.print_r($Manetter->header, true));
$my_info = $Manetter->getMyInfo();
//メンション取得
$mentions = $Manetter->getMentions($twitter_id);
if(isset($mentions['data'])) $mentions = $Manetter->mergeUserData($mentions);

if(1){
    //ツイート取得
    $tweets = $Manetter->getTweets($twitter_id);
    if(isset($tweets['data'])){
        $tweets_30days = $Manetter->getTweetsWithin($tweets, 60*60*24*30);
        $tweets_sorted_like = $Manetter->sortTweetsByMetrics($tweets_30days, 'like_count');
        $tweets_sorted_retweet = $Manetter->sortTweetsByMetrics($tweets_30days, 'retweet_count');
        $tweets_sorted_reply = $Manetter->sortTweetsByMetrics($tweets_30days, 'reply_count');
    }else{
        debug('Could not get Tweets data');
        debug('Twitter ID : ' .print_r($twitter_id, true));
        debug('Token : ' .print_r($token, true));
    }
    
    
    //フォロワー数処理
    $followers = $Manetter->getFollowers($twitter_id);
    if(isset($followers['data'])){
        $new_followers_num = $Manetter->getNewFollowersNum($followers, $twitter_id);
        if($new_followers_num < 0) $new_followers_num = 0;
        $current_followers_num = count($followers['data']);
        $previous_followers_num = $Manetter->getPreviousDaysFollowersNum($twitter_id);
        $unfollowed_num = $previous_followers_num + $new_followers_num - $current_followers_num;
        if($unfollowed_num < 0) $unfollowed_num = 0;
    }
    //フォロー数処理
    $following = $Manetter->getFollowing($twitter_id);
    if(isset($following['data'])){
        $new_following_num = $Manetter->getNewFollowingNum($following, $twitter_id);
        if($new_following_num < 0) $new_following_num = 0;
        $current_following_num = count($following['data']);
        $previous_following_num = $Manetter->getPreviousDaysFollowingNum($twitter_id);
        $unfollow_num = $previous_following_num + $new_following_num - $current_following_num;
        if($unfollow_num < 0) $unfollow_num = 0;
    }
    
    //いいね・リツイート数処理
    if(isset($tweets['data'])){
        $reactions = $Manetter->getNewReactionNum($tweets, $twitter_id);
        $new_get_like_num = $reactions['like_count'];
        if($new_get_like_num < 0) $new_get_like_num = 0;
        $new_get_retweet_num = $reactions['retweet_count'];
        if($new_get_retweet_num < 0) $new_get_retweet_num = 0;
        $liking = $Manetter->getLikingTweet($twitter_id);
        if(isset($liking['data'])){
            $new_liking_num = $Manetter->getNewLikingNum($liking, $twitter_id);
            if($new_liking_num < 0) $new_liking_num = 0;
        }
    }
}

$hist = $Manetter->TwitterDB->get_all_hist($twitter_id);

$json_php_hist = json_encode($hist);

?>
<?php
$site_title = 'ホーム - Manetter';
require('head.php');
?>


    <body>
        <div class="l-container--3col__fluid">
            <!--左ヘッダ-->
            <?php require('header.php'); ?>
            <!--メイン-->
            <div class="l-container--3col__main">
                <div class="p-dashboard">
                    <div class="p-dashboard__title">
                        <i class="fa-solid fa-square-poll-vertical u-color--mainBlue u-icon--shadow u-margin--l--10"></i>
                        <span class="u-margin--l--5">ダッシュボード</span>
                    </div>
                    <div class="c-tile u-bgColor--mainBlue--gradient u-color--white">
                        <div class="c-tile__header"><h3>Today's Paformance</h3></div>
                        <div class="c-tile__inner">
                            <div class="c-tile__item c-tile__item--order1">
                                <i class="fa-solid fa-arrow-right"></i><i class="fa-solid fa-user-plus c-tile__icon"></i>
                                <span class="c-tile__title">新規フォロワー</span>
                                <div class="c-tile__num"><?php if(isset($new_followers_num)) echo $new_followers_num; ?></div>
                            </div>
                            <div class="c-tile__item c-tile__item--order3">
                                <i class="fa-solid fa-arrow-right"></i><i class="fa-solid fa-user-minus c-tile__icon"></i>
                                <span class="c-tile__title">フォロワー解除</span>
                                <div class="c-tile__num"><?php if(isset($unfollowed_num)) echo $unfollowed_num; ?></div>
                            </div>
                            <div class="c-tile__item c-tile__item--order5">
                                <i class="fa-solid fa-arrow-right"></i><i class="fa-solid fa-heart-circle-plus c-tile__icon"></i>
                                <span class="c-tile__title">いいね</span>
                                <div class="c-tile__num"><?php if(isset($new_get_like_num)) echo $new_get_like_num; ?></div>
                            </div>
                            <div class="c-tile__item c-tile__item--order7">
                                <i class="fa-solid fa-arrow-right"></i><i class="fa-solid fa-retweet c-tile__icon"></i>
                                <span class="c-tile__title">リツイート</span>
                                <div class="c-tile__num"><?php if(isset($new_get_retweet_num)) echo $new_get_retweet_num; ?></div>
                            </div>
                            <!--折り返し-->
                            <div class="c-tile__item c-tile__item--order2">
                                <i class="fa-solid fa-arrow-left"></i><i class="fa-solid fa-user-check c-tile__icon"></i>
                                <span class="c-tile__title">新規フォロー</span>
                                <div class="c-tile__num"><?php if(isset($new_following_num)) echo $new_following_num; ?></div>
                            </div>
                            <div class="c-tile__item c-tile__item--order4">
                                <i class="fa-solid fa-arrow-left"></i><i class="fa-solid fa-user-slash c-tile__icon"></i>
                                <span class="c-tile__title">フォロー解除</span>
                                <div class="c-tile__num"><?php if(isset($unfollow_num)) echo $unfollow_num; ?></div>
                            </div>
                            <div class="c-tile__item c-tile__item--order6">
                                <i class="fa-solid fa-arrow-left"></i><i class="fa-solid fa-heart-circle-check c-tile__icon"></i>
                                <span class="c-tile__title">いいね</span>
                                <div class="c-tile__num"><?php if(isset($new_liking_num)) echo $new_liking_num; ?></div>
                            </div>
                            <div class="c-tile__item c-tile__item--order8">
                                <i class="fa-solid fa-scale-balanced c-tile__icon"></i>
                                <span class="c-tile__title">FF比</span>
                                <div class="c-tile__num"><?php if(isset($current_followers_num) && isset($current_following_num)) echo round($current_followers_num / ($current_following_num +0.00000001), 2);//ゼロ除算対策 ?></div>
                            </div>
                        </div>
                    </div>
                    <!--チャート-->
                    <div class="c-tile u-bgColor--mainBlue--gradient u-color--white">
                        <div class="c-tile__header"><h3>30 Day's Paformance</h3></div>
                        <div class="c-tile__inner">
                            <div class="c-tile__item  c-tile__item--order1">
                                <i class="fa-solid fa-arrow-right"></i><i class="fa-solid fa-user-plus c-tile__icon"></i>
                                <span class="c-tile__title">新規フォロワー</span>
                                <div id="js-chart--followers_hist"></div>
                            </div>
                            <div class="c-tile__item c-tile__item--order3">
                                <i class="fa-solid fa-arrow-right"></i><i class="fa-solid fa-user-minus c-tile__icon"></i>
                                <span class="c-tile__title">フォロー解除</span>
                                <div id="js-chart--unfollowed_hist"></div>
                            </div>
                            <div class="c-tile__item c-tile__item--order5">
                                <i class="fa-solid fa-arrow-right"></i><i class="fa-solid fa-heart-circle-plus c-tile__icon"></i>
                                <span class="c-tile__title">いいね</span>
                                <div id="js-chart--get_like_hist"></div>
                            </div>
                            <div class="c-tile__item c-tile__item--order7">
                                <i class="fa-solid fa-arrow-right"></i><i class="fa-solid fa-retweet c-tile__icon"></i>
                                <span class="c-tile__title">リツイート</span>
                                <div id="js-chart--get_retweet_hist"></div>
                            </div>
                            <!--折り返し-->
                            <div class="c-tile__item c-tile__item--order2">
                                <i class="fa-solid fa-arrow-left"></i><i class="fa-solid fa-user-check c-tile__icon"></i>
                                <span class="c-tile__title">新規フォロー</span>
                                <div id="js-chart--following_hist"></div>
                            </div>
                            <div class="c-tile__item c-tile__item--order4">
                                <i class="fa-solid fa-arrow-left"></i><i class="fa-solid fa-user-slash c-tile__icon"></i>
                                <span class="c-tile__title">フォロー解除</span>
                                <div id="js-chart--unfollow_hist"></div>
                            </div>
                            <div class="c-tile__item c-tile__item--order6">
                                <i class="fa-solid fa-arrow-left"></i><i class="fa-solid fa-heart-circle-check c-tile__icon"></i>
                                <span class="c-tile__title">いいね</span>
                                <div id="js-chart--like_hist"></div>
                            </div>
                            <div class="c-tile__item c-tile__item--order8">
                                <i class="fa-solid fa-scale-balanced c-tile__icon"></i>
                                <span class="c-tile__title">FF比</span>
                                <div id="js-chart--ffratio_hist"></div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="c-tweet_card">
                    <!--トップツイート（いいね）-->
                    <div class="c-tweet_card__header">
                        <i class="fa-solid fa-heart u-color--like u-icon--shadow"></i>
                        <span>いいね　TOP3</span>
                    </div>
                    <div class="c-tweet_card__inner">
                        <?php for($i = 0; $i < 3; $i++){ ?>
                            <a href="<?php echo 'https://twitter.com/' .$my_info['data']['username'] .'/status' .'/' . $tweets_sorted_like['data'][$i]['id'] ; ?>" target="_new" class="c-tweet_card__item">
                                <div class="c-tweet__header">
                                    <img src="<?php echo $my_info['data']['profile_image_url']; ?>" class="c-tweet__profile_img">
                                    <div>
                                        <div class="c-tweet__name"><?php echo $my_info['data']['name']; ?></div>
                                        <div class="c-tweet__screen_name">@<?php echo $my_info['data']['username']; ?></div>
                                    </div>
                                </div>
                                <div class="c-tweet__body">
                                    <div class="c-tweet__text"><?php echo $tweets_sorted_like['data'][$i]['text'] ?></div>
                                </div>
                                <div class="c-tweet__footer">
                                    <div class="c-tweet__reaction">
                                        <i class="fa-solid fa-heart"></i>
                                        <span><?php echo $tweets_sorted_like['data'][$i]['public_metrics']['like_count'] ?></span>
                                        <i class="fa-solid fa-retweet"></i>
                                        <span><?php echo $tweets_sorted_like['data'][$i]['public_metrics']['retweet_count'] ?></span>
                                        <i class="fa-solid fa-message"></i>
                                        <span><?php echo $tweets_sorted_like['data'][$i]['public_metrics']['reply_count'] ?></span>
                                    </div>
                                    <span class="c-tweet__date"><?php echo $Manetter->toJapanTime($tweets_sorted_like['data'][$i]['created_at']); ?></span>
                                </div>
                            </a>
                        <?php } ?>
                    </div>
                
                    <!--トップツイート（リツイート）-->
                    <div class="c-tweet_card__header">
                        <i class="fa-solid fa-retweet u-color--retweet u-icon--shadow"></i>
                        <span>リツイート　TOP3</span>
                    </div>
                    <div class="c-tweet_card__inner">
                        <?php for($i = 0; $i < 3; $i++){ ?>
                            <a href="<?php echo 'https://twitter.com/' .$my_info['data']['username'] .'/status' .'/' . $tweets_sorted_retweet['data'][$i]['id'] ; ?>" target="_new" class="c-tweet_card__item">
                                <div class="c-tweet__header">
                                    <img src="<?php echo $my_info['data']['profile_image_url']; ?>" class="c-tweet__profile_img">
                                    <div>
                                        <div class="c-tweet__name"><?php echo $my_info['data']['name']; ?></div>
                                        <div class="c-tweet__screen_name">@<?php echo $my_info['data']['username']; ?></div>
                                    </div>
                                </div>
                                <div class="c-tweet__body">
                                    <div class="c-tweet__text"><?php echo $tweets_sorted_retweet['data'][$i]['text'] ?></div>
                                </div>
                                <div class="c-tweet__footer">
                                    <div class="c-tweet__reaction">
                                        <i class="fa-solid fa-heart"></i>
                                        <span><?php echo $tweets_sorted_retweet['data'][$i]['public_metrics']['like_count'] ?></span>
                                        <i class="fa-solid fa-retweet"></i>
                                        <span><?php echo $tweets_sorted_retweet['data'][$i]['public_metrics']['retweet_count'] ?></span>
                                        <i class="fa-solid fa-message"></i>
                                        <span><?php echo $tweets_sorted_retweet['data'][$i]['public_metrics']['reply_count'] ?></span>
                                    </div>
                                    <span class="c-tweet__date"><?php echo $Manetter->toJapanTime($tweets_sorted_retweet['data'][$i]['created_at']); ?></span>
                                </div>
                            </a>
                        <?php } ?>
                    </div>
                    
                    <!--トップツイート（リプライ）-->
                    <div class="c-tweet_card__header">
                        <i class="fa-solid fa-message u-color--mainBlue u-icon--shadow"></i>
                        <span>リプライ　TOP3</span>
                    </div>
                    <div class="c-tweet_card__inner">
                        <?php for($i = 0; $i < 3; $i++){ ?>
                            <a href="<?php echo 'https://twitter.com/' .$my_info['data']['username'] .'/status' .'/' . $tweets_sorted_reply['data'][$i]['id'] ; ?>" target="_new" class="c-tweet_card__item">
                                <div class="c-tweet__header">
                                    <img src="<?php echo $my_info['data']['profile_image_url']; ?>" class="c-tweet__profile_img">
                                    <div>
                                        <div class="c-tweet__name"><?php echo $my_info['data']['name']; ?></div>
                                        <div class="c-tweet__screen_name">@<?php echo $my_info['data']['username']; ?></div>
                                    </div>
                                </div>
                                <div class="c-tweet__body">
                                    <div class="c-tweet__text"><?php echo $tweets_sorted_reply['data'][$i]['text'] ?></div>
                                </div>
                                <div class="c-tweet__footer">
                                    <div class="c-tweet__reaction">
                                        <i class="fa-solid fa-heart"></i>
                                        <span><?php echo $tweets_sorted_reply['data'][$i]['public_metrics']['like_count'] ?></span>
                                        <i class="fa-solid fa-retweet"></i>
                                        <span><?php echo $tweets_sorted_reply['data'][$i]['public_metrics']['retweet_count'] ?></span>
                                        <i class="fa-solid fa-message"></i>
                                        <span><?php echo $tweets_sorted_reply['data'][$i]['public_metrics']['reply_count'] ?></span>
                                    </div>
                                    <span class="c-tweet__date"><?php echo $Manetter->toJapanTime($tweets_sorted_reply['data'][$i]['created_at']); ?></span>
                                </div>
                            </a>
                        <?php } ?>
                    </div>
                    
                </div>
            </div>
            <!--右サイドバー(メンション)-->
            <div class="l-container--3col__sidebar">
                <div class="c-panel">
                    <div class="c-panel__header">
                        <div class="c-panel__header__title">
                            <i class="fa-solid fa-message u-color--mainBlue"></i>
                            <h3 class="u-margin--l--5">メンション</h3>
                        </div>
                    </div>
                    
                    <?php 
                        foreach($mentions['data'] as $mention){
                            $profile_img_url = $mention['profile_image_url'];
                            $name = $mention['name'];
                            $screen_name = $mention['username'];
                            $text = $mention['text'];
                            $tweet_id = $mention['edit_history_tweet_ids'][0];
                            $created_date = $Manetter->toJapanTime($mention['created_at']);
                    ?>
                            
                            <a href="https://twitter.com/<?php echo $screen_name; ?>/status/<?php echo $tweet_id; ?>" class="c-tweet" target="_new">
                                <div class="c-tweet__header">
                                    <img src="<?php echo $profile_img_url; ?>" class="c-tweet__profile_img">
                                    <div>
                                        <div class="c-tweet__name"><?php echo $name; ?></div>
                                        <div class="c-tweet__screen_name">@<?php echo $screen_name; ?></div>
                                    </div>
                                </div>
                                <div class="c-tweet__body">
                                    <div class="c-tweet__text"><?php echo $text; ?></div>
                                </div>
                                <div class="c-tweet__footer">
                                    <span class="c-tweet__date"><?php echo $created_date; ?></span>
                                </div>
                            </a>

                    <?php } ?> 

                    
                    
                </div>
            </div>
        </div>

    <!-- チャート生成用スクリプト -->
    <script>
        $(function(){
            $(".js-nav__home").addClass("js-nav--active");
        });
    </script>
    <script>
    google.charts.load('current', {'packages':['corechart']});
    google.charts.setOnLoadCallback(drawChart);

    function drawChart() {

        var hist_data = <?php echo $json_php_hist?>;

        //新規フォロワーのデータ
        const days_num = 30;
        var temp = [];
        temp.push(['日', '新規フォロワー']);
        for(var i = days_num; i >= 1; i-- ){
            temp.push([String(i) + '日前', Number(hist_data['followers_hist']['p' + String(i)])]);
        }
        data_followers_hist = google.visualization.arrayToDataTable(temp);
        //新規フォローのデータ
        var temp = [];
        temp.push(['日', '新規フォロー']);
        for(var i = days_num; i >= 1; i-- ){
            temp.push([String(i) + '日前', Number(hist_data['following_hist']['p' + String(i)])]);
        }
        data_following_hist = google.visualization.arrayToDataTable(temp);
        //新規アンフォローされたのデータ
        var temp = [];
        temp.push(['日', 'フォロワー解除']);
        for(var i = days_num; i >= 1; i-- ){
            temp.push([String(i) + '日前', Number(hist_data['unfollowed_hist']['p' + String(i)])]);
        }
        data_unfollowed_hist = google.visualization.arrayToDataTable(temp);
        //新規アンフォローしたのデータ
        var temp = [];
        temp.push(['日', 'フォロー解除']);
        for(var i = days_num; i >= 1; i-- ){
            temp.push([String(i) + '日前', Number(hist_data['unfollow_hist']['p' + String(i)])]);
        }
        data_unfollow_hist = google.visualization.arrayToDataTable(temp);
        //いいねされたのデータ
        var temp = [];
        temp.push(['日', 'いいね']);
        for(var i = days_num; i >= 1; i-- ){
            temp.push([String(i) + '日前', Number(hist_data['get_like_hist']['p' + String(i)])]);
        }
        data_get_like_hist = google.visualization.arrayToDataTable(temp);
        //いいねしたのデータ
        var temp = [];
        temp.push(['日', 'いいね']);
        for(var i = days_num; i >= 1; i-- ){
            temp.push([String(i) + '日前', Number(hist_data['like_hist']['p' + String(i)])]);
        }
        data_like_hist = google.visualization.arrayToDataTable(temp);
        //リツイートされたのデータ
        var temp = [];
        temp.push(['日', 'リツイート']);
        for(var i = days_num; i >= 1; i-- ){
            temp.push([String(i) + '日前', Number(hist_data['get_retweet_hist']['p' + String(i)])]);
        }
        data_get_retweet_hist = google.visualization.arrayToDataTable(temp);
        //FF比のデータ
        var temp = [];
        var min_ff = 0;
        temp.push(['日', 'FF比']);
        for(var i = days_num; i >= 1; i-- ){
            temp.push([String(i) + '日前', Number(hist_data['ffratio_hist']['p' + String(i)])]);
            if(Number(hist_data['ffratio_hist']['p' + String(i)]) > min_ff) min_ff = Number(hist_data['ffratio_hist']['p' + String(i)]);
        }
        var min_ff_floor = Math.floor(min_ff * 10) / 10;
        console.log(min_ff_floor);
        data_ffratio_hist = google.visualization.arrayToDataTable(temp);
        //チャートオプション
        var options = {
                width: '100%',
                height: '150',
                // フォーカスする対象 datum,category
                focusTarget: 'category',
                colors : ['#fff'],
                //curveType:'function',
                curveType: 'function',
                tooltip: {
                    //表示する情報 both,value,percentage
                    text: 'both',
                    textStyle: {
                        color: '#878787',
                        fontName: 'Arial',
                        fontSize: 12,
                        bold: 'false',
                        italic: 'false',
                    },
                    //ツールチップを表示するタイミング focus,none,selection
                    //trigger: 'focus',
                    //ツールチップに要素色のアイコンを表示する
                    showColorCode: 'true',
                    //HTMLツールチップを使う
                    //isHtml: 'true',
                    //HTMLツールチップのみ反映
                    //ignoreBounds:'false', 
                },
                hAxis: {
                    gridlines:{
                        count : 0,
                        color : '#fff',
                    }, 
                    minorGridlines:{
                        count:0,
                    },
                    textStyle : {
                        color : '#fff',
                    },
                    slantedText: false,
                    showTextEvery: 10,
                    viewWindow:{
                        min:0,
                    },
                    minValue:0,
                    
                }, // Y軸の説明,
                vAxis: {
                    //minValue:0.8,
                    gridlines:{
                        count:2,
                        color : '#fff',
                    }, 
                    minorGridlines:{
                        count:0,
                    },
                    baselineColor: '#fff',
                    textPosition: 'none',
                    textStyle : {
                        color : '#fff',
                    },
                    viewWindow:{
                        min:0,
                    },
                    minValue:0,
                }, // X軸の説明
                chartArea: {
                    //backgroundColor: '#fff',
                    //left: 20,
                    //right: 40,
                    //top: 20,
                    bottom: 20,
                    width: '100%',
                    height: '100%',
                },
                backgroundColor: 'transparent',
                legend: {
                    position: 'none',
                },
        };
        //ffraitio_hist専用のオプション
        var options_ff = {
                width: '100%',
                height: '150',
                // フォーカスする対象 datum,category
                focusTarget: 'category',
                colors : ['#fff'],
                //curveType:'function',
                curveType: 'function',
                tooltip: {
                    //表示する情報 both,value,percentage
                    text: 'both',
                    textStyle: {
                        color: '#878787',
                        fontName: 'Arial',
                        fontSize: 12,
                        bold: 'false',
                        italic: 'false',
                    },
                    //ツールチップを表示するタイミング focus,none,selection
                    //trigger: 'focus',
                    //ツールチップに要素色のアイコンを表示する
                    showColorCode: 'true',
                    //HTMLツールチップを使う
                    //isHtml: 'true',
                    //HTMLツールチップのみ反映
                    //ignoreBounds:'false', 
                },
                hAxis: {
                    gridlines:{
                        count : 0,
                        color : '#fff',
                    }, 
                    minorGridlines:{
                        count:0,
                    },
                    textStyle : {
                        color : '#fff',
                    },
                    slantedText: false,
                    showTextEvery: 10,
                    viewWindow:{
                        min:0,
                    },
                    minValue:0,
                    
                }, // Y軸の説明,
                vAxis: {
                    minValue : min_ff_floor,
                    gridlines:{
                        count:2,
                        color : '#fff',
                    }, 
                    minorGridlines:{
                        count:0,
                    },
                    baselineColor: '#fff',
                    textPosition: 'none',
                    textStyle : {
                        color : '#fff',
                    },
                    viewWindow:{
                        min:0,
                    },
                    minValue:0,
                }, // X軸の説明
                chartArea: {
                    //backgroundColor: '#fff',
                    //left: 20,
                    //right: 40,
                    //top: 20,
                    bottom: 20,
                    width: '100%',
                    height: '100%',
                },
                backgroundColor: 'transparent',
                legend: {
                    position: 'none',
                },
        };

        var chart_followers_hist = new google.visualization.AreaChart(document.getElementById('js-chart--followers_hist'));
        chart_followers_hist.draw(data_followers_hist, options);
        var chart_following_hist = new google.visualization.AreaChart(document.getElementById('js-chart--following_hist'));
        chart_following_hist.draw(data_following_hist, options);
        var chart_unfollowed_hist = new google.visualization.AreaChart(document.getElementById('js-chart--unfollowed_hist'));
        chart_unfollowed_hist.draw(data_unfollowed_hist, options);
        var chart_unfollow_hist = new google.visualization.AreaChart(document.getElementById('js-chart--unfollow_hist'));
        chart_unfollow_hist.draw(data_unfollow_hist, options);
        var chart_get_like_hist = new google.visualization.AreaChart(document.getElementById('js-chart--get_like_hist'));
        chart_get_like_hist.draw(data_get_like_hist, options);
        var chart_like_hist = new google.visualization.AreaChart(document.getElementById('js-chart--like_hist'));
        chart_like_hist.draw(data_like_hist, options);
        var chart_get_retweet_hist = new google.visualization.AreaChart(document.getElementById('js-chart--get_retweet_hist'));
        chart_get_retweet_hist.draw(data_get_retweet_hist, options);
        var chart_ffratio_hist = new google.visualization.AreaChart(document.getElementById('js-chart--ffratio_hist'));
        chart_ffratio_hist.draw(data_ffratio_hist, options_ff);
    }
    </script>
    <script src="app.js"></script>


    </body>

</html>