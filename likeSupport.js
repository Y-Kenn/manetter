$(function(){

    const ERR_429 = 'いいねの回数が上限に達しました。15分ほど時間を空けて再開してください。'
    const ERR_1001 = 'ログインの有効期限が切れています。再ログインしてください。'
    const ERR_1002 = 'sessionがありません。'
    const ERR_2001 = 'ツイートの取得数は１〜１０００の数字を入力してください'
    //セレクトボックス
    //URLからセレクトボックス取得

    //スライドバー
    var $slidebar = $('#js-slidebar_like'),
        $list_ratio = $('#js-list_ratio'),
        $recomend_ratio = $('#js-recomend_ratio');
        var slidebar_val = $slidebar.val()
        $list_ratio.html(slidebar_val);
        $recomend_ratio.html(100 - slidebar_val);
    $slidebar.on('mousemove', function(e){
        slidebar_val = Math.round($slidebar.val() / 10) * 10;
        $list_ratio.html(slidebar_val);
        $recomend_ratio.html(100 - slidebar_val);
    });
    $slidebar.on('click', function(e){
        slidebar_val = Math.round($slidebar.val() / 10) * 10;
        $list_ratio.html(slidebar_val);
        $recomend_ratio.html(100 - slidebar_val);
    });

    //セレクトボックス・ナンバーボックス
    var $selectbox = $('#js-selectbox_list');
    var $numberbox = $('#js-numberbox_tweets');
    var $submit = $('#js-submit');
    if($selectbox.val() && $numberbox.val() >= 1 && $numberbox.val() <= 1000){
        $submit.prop('disabled', false);
    }
    $selectbox.on('change', function(){
        if($selectbox.val() && $numberbox.val() >= 1 && $numberbox.val() <= 1000){
            $submit.prop('disabled', false);
        }
    });
    $numberbox.on('keyup',function(){
        if($selectbox.val() && $numberbox.val() >= 1 && $numberbox.val() <= 1000){
            $submit.prop('disabled', false);
        }else{
            $submit.prop('disabled', true);
        }
    });
    $numberbox.on('change', function(){
        if($numberbox.val()< 1 || $numberbox.val() > 1000){
            alert(ERR_2001);
        }
    });
    
    
    //ツイート取得ボタン
    $('#js-submit').on('click', function(data){
        const agent = window.navigator.userAgent.toLowerCase()

        if(agent.indexOf("chrome") != -1){ 
            $(this).html('<i class="fa fa-spinner fa-spin-pulse"></i> 取得中...').css("pointer-events", "none");
        }else{
            $(this).html('<i class="fa-solid fa-arrows-spin"></i> 取得中...').css("pointer-events", "none");
        }

        
    });

    //いいねボタンクリック時
    $('.js-button--like').on('click', function(e){
        console.log($(this).val());

        var $that = $(this);
        $.ajax({
            type : 'post',
            url : 'ajax_postLike.php',
            dataType : 'json',
            data : {
                like_id : $(this).val()
            }
        }).done(function(result){
            console.log(result);
            if('data' in result){
                $that.addClass('c-button--pushed').removeClass('c-button--like').html('').append('<i class="fa-solid fa-check"></i>');
            }else if('status' in result){
                if(result['status'] === 429){
                    alert(ERR_429);
                }else if(result['status'] === 1001){
                    alert(ERR_1001);
                }else if(result['status'] === 1002){
                    alert(ERR_1002);
                }

            }
        }).fail(function(jqXHR, textStatus, errorThrown){
            console.log("ajax通信に失敗しました");
            console.log("jqXHR          : " + jqXHR.status); // HTTPステータスが取得
            console.log("textStatus     : " + textStatus);    // タイムアウト、パースエラー
            console.log("errorThrown    : " + errorThrown.message); // 例外情報
        });
        
       //
    });

});