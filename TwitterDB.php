<?php 

class UseDB{

    public function __construct($dsn, $user, $password){
        $options = array(
            PDO::ATTR_ERRMODE => PDO::ERRMODE_SILENT,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
            PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => true,
        );
        $this->dbh = new PDO($dsn, $user, $password, $options);

    }
    
    function queryPost($sql, $data){
        $stmt = $this->dbh->prepare($sql);
        if(!$stmt->execute($data)){
            debug('クエリに失敗');
            debug('失敗したSQL：' .print_r($stmt, true));
            return false;
        }else{
            debug('クエリ成功');
            return $stmt;
        }
        
    }
}

class TwitterDB extends UseDB{

    public function __construct($dsn, $user, $password){
        parent::__construct($dsn, $user, $password);
    }

    ////////////////////////////////
    //ユーザ登録処理
    ////////////////////////////////
    //ユーザ登録//
    public function registUser($twitter_id){
        try{
            $query = 'INSERT INTO users (twitter_id, login_time, create_date) VALUES(:t_id, :l_time, :c_date)';
            $data = array(
                ':t_id' => $twitter_id,
                ':l_time' => date('Y-m-d H:i:s'),
                ':c_date' => date('Y-m-d H:i:s'),
            );
            $stmt = $this->queryPost($query, $data);
            if($stmt){
                $result = $stmt->fetch(PDO::FETCH_ASSOC);
            }else{
                $result = false;
            }
        }catch(Exception $e){
            debug('エラー発生' .$e->getMessage());
            $result = false;
        }

        return $result;
    }
    
    //チャート用ヒストリー作成
    public function makeFollowersHist($twitter_id){
        try{
            $query = 'INSERT INTO followers_hist (twitter_id) VALUE(:t_id)';
            $data = array(':t_id' => $twitter_id);
            $stmt = $this->queryPost($query, $data);
            if($stmt){
                $result = $stmt->fetch(PDO::FETCH_ASSOC);
            }else{
                $result = false;
            }
        }catch(Exception $e){
            debug('エラー発生' .$e->getMessage());
            $result = false;
        }

        return $result;
    }
    public function makeFollowingHist($twitter_id){
        try{
            $query = 'INSERT INTO following_hist (twitter_id) VALUE(:t_id)';
            $data = array(':t_id' => $twitter_id);
            $stmt = $this->queryPost($query, $data);
            if($stmt){
                $result = $stmt->fetch(PDO::FETCH_ASSOC);
            }else{
                $result = false;
            }
        }catch(Exception $e){
            debug('エラー発生' .$e->getMessage());
            $result = false;
        }

        return $result;
    }
    public function makeUnfollowedHist($twitter_id){
        try{
            $query = 'INSERT INTO unfollowed_hist (twitter_id) VALUE(:t_id)';
            $data = array(':t_id' => $twitter_id);
            $stmt = $this->queryPost($query, $data);
            if($stmt){
                $result = $stmt->fetch(PDO::FETCH_ASSOC);
            }else{
                $result = false;
            }
        }catch(Exception $e){
            debug('エラー発生' .$e->getMessage());
            $result = false;
        }

        return $result;
    }
    public function makeUnfollowHist($twitter_id){
        try{
            $query = 'INSERT INTO unfollow_hist (twitter_id) VALUE(:t_id)';
            $data = array(':t_id' => $twitter_id);
            $stmt = $this->queryPost($query, $data);
            if($stmt){
                $result = $stmt->fetch(PDO::FETCH_ASSOC);
            }else{
                $result = false;
            }
        }catch(Exception $e){
            debug('エラー発生' .$e->getMessage());
            $result = false;
        }

        return $result;
    }
    public function makeGetLikeHist($twitter_id){
        try{
            $query = 'INSERT INTO get_like_hist (twitter_id) VALUE(:t_id)';
            $data = array(':t_id' => $twitter_id);
            $stmt = $this->queryPost($query, $data);
            if($stmt){
                $result = $stmt->fetch(PDO::FETCH_ASSOC);
            }else{
                $result = false;
            }
        }catch(Exception $e){
            debug('エラー発生' .$e->getMessage());
            $result = false;
        }

        return $result;
    }
    public function makeLikeHist($twitter_id){
        try{
            $query = 'INSERT INTO like_hist (twitter_id) VALUE(:t_id)';
            $data = array(':t_id' => $twitter_id);
            $stmt = $this->queryPost($query, $data);
            if($stmt){
                $result = $stmt->fetch(PDO::FETCH_ASSOC);
            }else{
                $result = false;
            }
        }catch(Exception $e){
            debug('エラー発生' .$e->getMessage());
            $result = false;
        }

        return $result;
    }
    public function makeGetRetweetHist($twitter_id){
        try{
            $query = 'INSERT INTO get_retweet_hist (twitter_id) VALUE(:t_id)';
            $data = array(':t_id' => $twitter_id);
            $stmt = $this->queryPost($query, $data);
            if($stmt){
                $result = $stmt->fetch(PDO::FETCH_ASSOC);
            }else{
                $result = false;
            }
        }catch(Exception $e){
            debug('エラー発生' .$e->getMessage());
            $result = false;
        }

        return $result;
    }
    public function makeGetReplyHist($twitter_id){
        try{
            $query = 'INSERT INTO get_reply_hist (twitter_id) VALUE(:t_id)';
            $data = array(':t_id' => $twitter_id);
            $stmt = $this->queryPost($query, $data);
            if($stmt){
                $result = $stmt->fetch(PDO::FETCH_ASSOC);
            }else{
                $result = false;
            }
        }catch(Exception $e){
            debug('エラー発生' .$e->getMessage());
            $result = false;
        }

        return $result;
    }
    public function makeffratioHist($twitter_id){
        try{
            $query = 'INSERT INTO ffratio_hist (twitter_id) VALUE(:t_id)';
            $data = array(':t_id' => $twitter_id);
            $stmt = $this->queryPost($query, $data);
            if($stmt){
                $result = $stmt->fetch(PDO::FETCH_ASSOC);
            }else{
                $result = false;
            }
        }catch(Exception $e){
            debug('エラー発生' .$e->getMessage());
            $result = false;
        }

        return $result;
    }
    public function makeHist($twitter_id){
        $this->makeFollowersHist($twitter_id);
        $this->makeFollowingHist($twitter_id);
        $this->makeUnfollowedHist($twitter_id);
        $this->makeUnfollowHist($twitter_id);
        $this->makeGetLikeHist($twitter_id);
        $this->makeLikeHist($twitter_id);
        $this->makeGetRetweetHist($twitter_id);
        $this->makeGetReplyHist($twitter_id);
        $this->makeffratioHist($twitter_id);
    }

    ////////////////////////////////
    //認証関係
    ////////////////////////////////

    //トークンをDBへ登録//
    public function updateToken($access_token, $refresh_token, $tw_id){
        try{
            $query = 'UPDATE users SET access_token = :a_token, refresh_token = :r_token, token_generate_time = :t_g_time WHERE twitter_id = :tw_id AND delete_flg = 0';
            $data = array(
                ':a_token' => $access_token,
                ':r_token' => $refresh_token,
                ':tw_id' => $tw_id,
                't_g_time' => date('Y-m-d H:i:s'),
            );
            $stmt = $this->queryPost($query, $data);
            if($stmt){
                $result = $stmt->fetch(PDO::FETCH_ASSOC);
            }else{
                $result = false;
            }
        }catch(Exception $e){
            debug('エラー発生' .$e->getMessage());
            $result = false;
        }

        return $result;
    }

    //Twitter IDからユーザIDを取得
    public function searchAccountByTwitterId($tw_id){
        try{
            $query = 'SELECT id FROM users WHERE twitter_id = :t_id AND delete_flg = 0';
            $data = array(':t_id' => $tw_id);
            $stmt = $this->queryPost($query, $data);
            if($stmt){
                $result = $stmt->fetch(PDO::FETCH_ASSOC);
            }else{
                $result = false;
            }
        }catch(Exception $e){
            debug('エラー発生' .$e->getMessage());
            $result = false;
        }

        return $result;
    }

    //退会済アカウントを検索
    public function searchWithdrawedAccountByTwitterId($tw_id){
        try{
            $query = 'SELECT id FROM users WHERE twitter_id = :t_id AND delete_flg = 1';
            $data = array(':t_id' => $tw_id);
            $stmt = $this->queryPost($query, $data);
            if($stmt){
                $result = $stmt->fetch(PDO::FETCH_ASSOC);
            }else{
                $result = false;
            }
        }catch(Exception $e){
            debug('エラー発生' .$e->getMessage());
            $result = false;
        }

        return $result;
    }
    
    //DBからアクセストークン・リフレッシュトークン・トークンの生成日時を取得
    public function getRegisteredToken($twitter_id){
        try{
            $query = 'SELECT access_token, refresh_token, token_generate_time FROM users WHERE twitter_id = :t_id';
            $data = array(':t_id' => $twitter_id);
            $stmt = $this->queryPost($query, $data);
            if($stmt){
                $result = $stmt->fetch(PDO::FETCH_ASSOC);
            }else{
                $result = false;
            }
        }catch(Exception $e){
            debug('エラー発生' .$e->getMessage());
            $result = false;
        }

        return $result;
    }

    ////////////////////////////////
    //常用処理
    ////////////////////////////////

    //ヒストリー取得
    public function followers_hist($twitter_id){
        try{
            $query = 'SELECT * FROM followers_hist WHERE twitter_id = :t_id';
            $data = array(':t_id' => $twitter_id);
            $stmt = $this->queryPost($query, $data);
            if($stmt){
                $result = $stmt->fetch(PDO::FETCH_ASSOC);
            }else{
                $result = false;
            }
        }catch(Exception $e){
            debug('エラー発生' .$e->getMessage());
            $result = false;
        }

        return $result;
    }
    public function following_hist($twitter_id){
        try{
            $query = 'SELECT * FROM following_hist WHERE twitter_id = :t_id';
            $data = array(':t_id' => $twitter_id);
            $stmt = $this->queryPost($query, $data);
            if($stmt){
                $result = $stmt->fetch(PDO::FETCH_ASSOC);
            }else{
                $result = false;
            }
        }catch(Exception $e){
            debug('エラー発生' .$e->getMessage());
            $result = false;
        }

        return $result;
    }
    public function unfollowed_hist($twitter_id){
        try{
            $query = 'SELECT * FROM unfollowed_hist WHERE twitter_id = :t_id';
            $data = array(':t_id' => $twitter_id);
            $stmt = $this->queryPost($query, $data);
            if($stmt){
                $result = $stmt->fetch(PDO::FETCH_ASSOC);
            }else{
                $result = false;
            }
        }catch(Exception $e){
            debug('エラー発生' .$e->getMessage());
            $result = false;
        }

        return $result;
    }    
    public function unfollow_hist($twitter_id){
        try{
            $query = 'SELECT * FROM unfollow_hist WHERE twitter_id = :t_id';
            $data = array(':t_id' => $twitter_id);
            $stmt = $this->queryPost($query, $data);
            if($stmt){
                $result = $stmt->fetch(PDO::FETCH_ASSOC);
            }else{
                $result = false;
            }
        }catch(Exception $e){
            debug('エラー発生' .$e->getMessage());
            $result = false;
        }

        return $result;
    }
    public function get_like_hist($twitter_id){
        try{
            $query = 'SELECT * FROM get_like_hist WHERE twitter_id = :t_id';
            $data = array(':t_id' => $twitter_id);
            $stmt = $this->queryPost($query, $data);
            if($stmt){
                $result = $stmt->fetch(PDO::FETCH_ASSOC);
            }else{
                $result = false;
            }
        }catch(Exception $e){
            debug('エラー発生' .$e->getMessage());
            $result = false;
        }

        return $result;
    }
    public function like_hist($twitter_id){
        try{
            $query = 'SELECT * FROM like_hist WHERE twitter_id = :t_id';
            $data = array(':t_id' => $twitter_id);
            $stmt = $this->queryPost($query, $data);
            if($stmt){
                $result = $stmt->fetch(PDO::FETCH_ASSOC);
            }else{
                $result = false;
            }
        }catch(Exception $e){
            debug('エラー発生' .$e->getMessage());
            $result = false;
        }

        return $result;
    }
    public function get_retweet_hist($twitter_id){
        try{
            $query = 'SELECT * FROM get_retweet_hist WHERE twitter_id = :t_id';
            $data = array(':t_id' => $twitter_id);
            $stmt = $this->queryPost($query, $data);
            if($stmt){
                $result = $stmt->fetch(PDO::FETCH_ASSOC);
            }else{
                $result = false;
            }
        }catch(Exception $e){
            debug('エラー発生' .$e->getMessage());
            $result = false;
        }

        return $result;
    }
    public function ffratio_hist($twitter_id){
        try{
            $query = 'SELECT * FROM ffratio_hist WHERE twitter_id = :t_id';
            $data = array(':t_id' => $twitter_id);
            $stmt = $this->queryPost($query, $data);
            if($stmt){
                $result = $stmt->fetch(PDO::FETCH_ASSOC);
            }else{
                $result = false;
            }
        }catch(Exception $e){
            debug('エラー発生' .$e->getMessage());
            $result = false;
        }

        return $result;
    }
    public function get_all_hist($twitter_id){
        $result = array(
            'followers_hist' => $this->followers_hist($twitter_id),
            'following_hist' => $this->following_hist($twitter_id),
            'unfollowed_hist' => $this->unfollowed_hist($twitter_id),
            'unfollow_hist' => $this->unfollow_hist($twitter_id),
            'get_like_hist' => $this->get_like_hist($twitter_id),
            'like_hist' => $this->like_hist($twitter_id),
            'get_retweet_hist' => $this->get_retweet_hist($twitter_id),
            'ffratio_hist' => $this->ffratio_hist($twitter_id)
        );
  
        return $result;
    }


    ////////////////////////////////
    //バッチ処理関係
    ////////////////////////////////

    //全アカウントのトークン情報を取得
    public function getRegisteredTokenForBatch(){
        try{
            $query = 'SELECT twitter_id, access_token, refresh_token, token_generate_time FROM users WHERE delete_flg = 0';
            $data = array();
            $stmt = $this->queryPost($query, $data);
            if($stmt){
                $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            }else{
                $result = false;
            }
        }catch(Exception $e){
            debug('エラー発生' .$e->getMessage());
            $result = false;
        }

        return $result;
    }

    //$tableで指定したテーブルから、$twitter_idのユーザの全てのカラム情報を取得
    public function getAllColumn($table, $twitter_id){
        try{
            $query = 'SELECT * FROM ' .$table .' WHERE delete_flg = 0 AND twitter_id = :t_id';
            $data = array(
                //':table' => $table,
                ':t_id' => $twitter_id,
            );
            $stmt = $this->queryPost($query, $data);
            if($stmt){
                $result = $stmt->fetch(PDO::FETCH_ASSOC);
            }else{
                $result = false;
            }
        }catch(Exception $e){
            debug('エラー発生' .$e->getMessage());
            $result = false;
        }

        return $result;
    }

    //ヒストリーのアップデート
    public function updateFollowersHist($values, $twitter_id){
        try{
            $query = 'UPDATE followers_hist SET last_id = :l_id, followers_num = :f_num , p1 = :p1, p2 = :p2, p3 = :p3, p4 = :p4, p5 = :p5, p6 = :p6, p7 = :p7, p8 = :p8, p9 = :p9, p10 = :p10, p11 = :p11, p12 = :p12, p13 = :p13, p14 = :p14, p15 = :p15, p16 = :p16, p17 = :p17, p18 = :p18, p19 = :p19, p20 = :p20, p21 = :p21, p22 = :p22, p23 = :p23, p24 = :p24, p25 = :p25, p26 = :p26, p27 = :p27, p28 = :p28, p29 = :p29, p30 = :p30 WHERE delete_flg = 0 AND twitter_id = :t_id';
            $data = array( ':t_id' => $twitter_id, ':l_id' => $values['last_id'], ':f_num' => $values['followers_num'],
                ':p1' => $values['p1'], ':p2' => $values['p2'], ':p3' => $values['p3'], ':p4' => $values['p4'], ':p5' => $values['p5'], ':p6' => $values['p6'], ':p7' => $values['p7'], ':p8' => $values['p8'], ':p9' => $values['p9'], ':p10' => $values['p10'],
                ':p11' => $values['p11'], ':p12' => $values['p12'], ':p13' => $values['p13'], ':p14' => $values['p14'], ':p15' => $values['p15'], ':p16' => $values['p16'], ':p17' => $values['p17'], ':p18' => $values['p18'], ':p19' => $values['p19'], ':p20' => $values['p20'],
                ':p21' => $values['p21'], ':p22' => $values['p22'], ':p23' => $values['p23'], ':p24' => $values['p24'], ':p25' => $values['p25'], ':p26' => $values['p26'], ':p27' => $values['p27'], ':p28' => $values['p28'], ':p29' => $values['p29'], ':p30' => $values['p30'],
            );
            //debug('VALUES : ' .print_r($data, true));
            $stmt = $this->queryPost($query, $data);
            if($stmt){
                $result = $stmt->fetch(PDO::FETCH_ASSOC);
            }else{
                $result = false;
            }
        }catch(Exception $e){
            debug('エラー発生' .$e->getMessage());
            $result = false;
        }

        return $result;
    }
    public function updateFollowingHist($values, $twitter_id){
        try{
            $query = 'UPDATE following_hist SET last_id = :l_id, following_num = :f_num , p1 = :p1, p2 = :p2, p3 = :p3, p4 = :p4, p5 = :p5, p6 = :p6, p7 = :p7, p8 = :p8, p9 = :p9, p10 = :p10, p11 = :p11, p12 = :p12, p13 = :p13, p14 = :p14, p15 = :p15, p16 = :p16, p17 = :p17, p18 = :p18, p19 = :p19, p20 = :p20, p21 = :p21, p22 = :p22, p23 = :p23, p24 = :p24, p25 = :p25, p26 = :p26, p27 = :p27, p28 = :p28, p29 = :p29, p30 = :p30 WHERE delete_flg = 0 AND twitter_id = :t_id';
            $data = array( ':t_id' => $twitter_id, ':l_id' => $values['last_id'], ':f_num' => $values['following_num'],
                ':p1' => $values['p1'], ':p2' => $values['p2'], ':p3' => $values['p3'], ':p4' => $values['p4'], ':p5' => $values['p5'], ':p6' => $values['p6'], ':p7' => $values['p7'], ':p8' => $values['p8'], ':p9' => $values['p9'], ':p10' => $values['p10'],
                ':p11' => $values['p11'], ':p12' => $values['p12'], ':p13' => $values['p13'], ':p14' => $values['p14'], ':p15' => $values['p15'], ':p16' => $values['p16'], ':p17' => $values['p17'], ':p18' => $values['p18'], ':p19' => $values['p19'], ':p20' => $values['p20'],
                ':p21' => $values['p21'], ':p22' => $values['p22'], ':p23' => $values['p23'], ':p24' => $values['p24'], ':p25' => $values['p25'], ':p26' => $values['p26'], ':p27' => $values['p27'], ':p28' => $values['p28'], ':p29' => $values['p29'], ':p30' => $values['p30'],
            );
            //debug('VALUES : ' .print_r($data, true));
            $stmt = $this->queryPost($query, $data);
            if($stmt){
                $result = $stmt->fetch(PDO::FETCH_ASSOC);
            }else{
                $result = false;
            }
        }catch(Exception $e){
            debug('エラー発生' .$e->getMessage());
            $result = false;
        }

        return $result;
    }
    public function updateUnfollowedHist($values, $twitter_id){
        try{
            $query = 'UPDATE unfollowed_hist SET p1 = :p1, p2 = :p2, p3 = :p3, p4 = :p4, p5 = :p5, p6 = :p6, p7 = :p7, p8 = :p8, p9 = :p9, p10 = :p10, p11 = :p11, p12 = :p12, p13 = :p13, p14 = :p14, p15 = :p15, p16 = :p16, p17 = :p17, p18 = :p18, p19 = :p19, p20 = :p20, p21 = :p21, p22 = :p22, p23 = :p23, p24 = :p24, p25 = :p25, p26 = :p26, p27 = :p27, p28 = :p28, p29 = :p29, p30 = :p30 WHERE delete_flg = 0 AND twitter_id = :t_id';
            $data = array( ':t_id' => $twitter_id,
                ':p1' => $values['p1'], ':p2' => $values['p2'], ':p3' => $values['p3'], ':p4' => $values['p4'], ':p5' => $values['p5'], ':p6' => $values['p6'], ':p7' => $values['p7'], ':p8' => $values['p8'], ':p9' => $values['p9'], ':p10' => $values['p10'],
                ':p11' => $values['p11'], ':p12' => $values['p12'], ':p13' => $values['p13'], ':p14' => $values['p14'], ':p15' => $values['p15'], ':p16' => $values['p16'], ':p17' => $values['p17'], ':p18' => $values['p18'], ':p19' => $values['p19'], ':p20' => $values['p20'],
                ':p21' => $values['p21'], ':p22' => $values['p22'], ':p23' => $values['p23'], ':p24' => $values['p24'], ':p25' => $values['p25'], ':p26' => $values['p26'], ':p27' => $values['p27'], ':p28' => $values['p28'], ':p29' => $values['p29'], ':p30' => $values['p30'],
            );
            debug('VALUES : ' .print_r($data, true));
            $stmt = $this->queryPost($query, $data);
            if($stmt){
                $result = $stmt->fetch(PDO::FETCH_ASSOC);
            }else{
                $result = false;
            }
        }catch(Exception $e){
            debug('エラー発生' .$e->getMessage());
            $result = false;
        }

        return $result;
    }
    public function updateUnfollowHist($values, $twitter_id){
        try{
            $query = 'UPDATE unfollow_hist SET p1 = :p1, p2 = :p2, p3 = :p3, p4 = :p4, p5 = :p5, p6 = :p6, p7 = :p7, p8 = :p8, p9 = :p9, p10 = :p10, p11 = :p11, p12 = :p12, p13 = :p13, p14 = :p14, p15 = :p15, p16 = :p16, p17 = :p17, p18 = :p18, p19 = :p19, p20 = :p20, p21 = :p21, p22 = :p22, p23 = :p23, p24 = :p24, p25 = :p25, p26 = :p26, p27 = :p27, p28 = :p28, p29 = :p29, p30 = :p30 WHERE delete_flg = 0 AND twitter_id = :t_id';
            $data = array( ':t_id' => $twitter_id,
                ':p1' => $values['p1'], ':p2' => $values['p2'], ':p3' => $values['p3'], ':p4' => $values['p4'], ':p5' => $values['p5'], ':p6' => $values['p6'], ':p7' => $values['p7'], ':p8' => $values['p8'], ':p9' => $values['p9'], ':p10' => $values['p10'],
                ':p11' => $values['p11'], ':p12' => $values['p12'], ':p13' => $values['p13'], ':p14' => $values['p14'], ':p15' => $values['p15'], ':p16' => $values['p16'], ':p17' => $values['p17'], ':p18' => $values['p18'], ':p19' => $values['p19'], ':p20' => $values['p20'],
                ':p21' => $values['p21'], ':p22' => $values['p22'], ':p23' => $values['p23'], ':p24' => $values['p24'], ':p25' => $values['p25'], ':p26' => $values['p26'], ':p27' => $values['p27'], ':p28' => $values['p28'], ':p29' => $values['p29'], ':p30' => $values['p30'],
            );
            debug('VALUES : ' .print_r($data, true));
            $stmt = $this->queryPost($query, $data);
            if($stmt){
                $result = $stmt->fetch(PDO::FETCH_ASSOC);
            }else{
                $result = false;
            }
        }catch(Exception $e){
            debug('エラー発生' .$e->getMessage());
            $result = false;
        }

        return $result;
    }
    public function updateGetLikeHist($values, $twitter_id){
        try{
            $query = 'UPDATE get_like_hist SET base_id = :b_id, base_num = :b_num, p1 = :p1, p2 = :p2, p3 = :p3, p4 = :p4, p5 = :p5, p6 = :p6, p7 = :p7, p8 = :p8, p9 = :p9, p10 = :p10, p11 = :p11, p12 = :p12, p13 = :p13, p14 = :p14, p15 = :p15, p16 = :p16, p17 = :p17, p18 = :p18, p19 = :p19, p20 = :p20, p21 = :p21, p22 = :p22, p23 = :p23, p24 = :p24, p25 = :p25, p26 = :p26, p27 = :p27, p28 = :p28, p29 = :p29, p30 = :p30 WHERE delete_flg = 0 AND twitter_id = :t_id';
            $data = array( ':t_id' => $twitter_id, ':b_id' => $values['base_id'], ':b_num' => $values['base_num'],
                ':p1' => $values['p1'], ':p2' => $values['p2'], ':p3' => $values['p3'], ':p4' => $values['p4'], ':p5' => $values['p5'], ':p6' => $values['p6'], ':p7' => $values['p7'], ':p8' => $values['p8'], ':p9' => $values['p9'], ':p10' => $values['p10'],
                ':p11' => $values['p11'], ':p12' => $values['p12'], ':p13' => $values['p13'], ':p14' => $values['p14'], ':p15' => $values['p15'], ':p16' => $values['p16'], ':p17' => $values['p17'], ':p18' => $values['p18'], ':p19' => $values['p19'], ':p20' => $values['p20'],
                ':p21' => $values['p21'], ':p22' => $values['p22'], ':p23' => $values['p23'], ':p24' => $values['p24'], ':p25' => $values['p25'], ':p26' => $values['p26'], ':p27' => $values['p27'], ':p28' => $values['p28'], ':p29' => $values['p29'], ':p30' => $values['p30'],
            );
            debug('VALUES : ' .print_r($data, true));
            $stmt = $this->queryPost($query, $data);
            if($stmt){
                $result = $stmt->fetch(PDO::FETCH_ASSOC);
            }else{
                $result = false;
            }
        }catch(Exception $e){
            debug('エラー発生' .$e->getMessage());
            $result = false;
        }

        return $result;
    }
    public function updateGetRetweetHist($values, $twitter_id){
        try{
            $query = 'UPDATE get_retweet_hist SET base_id = :b_id, base_num = :b_num, p1 = :p1, p2 = :p2, p3 = :p3, p4 = :p4, p5 = :p5, p6 = :p6, p7 = :p7, p8 = :p8, p9 = :p9, p10 = :p10, p11 = :p11, p12 = :p12, p13 = :p13, p14 = :p14, p15 = :p15, p16 = :p16, p17 = :p17, p18 = :p18, p19 = :p19, p20 = :p20, p21 = :p21, p22 = :p22, p23 = :p23, p24 = :p24, p25 = :p25, p26 = :p26, p27 = :p27, p28 = :p28, p29 = :p29, p30 = :p30 WHERE delete_flg = 0 AND twitter_id = :t_id';
            $data = array( ':t_id' => $twitter_id, ':b_id' => $values['base_id'], ':b_num' => $values['base_num'],
                ':p1' => $values['p1'], ':p2' => $values['p2'], ':p3' => $values['p3'], ':p4' => $values['p4'], ':p5' => $values['p5'], ':p6' => $values['p6'], ':p7' => $values['p7'], ':p8' => $values['p8'], ':p9' => $values['p9'], ':p10' => $values['p10'],
                ':p11' => $values['p11'], ':p12' => $values['p12'], ':p13' => $values['p13'], ':p14' => $values['p14'], ':p15' => $values['p15'], ':p16' => $values['p16'], ':p17' => $values['p17'], ':p18' => $values['p18'], ':p19' => $values['p19'], ':p20' => $values['p20'],
                ':p21' => $values['p21'], ':p22' => $values['p22'], ':p23' => $values['p23'], ':p24' => $values['p24'], ':p25' => $values['p25'], ':p26' => $values['p26'], ':p27' => $values['p27'], ':p28' => $values['p28'], ':p29' => $values['p29'], ':p30' => $values['p30'],
            );
            debug('VALUES : ' .print_r($data, true));
            $stmt = $this->queryPost($query, $data);
            if($stmt){
                $result = $stmt->fetch(PDO::FETCH_ASSOC);
            }else{
                $result = false;
            }
        }catch(Exception $e){
            debug('エラー発生' .$e->getMessage());
            $result = false;
        }

        return $result;
    }
    public function updateGetReplyHist($values, $twitter_id){
        try{
            $query = 'UPDATE get_reply_hist SET base_id = :b_id, base_num = :b_num, p1 = :p1, p2 = :p2, p3 = :p3, p4 = :p4, p5 = :p5, p6 = :p6, p7 = :p7, p8 = :p8, p9 = :p9, p10 = :p10, p11 = :p11, p12 = :p12, p13 = :p13, p14 = :p14, p15 = :p15, p16 = :p16, p17 = :p17, p18 = :p18, p19 = :p19, p20 = :p20, p21 = :p21, p22 = :p22, p23 = :p23, p24 = :p24, p25 = :p25, p26 = :p26, p27 = :p27, p28 = :p28, p29 = :p29, p30 = :p30 WHERE delete_flg = 0 AND twitter_id = :t_id';
            $data = array( ':t_id' => $twitter_id, ':b_id' => $values['base_id'], ':b_num' => $values['base_num'],
                ':p1' => $values['p1'], ':p2' => $values['p2'], ':p3' => $values['p3'], ':p4' => $values['p4'], ':p5' => $values['p5'], ':p6' => $values['p6'], ':p7' => $values['p7'], ':p8' => $values['p8'], ':p9' => $values['p9'], ':p10' => $values['p10'],
                ':p11' => $values['p11'], ':p12' => $values['p12'], ':p13' => $values['p13'], ':p14' => $values['p14'], ':p15' => $values['p15'], ':p16' => $values['p16'], ':p17' => $values['p17'], ':p18' => $values['p18'], ':p19' => $values['p19'], ':p20' => $values['p20'],
                ':p21' => $values['p21'], ':p22' => $values['p22'], ':p23' => $values['p23'], ':p24' => $values['p24'], ':p25' => $values['p25'], ':p26' => $values['p26'], ':p27' => $values['p27'], ':p28' => $values['p28'], ':p29' => $values['p29'], ':p30' => $values['p30'],
            );
            debug('VALUES : ' .print_r($data, true));
            $stmt = $this->queryPost($query, $data);
            if($stmt){
                $result = $stmt->fetch(PDO::FETCH_ASSOC);
            }else{
                $result = false;
            }
        }catch(Exception $e){
            debug('エラー発生' .$e->getMessage());
            $result = false;
        }

        return $result;
    }
    public function updateLikeHist($values, $twitter_id){
        try{
            $query = 'UPDATE like_hist SET last_id = :l_id, p1 = :p1, p2 = :p2, p3 = :p3, p4 = :p4, p5 = :p5, p6 = :p6, p7 = :p7, p8 = :p8, p9 = :p9, p10 = :p10, p11 = :p11, p12 = :p12, p13 = :p13, p14 = :p14, p15 = :p15, p16 = :p16, p17 = :p17, p18 = :p18, p19 = :p19, p20 = :p20, p21 = :p21, p22 = :p22, p23 = :p23, p24 = :p24, p25 = :p25, p26 = :p26, p27 = :p27, p28 = :p28, p29 = :p29, p30 = :p30 WHERE delete_flg = 0 AND twitter_id = :t_id';
            $data = array( ':t_id' => $twitter_id, ':l_id' => $values['last_id'],
                ':p1' => $values['p1'], ':p2' => $values['p2'], ':p3' => $values['p3'], ':p4' => $values['p4'], ':p5' => $values['p5'], ':p6' => $values['p6'], ':p7' => $values['p7'], ':p8' => $values['p8'], ':p9' => $values['p9'], ':p10' => $values['p10'],
                ':p11' => $values['p11'], ':p12' => $values['p12'], ':p13' => $values['p13'], ':p14' => $values['p14'], ':p15' => $values['p15'], ':p16' => $values['p16'], ':p17' => $values['p17'], ':p18' => $values['p18'], ':p19' => $values['p19'], ':p20' => $values['p20'],
                ':p21' => $values['p21'], ':p22' => $values['p22'], ':p23' => $values['p23'], ':p24' => $values['p24'], ':p25' => $values['p25'], ':p26' => $values['p26'], ':p27' => $values['p27'], ':p28' => $values['p28'], ':p29' => $values['p29'], ':p30' => $values['p30'],
            );
            debug('VALUES : ' .print_r($data, true));
            $stmt = $this->queryPost($query, $data);
            if($stmt){
                $result = $stmt->fetch(PDO::FETCH_ASSOC);
            }else{
                $result = false;
            }
        }catch(Exception $e){
            debug('エラー発生' .$e->getMessage());
            $result = false;
        }

        return $result;
    }
    public function updateFfratioHist($values, $twitter_id){
        try{
            $query = 'UPDATE ffratio_hist SET p1 = :p1, p2 = :p2, p3 = :p3, p4 = :p4, p5 = :p5, p6 = :p6, p7 = :p7, p8 = :p8, p9 = :p9, p10 = :p10, p11 = :p11, p12 = :p12, p13 = :p13, p14 = :p14, p15 = :p15, p16 = :p16, p17 = :p17, p18 = :p18, p19 = :p19, p20 = :p20, p21 = :p21, p22 = :p22, p23 = :p23, p24 = :p24, p25 = :p25, p26 = :p26, p27 = :p27, p28 = :p28, p29 = :p29, p30 = :p30 WHERE delete_flg = 0 AND twitter_id = :t_id';
            $data = array( ':t_id' => $twitter_id,
                ':p1' => $values['p1'], ':p2' => $values['p2'], ':p3' => $values['p3'], ':p4' => $values['p4'], ':p5' => $values['p5'], ':p6' => $values['p6'], ':p7' => $values['p7'], ':p8' => $values['p8'], ':p9' => $values['p9'], ':p10' => $values['p10'],
                ':p11' => $values['p11'], ':p12' => $values['p12'], ':p13' => $values['p13'], ':p14' => $values['p14'], ':p15' => $values['p15'], ':p16' => $values['p16'], ':p17' => $values['p17'], ':p18' => $values['p18'], ':p19' => $values['p19'], ':p20' => $values['p20'],
                ':p21' => $values['p21'], ':p22' => $values['p22'], ':p23' => $values['p23'], ':p24' => $values['p24'], ':p25' => $values['p25'], ':p26' => $values['p26'], ':p27' => $values['p27'], ':p28' => $values['p28'], ':p29' => $values['p29'], ':p30' => $values['p30'],
            );
            debug('VALUES : ' .print_r($data, true));
            $stmt = $this->queryPost($query, $data);
            if($stmt){
                $result = $stmt->fetch(PDO::FETCH_ASSOC);
            }else{
                $result = false;
            }
        }catch(Exception $e){
            debug('エラー発生' .$e->getMessage());
            $result = false;
        }

        return $result;
    }

    ////////////////////////////////
    //退会・復帰処理
    ////////////////////////////////

    //退会処理として各テーブルのdelete_flgをtrueに
    public function deleteFlgToOneAll($twitter_id){
        try{
            //usersテーブル
            $query = 'UPDATE users SET delete_flg = :d_flg WHERE twitter_id = :t_id';
            $data = array(
                ':d_flg' => 1,
                ':t_id' => $twitter_id,
            );
            $stmt_u = $this->queryPost($query, $data);
            //followers_histテーブル
            $query = 'UPDATE followers_hist SET delete_flg = :d_flg WHERE twitter_id = :t_id';
            $stmt = $this->queryPost($query, $data);
            //following_histテーブル
            $query = 'UPDATE following_hist SET delete_flg = :d_flg WHERE twitter_id = :t_id';
            $stmt = $this->queryPost($query, $data);
            //unfollow_histテーブル
            $query = 'UPDATE unfollow_hist SET delete_flg = :d_flg WHERE twitter_id = :t_id';
            $stmt = $this->queryPost($query, $data);
            //unfollowed_histテーブル
            $query = 'UPDATE unfollowed_hist SET delete_flg = :d_flg WHERE twitter_id = :t_id';
            $stmt = $this->queryPost($query, $data);
            //get_like_histテーブル
            $query = 'UPDATE get_like_hist SET delete_flg = :d_flg WHERE twitter_id = :t_id';
            $stmt = $this->queryPost($query, $data);
            //get_retweet_histテーブル
            $query = 'UPDATE get_retweet_hist SET delete_flg = :d_flg WHERE twitter_id = :t_id';
            $stmt = $this->queryPost($query, $data);
            //get_reply_histテーブル
            $query = 'UPDATE get_reply_hist SET delete_flg = :d_flg WHERE twitter_id = :t_id';
            $stmt = $this->queryPost($query, $data);
            //like_histテーブル
            $query = 'UPDATE like_hist SET delete_flg = :d_flg WHERE twitter_id = :t_id';
            $stmt = $this->queryPost($query, $data);
            //ffratio_histテーブル
            $query = 'UPDATE ffratio_hist SET delete_flg = :d_flg WHERE twitter_id = :t_id';
            $stmt = $this->queryPost($query, $data);

            if($stmt_u) $result = $stmt_u->fetch(PDO::FETCH_ASSOC);
        }catch(Exception $e){
            debug('エラー発生' .$e->getMessage());
            $result = false;
        }

        return $result;
    }

    //退会状態からの復帰処理として各テーブルのdelte_flgをfalseに
    public function deleteFlgToZeroAll($twitter_id){
        try{
            //usersテーブル
            $query = 'UPDATE users SET delete_flg = :d_flg WHERE twitter_id = :t_id';
            $data = array(
                ':d_flg' => 0,
                ':t_id' => $twitter_id,
            );
            $stmt_u = $this->queryPost($query, $data);
            //followers_histテーブル
            $query = 'UPDATE followers_hist SET delete_flg = :d_flg WHERE twitter_id = :t_id';
            $stmt = $this->queryPost($query, $data);
            //following_histテーブル
            $query = 'UPDATE following_hist SET delete_flg = :d_flg WHERE twitter_id = :t_id';
            $stmt = $this->queryPost($query, $data);
            //unfollow_histテーブル
            $query = 'UPDATE unfollow_hist SET delete_flg = :d_flg WHERE twitter_id = :t_id';
            $stmt = $this->queryPost($query, $data);
            //unfollowed_histテーブル
            $query = 'UPDATE unfollowed_hist SET delete_flg = :d_flg WHERE twitter_id = :t_id';
            $stmt = $this->queryPost($query, $data);
            //get_like_histテーブル
            $query = 'UPDATE get_like_hist SET delete_flg = :d_flg WHERE twitter_id = :t_id';
            $stmt = $this->queryPost($query, $data);
            //get_retweet_histテーブル
            $query = 'UPDATE get_retweet_hist SET delete_flg = :d_flg WHERE twitter_id = :t_id';
            $stmt = $this->queryPost($query, $data);
            //get_reply_histテーブル
            $query = 'UPDATE get_reply_hist SET delete_flg = :d_flg WHERE twitter_id = :t_id';
            $stmt = $this->queryPost($query, $data);
            //like_histテーブル
            $query = 'UPDATE like_hist SET delete_flg = :d_flg WHERE twitter_id = :t_id';
            $stmt = $this->queryPost($query, $data);
            //ffratio_histテーブル
            $query = 'UPDATE ffratio_hist SET delete_flg = :d_flg WHERE twitter_id = :t_id';
            $stmt = $this->queryPost($query, $data);

            if($stmt_u) $result = $stmt_u->fetch(PDO::FETCH_ASSOC);
        }catch(Exception $e){
            debug('エラー発生' .$e->getMessage());
            $result = false;
        }

        return $result;
    }

}