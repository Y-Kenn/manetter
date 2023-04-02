<?php 
require_once(dirname(__FILE__).'/func_common.php');
require_once(dirname(__FILE__).'/TwitterAPI.php');
require_once(dirname(__FILE__).'/TwitterDB.php');

class Manetter extends TwitterAPI{

    public const TOKEN_LIFETIME = 7100; //秒

    public function __construct($key, $secret, $bearer, $dsn, $user, $password, $client_id, $client_secret, $redirect_uri){
        parent::__construct($key, $secret, $bearer, $client_id, $client_secret, $redirect_uri);
        $this->TwitterDB = new TwitterDB($dsn, $user, $password);
        
        if(isset($_SESSION['token_limit'])){
            
            $token_lifetime_remaining = $_SESSION['token_limit'] - time();
            debug('TOKEN_LIMIT :' .print_r($token_lifetime_remaining, true));
            if($token_lifetime_remaining < 0){
                $this->refreshToken($_SESSION['twitter_id']);
            }
        }
    }

    ////////////////////////////////
    //認証関係
    ////////////////////////////////

    //TwitterApiからオーバーライド
    //トークンの取得
    public function getAccessToken($verify, $code){
        $token = parent::getAccessToken($verify, $code);
        $this->setTokenToHeader($token['access_token']);

        $_SESSION['token_limit'] = time() + self::TOKEN_LIFETIME;
        return $token;
    }

    //TwitterApiからオーバーライド
    //トークンのリフレッシュ
    public function refreshToken($twitter_id){
        $token = $this->TwitterDB->getRegisteredToken($twitter_id);
        debug('REFRESH TOKEN -> TOKEN : ' .print_r($token, true));
        $refresh = parent::refreshToken($token['refresh_token']);
        debug('REFRESH TOKEN -> REFRESH : ' .print_r($refresh, true));
        $this->setTokenToHeader($refresh['access_token']);
        $my_info = $this->getMyInfo();
        $my_info = $this->getMyInfo();
        $my_info = $this->getMyInfo();
        debug('REFRESH TOKEN -> MY INFO : ' .print_r($my_info, true));
        $this->TwitterDB->updateToken($refresh['access_token'], $refresh['refresh_token'], $my_info['data']['id']);
        

        $_SESSION['token_limit'] = time() + self::TOKEN_LIFETIME;
        debug('REFRESH!!');
        return $refresh;
    }

    ////////////////////////////////
    //いいね・リツイート・リプライ関係
    ////////////////////////////////    

    //本日のいいねした数を取得//
    public function getNewLikingNum($liking, $twitter_id){
        try{
            $query = 'SELECT last_id FROM like_hist WHERE twitter_id = :t_id';
            $data = array(':t_id' => $twitter_id);
            $stmt = $this->TwitterDB->queryPost($query, $data);
            if($stmt){
                $result = $stmt->fetch(PDO::FETCH_ASSOC);
                $key = array_search($result['last_id'], array_column($liking['data'], 'id'));

                return $key;
            }
        }catch(Exception $e){
            debug('エラー発生' .$e->getMessage());
        }

        return false;
    }

    //本日のいいね・リツイート・リプライされた数を取得//
    public function getNewReactionNum($tweets, $twitter_id){
        try{
            //いいね数取得
            $sql = 'SELECT base_id, base_num FROM get_like_hist WHERE twitter_id = :t_id';
            $data = array(':t_id' => $twitter_id);
            $stmt = $this->TwitterDB->queryPost($sql, $data);
            if($stmt){
                $result = $stmt->fetch(PDO::FETCH_ASSOC);
                $base_id = $result['base_id']; //前日のいいね数の基準としたツイートのID
                $key = array_search($base_id, array_column($tweets['data'], 'id'));
                $like_num = 0;
                //base_idのツイートまでのいいね数を合計し、前日までの合計数を減算、今日のいいね数を算出
                for($i = 0; $i <= $key; $i++){
                    $like_num += $tweets['data'][$i]['public_metrics']['like_count'];
                }
                $like_num -= $result['base_num'];
            }else{
                $like_num = '取得に失敗しました';
            }
            //リツイート数取得
            $sql = 'SELECT base_id, base_num FROM get_retweet_hist WHERE twitter_id = :t_id';
            $data = array(':t_id' => $twitter_id);
            $stmt = $this->TwitterDB->queryPost($sql, $data);
            if($stmt){
                $result = $stmt->fetch(PDO::FETCH_ASSOC);
                $base_id = $result['base_id'];
                $key = array_search($base_id, array_column($tweets['data'], 'id'));

                $retweet_num = 0;
                for($i = 0; $i <= $key; $i++){
                    $retweet_num += $tweets['data'][$i]['public_metrics']['retweet_count'];
                }
                $retweet_num -= $result['base_num'];
            }else{
                $retweet_num = '取得に失敗しました';
            }
            //リプライ数取得
            $sql = 'SELECT base_id, base_num FROM get_reply_hist WHERE twitter_id = :t_id';
            $data = array(':t_id' => $twitter_id);
            $stmt = $this->TwitterDB->queryPost($sql, $data);
            if($stmt){
                $result = $stmt->fetch(PDO::FETCH_ASSOC);
                $base_id = $result['base_id'];
                $key = array_search($base_id, array_column($tweets['data'], 'id'));

                $reply_num = 0;
                for($i = 0; $i <= $key; $i++){
                    $reply_num += $tweets['data'][$i]['public_metrics']['reply_count'];
                }
                $reply_num -= $result['base_num'];
            }else{
                $reply_num = '取得に失敗しました';
            }

            $reactions = array(
                'like_count' => $like_num,
                'retweet_count' => $retweet_num,
                'reply_count' => $reply_num,
            );

            return $reactions;

        }catch(Exception $e){
            debug('エラー発生' .$e->getMessage());
        }

        return false;
    }

    ////////////////////////////////
    //フォロー・フォロワー関係
    ////////////////////////////////

    //新規フォロワー数を取得//
    public function getNewFollowersNum($followers, $twitter_id){
        try{
            $sql ='SELECT last_id, followers_num FROM followers_hist WHERE twitter_id = :t_id AND delete_flg = 0';
            $data = array(':t_id' => $twitter_id);
            $stmt = $this->TwitterDB->queryPost($sql, $data);        
            if($stmt){
                $result = $stmt->fetch(PDO::FETCH_ASSOC);
                $last_id = $result['last_id'];
                $key = array_search($last_id, array_column($followers['data'], 'id'));
                if(!$key) $key = count($followers['data']) - $result['followers_num'];
            }else{

                $key = false;
            }            
        }catch(Exception $e){
            debug('エラー発生:' . $e->getMessage());
            $key = false;
        }

        return $key;
    }

    //前日のフォロワー数を取得//
    public function getPreviousDaysFollowersNum($twitter_id){
        try{
            $sql = 'SELECT followers_num FROM followers_hist WHERE twitter_id = :t_id AND delete_flg = 0';
            $data = array(':t_id' => $twitter_id);
            $stmt = $this->TwitterDB->queryPost($sql, $data);
            if($stmt){
                $result = $stmt->fetch(PDO::FETCH_ASSOC);
                $previous_num = $result['followers_num'];

                return $previous_num;
            }

            return false;

        }catch(Exception $e){
            debug('エラー発生：' .$e->getMessage());
        }
        
        return false;
    }

    //新規フォロー数を取得//
    public function getNewFollowingNum($following, $twitter_id){
        try{
            $sql ='SELECT last_id, following_num FROM following_hist WHERE twitter_id = :t_id AND delete_flg = 0';
            $data = array(':t_id' => $twitter_id);
            $stmt = $this->TwitterDB->queryPost($sql, $data);
            if($stmt){
                $result = $stmt->fetch(PDO::FETCH_ASSOC);
                $last_id = $result['last_id'];
                $key = array_search($last_id, array_column($following['data'], 'id'));
                if(!$key) $key = count($following['data']) - $result['following_num'];
            }else{
                $key = false;
            }            
        }catch(Exception $e){
            debug('エラー発生:' . $e->getMessage());
            $key = false;
        }
        return $key;
    }

   //前日のフォロー数を取得//
    public function getPreviousDaysFollowingNum($twitter_id){
        try{
            $sql = 'SELECT following_num FROM following_hist WHERE twitter_id = :t_id AND delete_flg = 0';
            $data = array(':t_id' => $twitter_id);
            $stmt = $this->TwitterDB->queryPost($sql, $data);
            if($stmt){
                $result = $stmt->fetch(PDO::FETCH_ASSOC);
                $previous_num = $result['following_num'];

                return $previous_num;
            }

            return false;

        }catch(Exception $e){
            debug('エラー発生：' .$e->getMessage());
        }
        
        return false;
    }

    ////////////////////////////////
    //ツイートデータの処理
    ////////////////////////////////

    //いいね・リツイート・リプライの多い順にツイートをソート//
    public function sortTweetsByMetrics($tweets, $key){//key = 'like_count' or 'retweet_count' or 'reply_count'
        //引用リツイートの回数をリツイートの回数と統合
        for($i = 0; $i < count($tweets['data']); $i++){
            $tweets['data'][$i]['public_metrics']['retweet_count'] = $tweets['data'][$i]['public_metrics']['retweet_count'] + $tweets['data'][$i]['public_metrics']['quote_count'];
        }
        $count_arr = array();//ソート順を決める配列　例) [0] => 41, ...
        foreach($tweets['data'] as $one_tweet){
            array_push($count_arr, array_column($one_tweet,$key));
        }
        array_multisort($count_arr, SORT_DESC, $tweets['data']);
        
        return $tweets;
    }

    public function sotrTweetsByAuthorId($tweets){
        array_multisort(array_column($tweets['data'],'author_id'), SORT_DESC, $tweets['data']);

        return $tweets;
    }

    //指定秒以内のツイートを抽出//
    public function getTweetsWithin($tweets, $seconds){
        $now = strtotime(date('Y-m-d H:i'));
        $len = count($tweets['data']);
        for($i = 0; $i < $len; $i++){       
            $created_at = strtotime($tweets['data'][$i]['created_at']);
            if($now - $created_at > $seconds){
                unset($tweets['data'][$i]);
            } 
        }

        return $tweets;
    }

    //各ユーザ毎に10ツイートずつ取得
    public function getMembersTweets($members){
        $tweets_members_temp = array();
        $includes_temp = array();
        foreach($members['data'] as $one_member){

            $test_time_start = microtime(true);

            $tweets_one_member = $this->getTweets($one_member['id'], 5,false);
            if(!$tweets_one_member){
                continue;
            }
            $tweets_one_member_wthin = $this->getTweetsWithin($tweets_one_member, 60*60*24);
            $tweets_members_temp = array_merge_recursive($tweets_members_temp, $tweets_one_member_wthin['data']);
            $includes_temp = array_merge_recursive($includes_temp, $tweets_one_member_wthin['includes']);

            debug('SMALL-getTweets : ' .print_r(microtime(true) - $test_time_start, true));
            
        }
        $tweets_members = array();
        $tweets_members['data'] = $tweets_members_temp;
        $tweets_members['includes'] = $includes_temp;

        return $tweets_members;
    }

    public function removeTweetsByAuthorId($tweets, $author_id){
        for($i = 0; $i < count($tweets['data']); $i++){
            if($tweets['data'][$i]['author_id'] === $author_id) unset($tweets['data'][$i]);
        }

        return $tweets;
    }

    

    //いいね済のツイートを削除する//
    public function removeLikingTweets($tweets, $liker_id){
        $tweets_liking = $this->getLikingTweet($liker_id);
        if(isset($tweets_liking['data'])){
            $liking_ids = array_column($tweets_liking['data'], 'id');
            for($i = 0; $i < count($tweets['data']); $i++){
                foreach($liking_ids as $index => $id){
                    if(isset($tweets['data'][$i]['id'])){
                        if($tweets['data'][$i]['id'] === $id) unset($tweets['data'][$i]);
                    }
                }
            }
        }
        
        
        return $tweets;
    }

    //author_idごとにツイートを連想配列にまとめる//
    public function reshapeByAuthorId($tweets){
        $tweets_sorted = $this->sotrTweetsByAuthorId($tweets);//前処理としてaurhor_idでソート
        $tweets_reshaped['data'] = array();
        foreach($tweets_sorted['data'] as $one_tweet){
            $tweets_reshaped['data'][$one_tweet['author_id']][] = $one_tweet;
        }
        
        return $tweets_reshaped;
    }

    //author_idブロックごとにmetricsでソート//
    public function sortReshapedTweetsByMetrics($reshaped_tweets, $metrics){
        $author_ids = array_keys($reshaped_tweets['data']);
        $i = 0;
        foreach($reshaped_tweets['data'] as $one_author){
            $count_arr = array();
            foreach($one_author as $one_tweet){
                array_push($count_arr, array_column($one_tweet, $metrics));
            }
            array_multisort($count_arr, SORT_DESC, $reshaped_tweets['data'][$author_ids[$i]]);
            $i++;
        }

        return $reshaped_tweets;
    }

    //1メンバーにつき1ツイート、指定したmetricsの最も大きいものを選択//
    public function selectTweetsByMembers($reshaped_tweets, $members, $metrics, $select_num, $shuffle = true){
        //author_idのブロック毎にいいね数でソート
        $sorted_tweets_like = $this->sortReshapedTweetsByMetrics($reshaped_tweets, $metrics);
        $selected_tweets = array();
        
        foreach($members['data'] as $one_member){
            if($sorted_tweets_like['data'][$one_member['id']]){
                
                $selected_tweets['data'][] = array_shift($sorted_tweets_like['data'][$one_member['id']]);
            }
        }
        if($shuffle) shuffle($selected_tweets['data']);

        if($select_num < count($selected_tweets['data'])){
            $selected_tweets['data'] = array_slice($selected_tweets['data'], 0, $select_num);
        }
        
        return $selected_tweets;
    }

    public function makeRecomendListTweets($tweets, $list_members, $select_num){
        $list_members_num = count($list_members['data']);
        if($select_num > $list_members_num) $select_num = $list_members_num;

        $selected_members_index = array_rand($list_members['data'], $select_num);
        $selected_t = array();

    }

    ////////////////////////////////
    //バッチ処理関係
    ////////////////////////////////
    //引数のツイートのいいね・リツイート・リプライをそれぞれ全て合計
    public function getAllReactionNum($tweets){
        $like_count = 0;
        $retweet_count = 0;
        $reply_count = 0;
        foreach($tweets['data'] as $one_tweet){
            $like_count += $one_tweet['public_metrics']['like_count'];
            $retweet_count += $one_tweet['public_metrics']['retweet_count'];
            $reply_count += $one_tweet['public_metrics']['reply_count'];
        }
        $result = array(
            'like_count' => $like_count,
            'retweet_count' => $retweet_count,
            'reply_count' => $reply_count,
        );

        return $result;
    }


}
