<?php 
require_once('func_common.php');
//取得するツイートはには、[author_id], [creater_at], [text], [(tweetの)id]を必ず含める

////////////////////////////////
//API用汎用クラス
////////////////////////////////
class UseApi{

    public function makeUrl($base_url, $query){
        return $base_url . '?' . http_build_query($query);
    }

    public function request($url, $method, $query=NULL){

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $this->header);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        if($query){
            curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($query));
        }
        

        $response = curl_exec($curl);
        $result = json_decode($response, true);

        curl_close($curl);

        return $result;
    }

    public function insertParam($base_url, $param_list){
        $new_url = $base_url;
        foreach($param_list as $key => $value){
            $new_url = str_replace($key, $value, $new_url);
        }
        return $new_url;
    }

    public function generateCodeVerifier(int $byte_length = 32)
    {
        $random_bytes_string = openssl_random_pseudo_bytes($byte_length);
        $encoded_random_string = base64_encode($random_bytes_string);
        $url_safe_encoding = [
            '=' => '',
            '+' => '-',
            '/' => '_',
        ];
        $CV = strtr($encoded_random_string, $url_safe_encoding);
        debug('CODE VERIFIER : ' .print_r($CV, true));

        return $CV;
    }

    public function generateCodeChallenge($code_verifier)
    {
        $hash = hash('sha256', $code_verifier, true);
        $CC = str_replace('=', '', strtr(base64_encode($hash), '+/', '-_'));
        debug('CODE VERIFIER : ' .print_r($CC, true));

        return $CC;
    }
}


////////////////////////////////
//Twitter用クラス
////////////////////////////////
class TwitterApi extends UseApi{

    public function __construct($key, $secret, $bearer, $client_id, $client_secret, $redirect_uri){
        $this->key = $key;
        $this->secret = $secret;
        $this->bearer = $bearer;
        $this->header = ['Authorization: Bearer ' . $this->bearer,
                        'Content-Type: application/json'];
        $this->client_id = $client_id;
        $this->client_secret = $client_secret;
        $this->redirect_uri = $redirect_uri;
    }

    //形式変換と時差修正//
    public function toJapanTime($iso8601){
        return date('Y-m-d H:i', strtotime($iso8601));
    }

    ////////////////////////////////
    //認証関係
    ////////////////////////////////

    //認証用URL作成// //TODO:引数にリダイレクトURLを指定
    public function makeAuthorizeUrl(){
        $base_url = 'https://twitter.com/i/oauth2/authorize';
        $code_verifier = $this->generateCodeVerifier();
        $code_challenge = $this->generateCodeChallenge($code_verifier);
        $query = [
            'response_type' => 'code',
            'client_id' => $this->client_id,
            'redirect_uri' => $this->redirect_uri,
            'scope' => 'users.read tweet.read list.read like.read follows.read tweet.write like.write offline.access',
            'state' => $code_verifier,
            'code_challenge' => $code_challenge,
            'code_challenge_method' => 's256'
        ];
        $url = $this->makeUrl($base_url, $query);

        return $url;
    }

    //アクセストークン取得//
    public function getAccessToken($verify, $code){
        $base_url = 'https://api.twitter.com/2/oauth2/token';
        $method = 'POST';

        $query = [
            'grant_type' => 'authorization_code',
            'code' => $code,
            'client_id' => $this->client_id,
            'redirect_uri' => $this->redirect_uri,
            'code_verifier' => $verify,
        ];

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $base_url);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($curl, CURLOPT_USERPWD, sprintf('%s:%s', $this->client_id, $this->client_secret));
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($query));
        
        $response = curl_exec($curl);
        $result = json_decode($response, true);

        curl_close($curl);

        debug('GET_ACCESS_TOKEN : ' .print_r($result, true));
        
        return $result;
    }

    //トークンのリフレッシュ//
    public function refreshToken($refresh_token){
        $base_url = 'https://api.twitter.com/2/oauth2/token';
        $method = 'POST';

        $query = [
            'grant_type' => 'refresh_token',
            'refresh_token' => $refresh_token,
        ];

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $base_url);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($curl, CURLOPT_USERPWD, sprintf('%s:%s', $this->client_id, $this->client_secret));
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($query));
        
        $response = curl_exec($curl);
        $result = json_decode($response, true);
        if(!isset($result['access_token'])) debug('ERR -refreshToken- : ' .print_r($result, true));

        curl_close($curl);

        
        return json_decode($response, true);
    }

    //アクセストークンをhttpヘッダにセット
    public function setTokenToHeader($access_token){
        $this->header = ['Authorization: Bearer ' . $access_token,
                        'Content-Type: application/json'
                    ];
    }


    ////////////////////////////////
    //ツイート関係
    ////////////////////////////////

    //ツイート// //TODO:ヘッダは認証関数で操作
    public function tweet($text){
        
        $base_url = 'https://api.twitter.com/2/tweets';
        $method = 'POST';
        $json_body = json_encode(array(
            'text' => $text
        ));

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $base_url);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $this->header);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $json_body);       

        $response = curl_exec($curl);
        $result = json_decode($response, true);

        curl_close($curl);

        return $result;
    }

    //ツイートを取得//
    public function getTweets($twitter_id, $max = 100, $paging = true){
        $base_url = 'https://api.twitter.com/2/users/:id/tweets';
        $data = [
            ':id' => $twitter_id,
        ];
        //１回目のリクエスト
        $inserted_url = $this->insertParam($base_url,$data);
        $query = [
            'max_results' => $max,
            'exclude' => 'retweets,replies', //リツイート、リプライを除外
            'expansions' => 'author_id,attachments.media_keys',
            'user.fields' => 'name,profile_image_url',
            'media.fields' => 'preview_image_url',
            'tweet.fields' => 'created_at,public_metrics',
        ];
        
        $url = $this->makeUrl($inserted_url, $query);      
        $result = $this->request($url, 'GET');
        

        //取得に失敗した場合
        if(!isset($result['data'])){
            debug('ERROR -getTweet- : ' .print_r($result, true));
            return false;
        } 

        if(isset($result['meta']['next_token'])) $next_token = $result['meta']['next_token'];

        $tweets = array();
        foreach($result['data'] as $one_tweet){
            $tweets['data'][] = $one_tweet;
        }
        if(isset($result['includes']['media'])){
            foreach($result['includes']['media'] as $one_tweet){
                $tweets['includes']['media'][] = $one_tweet;
            }
        }
        if(isset($result['includes']['users'])){
            foreach($result['includes']['users'] as $one_tweet){
                $tweets['includes']['users'][] = $one_tweet;
            }
        }
        
        //2回目以降のリクエスト
        if($paging){
            for($i = 0; $i < 15; $i++){
                if(!$next_token) break;
                debug(print_r($i, true));
                $query = [
                    'pagination_token' => $next_token,
                    'max_results' => $max,
                    'exclude' => 'retweets,replies', //リツイート、リプライを除外
                    'expansions' => 'author_id,attachments.media_keys',
                    'user.fields' => 'name,profile_image_url',
                    'media.fields' => 'preview_image_url',
                    'tweet.fields' => 'created_at,public_metrics',
                ];
                $url_with_next_token = $this->makeUrl($inserted_url, $query);
                $result = $this->request($url_with_next_token, 'GET');
                foreach($result['data'] as $one_tweet){
                    $tweets['data'][] = $one_tweet;
                }
                if(isset($result['includes']['media'])){
                    foreach($result['includes']['media'] as $one_tweet){
                        $tweets['includes']['media'][] = $one_tweet;
                    }
                }
                if(isset($result['includes']['users'])){
                    foreach($result['includes']['users'] as $one_tweet){
                        $tweets['includes']['users'][] = $one_tweet;
                    }
                }
                //next_tokenがレスポンスになければbreak
                (isset($result['meta']['next_token'])) ? $next_token = $result['meta']['next_token'] : $next_token = null;
            }
        }

        return $tweets;
    }

    //ツイート検索//
    public function serchTweets($words){ //検索ワードは複数可、スペースで区切る
        $base_url = 'https://api.twitter.com/2/tweets/search/recent';
        $query = [
            'query' => $words,
            'expansions' => 'author_id,attachments.media_keys',
        ];
        $url = $this->makeUrl($base_url, $query);

        return $this->request($url, 'GET');
    }

    //タイムラインを取得//
    public function getTimelines($twitter_id, $seconds = false){ //$secondsの指定秒以内のツイートを返却
        $base_url = 'https://api.twitter.com/2/users/:id/timelines/reverse_chronological';
        $data = [
            ':id' => $twitter_id
        ];

        $inserted_url = $this->insertParam($base_url,$data);
        if($seconds){
            $now = new DateTime();
            $start_datetime = $now->modify('-' .(string)$seconds .'second');
            $start_str = $start_datetime->format(DateTime::ATOM);
        }else{
            $now = new DateTime();
            $start_datetime = $now->modify('-' .(string)60*60*24*7 .'second');
            $start_str = $start_datetime->format(DateTime::ATOM);
        }
        $query = [
            'exclude' => 'retweets,replies', //リツイートをリプライを除外
            'expansions' => 'author_id,attachments.media_keys',
            'user.fields' => 'name,profile_image_url',
            'tweet.fields' => 'created_at,public_metrics',
            'media.fields' => 'preview_image_url,url',
            
            'start_time' => $start_str,
            
        ];
        $url = $this->makeUrl($inserted_url, $query);
        $result = $this->request($url, 'GET');
        //取得に失敗した場合
        if(!$result['data']) return array('status' => 1101);

        $next_token = $result['meta']['next_token'];
        $tweets = array();
        foreach($result['data'] as $one_tweet){
            $tweets['data'][] = $one_tweet;
        }
        foreach($result['includes']['media'] as $one_tweet){
            $tweets['includes']['media'][] = $one_tweet;
        }
        foreach($result['includes']['users'] as $one_tweet){
            $tweets['includes']['users'][] = $one_tweet;
        }

        //2回目以降のリクエスト
        for($i = 0; $i < 180; $i++){

            if(!$next_token) break;
            
            $query = [
                'pagination_token' => $next_token,
                'exclude' => 'retweets,replies', //リツイートをリプライを除外
                'expansions' => 'author_id,attachments.media_keys',
                'user.fields' => 'name,profile_image_url',
                'tweet.fields' => 'created_at,public_metrics',
                'media.fields' => 'preview_image_url,url',
                
                'start_time' => $start_str,
                
            ];
            var_dump('loop');
            $url_with_next_token = $this->makeUrl($inserted_url, $query);
            $result = $this->request($url_with_next_token, 'GET');
            foreach($result['data'] as $one_tweet){
                $tweets['data'][] = $one_tweet;
            }
            foreach($result['includes']['media'] as $one_tweet){
                $tweets['includes']['media'][] = $one_tweet;
            }
            foreach($result['includes']['users'] as $one_tweet){
                $tweets['includes']['users'][] = $one_tweet;
            }
            $next_token = $result['meta']['next_token'];
        }

        return $tweets;

        
    }

    //テキストと画像のマッチングリスト作成//　//TODO:画像４枚、その他メディアに対応
    //APIからのデータではツイートと画像が別配列に格納されているため
    public function matchTextAndImage($tweets){
        $texts = $tweets['data'];
        $result = array();
        foreach($texts as $one_text){
            $one_media = $one_text['attachments']['media_keys'][0];
            if($one_media === null){
                continue;
            }else{
                $data= [$one_media => $tweets['includes']['media'][array_search($one_media, array_column($tweets['includes']['media'], 'media_key'))]];
                $result[$one_media] = $data;
            }
        };

        return $result;
    }

    //メンションを取得//
    public function getMentions($twitter_id, $max='10'){//max:5~100,それ以上はページネーショントークン
        $base_url = 'https://api.twitter.com/2/users/:id/mentions';
        $param = [
            ':id' => $twitter_id
        ];
        $inserted_url = $this->insertParam($base_url, $param);
        $query = [
            'max_results' => $max,
            'expansions' => 'author_id,attachments.media_keys',
            'user.fields' => 'name,profile_image_url',
            'media.fields' => 'preview_image_url',
            'tweet.fields' => 'created_at',
        ];
        $url = $this->makeUrl($inserted_url, $query);

        return $this->request($url, 'GET');
    }

    //メンション整形//
    public function mergeUserData($tweets){
        for($i = 0; $i < count($tweets['data']); $i++){
            $author_id = $tweets['data'][$i]['author_id'];
            $prof = objectSearch($author_id, $tweets['includes']['users'], 'id'); //func_common.php
            unset($prof['id']);
            $tweets['data'][$i] = array_merge($tweets['data'][$i], $prof);
        }

        return $tweets;
    }

    ////////////////////////////////
    //いいね関係
    ////////////////////////////////
     
    //いいねを付ける//
    public function postLike($twitter_id, $tweet_id){
        $base_url = 'https://api.twitter.com/2/users/:id/likes';
        $data = [
            ':id' => $twitter_id,
        ];
        $inserted_url = $this->insertParam($base_url,$data);
        
        $json_body = json_encode(array(
            'tweet_id' => $tweet_id
        ));


        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $inserted_url);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($curl, CURLOPT_HTTPHEADER, $this->header);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $json_body);       

        $response = curl_exec($curl);
        $result = json_decode($response, true);

        curl_close($curl);

        return $result;
    }

    //いいねしたツイートを取得//
    public function getLikingTweet($twitter_id){
        $base_url = 'https://api.twitter.com/2/users/:id/liked_tweets';
        //１回目のリクエスト
        $data = [
            ':id' => $twitter_id
        ];
        $inserted_url = $this->insertParam($base_url, $data);
        $query = [
            'max_results' => 100,
        ];
        $url = $this->makeUrl($inserted_url, $query);
        $result = $this->request($url, 'GET');
        
        //取得に失敗した場合
        if(!$result['data']){
            debug('ERR -getLikingTweet- : ' .print_r($result, true));
            return array('status' => 1101);
        }

        $next_token = $result['meta']['next_token'];
        $liking = array();
        foreach($result['data'] as $one_tweet){
            $liking['data'][] = $one_tweet;
        }
        //2回目以降のリクエスト
        for($i = 0; $i < 10; $i++){

            if(!$next_token) break;
            
            $query = [
                'pagination_token' => $next_token,
                'max_results' => 100,
            ];
            $url_with_next_token = $this->makeUrl($inserted_url, $query);
            $result = $this->request($url_with_next_token, 'GET');
            foreach($result['data'] as $one_tweet){
                $liking['data'][] = $one_tweet;
            }
            $next_token = $result['meta']['next_token'];
        }
 
        return $liking;

    }

    //いいねされたユーザーを取得//
    public function getLikingUsers($tweet_id){
        $base_url = 'https://api.twitter.com/2/tweets/:id/liking_users';
        $data = [
            ':id' => $tweet_id
        ];
        $url = $this->insertParam($base_url, $data);

        return $this->request($url, 'GET');
    }

    ////////////////////////////////
    //フォロー・フォロワー関係
    ////////////////////////////////

    //フォロワーを取得//
    public function getFollowers($twitter_id){
        $base_url = 'https://api.twitter.com/2/users/:id/followers';
        $data = [
            ':id' => $twitter_id
        ];
        //１回目のリクエスト
        $inserted_url = $this->insertParam($base_url, $data);
        $query = [
            'max_results' => 1000,
        ];
        $url = $this->makeUrl($inserted_url, $query);
        $result = $this->request($url, 'GET');

        //取得に失敗した場合
        if(!$result['data']){
            debug('ERR -getFollowers- : ' .print_r($result, true));
            return array('status' => 1101);
        }

        $next_token = $result['meta']['next_token'];
        $followers = array();
        for($i = 0; $i < count($result['data']); $i++){
            $followers['data'][$i] = $result['data'][$i];
        }
        //2回目以降のリクエスト
        for($i = 0; $i < 15; $i++){

            if(!$next_token) break;
            
            $query = [
                'pagination_token' => $next_token,
                'max_results' => 1000,
            ];
            $url_with_next_token = $this->makeUrl($inserted_url, $query);
            $result = $this->request($url_with_next_token, 'GET');
            for($j = 0; $j < count($result['data']); $j++){
                $followers['data'][$j] = $result['data'][$j];
            }
            $next_token = $result['meta']['next_token'];
        }

        return $followers;
    }

    //フォローを取得//
    public function getFollowing($twitter_id){
        $base_url = 'https://api.twitter.com/2/users/:id/following';
        $data = [
            ':id' => $twitter_id
        ];
        //１回目のリクエスト
        $inserted_url = $this->insertParam($base_url, $data);
        $query = [
            'max_results' => 1000,
        ];
        $url = $this->makeUrl($inserted_url, $query);
        $result = $this->request($url, 'GET');

        //取得に失敗した場合
        if(!$result['data']){
            debug('ERR -getFollowing- : ' .print_r($result, true));
            return array('status' => 1101);
        }
        if(isset($result['meta']['next_token'])) $next_token = $result['meta']['next_token'];

        $following = array();
        foreach($result['data'] as $one_following){
            $following['data'][] = $one_following;
        }
        //2回目以降のリクエスト
        for($i = 0; $i < 15; $i++){
            debug('ROOP : ' .print_r($i, true));
            if(!isset($next_token)) break;
            
            $query = [
                'pagination_token' => $next_token,
                'max_results' => 1000,
            ];
            $url_with_next_token = $this->makeUrl($inserted_url, $query);
            $result = $this->request($url_with_next_token, 'GET');
            foreach($result['data'] as $one_following){
                $following['data'][] = $one_following;
            }
            //next_tokenがレスポンスになければbreak
            (isset($result['meta']['next_token'])) ? $next_token = $result['meta']['next_token'] : $next_token = null;
        }

        return $following;
    }

    ////////////////////////////////
    //リスト関係
    ////////////////////////////////

    //リストを取得
    public function getLists($twitter_id){
        $base_url = "https://api.twitter.com/2/users/:id/owned_lists";
        $data = [
            ':id' => $twitter_id
        ];
        $inserted_url = $this->insertParam($base_url, $data);
        $query = [
            'list.fields'=> 'private,created_at'
        ];
        $url = $this->makeUrl($inserted_url, $query);
        return $this->request($url, 'GET');
    }

    //リストのメンバーを取得//
    public function getListMembers($list_id){
        $base_url = 'https://api.twitter.com/2/lists/:id/members';
        $data = [
            ':id' => $list_id
        ];
        $inserted_url = $this->insertParam($base_url, $data);
        $result = $this->request($inserted_url, 'GET');

        if(!$result['data']) return array('status' => 1101);

        if(isset($result['meta']['next_token'])) $next_token = $result['meta']['next_token'];

        $members = array();
        foreach($result['data'] as $one_member){
            $members['data'][] = $one_member;
        }

        //2回目以降のリクエスト
        for($i = 0; $i < 50; $i++){

            if(!isset($next_token)) break;
            
            $query = [
                'pagination_token' => $next_token,
            ];
            $url_with_next_token = $this->makeUrl($inserted_url, $query);
            $result = $this->request($url_with_next_token, 'GET');
            foreach($result['data'] as $one_member){
                $members['data'][] = $one_member;
            }
            //next_tokenがレスポンスになければbreak
            (isset($result['meta']['next_token'])) ? $next_token = $result['meta']['next_token'] : $next_token = null;
        }

        return $members;
    }

    ////////////////////////////////
    //自身の情報
    ////////////////////////////////

    public function getMyInfo(){
        $base_url = "https://api.twitter.com/2/users/me";
        $query = [
            'user.fields' => 'profile_image_url,public_metrics',
        ];
        $url = $this->makeUrl($base_url, $query);

        $result = $this->request($url, 'GET');

        if(!isset($result['data'])){
            debug('ERROR -getMyInfo- : ' .print_r($result, true));
            return false;
        }
        return $result;

    }

}

?>