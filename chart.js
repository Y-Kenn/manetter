$(function(){
    google.charts.load('current', {'packages':['corechart']});
    google.charts.setOnLoadCallback(drawChart);

    function drawChart() {

        var axis_names = {
            followers_hist : ['新規フォロワー', '日'],
            following_hist : ['新規フォロー', '日'],
            unfollowed_hist : ['アンフォローされた', '日'],
            unfollow : ['アンフォローした', '日'],
            get_like_hist : ['いいねされた', '日'],
            like_hist : ['いいねした', '日'],
            get_retweet_hist : ['リツイートされた', '日'],
            ffratio_hist : ['FF日', '日']
        }

        var hist_data = <? php echo htmlspecialchars($hist, ENT_QUOTES, 'UTF-8'); ?>;
        
        //新規フォロワーのデータ
        var data_followers_hist = [];
        data_followers_hist.push(['日', '新規フォロワー']);
        for(var i = 1; i <= 30; i++ ){
            data_followers_hist.push([i, data['p' + String(i)]]);
        }
        //新規フォローのデータ
        var data_following_hist = [];
        data_following_hist.push(['日', '新規フォロー']);
        for(var i = 1; i <= 30; i++ ){
            data_following_hist.push([i, data['p' + String(i)]]);
        }
        //新規アンフォローされたのデータ
        var data_unfollowed_hist = [];
        data_unfollowed_hist.push(['日', '新規アンフォローされた']);
        for(var i = 1; i <= 30; i++ ){
            data_unfollowed_hist.push([i, data['p' + String(i)]]);
        }
        //新規アンフォローしたのデータ
        var data_unfollow_hist = [];
        data_unfollow_hist.push(['日', '新規アンフォローした']);
        for(var i = 1; i <= 30; i++ ){
            data_unfollow_hist.push([i, data['p' + String(i)]]);
        }
        //いいねされたのデータ
        var data_get_like_hist = [];
        data_get_like_hist.push(['日', 'いいねされた']);
        for(var i = 1; i <= 30; i++ ){
            data_get_like_hist.push([i, data['p' + String(i)]]);
        }
        //いいねしたのデータ
        var data_like_hist = [];
        data_like_hist.push(['日', 'いいねした']);
        for(var i = 1; i <= 30; i++ ){
            data_like_hist.push([i, data['p' + String(i)]]);
        }
        //リツイートされたのデータ
        var data_get_retweet_hist = [];
        data_get_retweet_hist.push(['日', 'リツイートされた']);
        for(var i = 1; i <= 30; i++ ){
            ddata_get_retweet_hist.push([i, data['p' + String(i)]]);
        }
        //FF比のデータ
        var data_ffratio_hist = [];
        data_ffratio_hist.push(['日', 'FF比のデータ']);
        for(var i = 1; i <= 30; i++ ){
            data_ffratio_hist.push([i, data['p' + String(i)]]);
        }


        // ①グラフにしたいデータを設置
        var data = google.visualization.arrayToDataTable([
            ['輸入相手国', '輸入総額（億円）'],
            ['中国', 175077],
            ['米国', 74536],
            ['オーストラリア', 38313],
            ['台湾', 28629],
            ['韓国', 28416],
            ['タイ', 25401],
            ['ベトナム', 23551],
            ['ドイツ', 22763],
            ['サウジアラビア', 19696],
            ['アラブ首長国連邦', 17502]
        ]);

        // ②グラフのオプションを指定
        var options = {
            title: '2020年輸入相手国トップ１０',
            width: 400,
            height: 300
        };

        // ③グラフの種類とグラフを設置する場所を指定
        var chart = new google.visualization.PieChart(document.getElementById('chart_div'));
        chart.draw(data_followers_hist, options);
    }
});
