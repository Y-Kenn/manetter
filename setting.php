<?php 
require_once('access.php');
require_once('func_common.php');
require_once('Manetter.php');
debug('[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[');
debug('- SETTING -');
debug('[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[');

require_once('auth.php');

$twitter_id = $_SESSION['twitter_id'];

$Manetter = new Manetter($key, $secret, $bearer, $dsn, $user, $password, $client_id, $client_secret, $redirect_uri);
$token = $Manetter->TwitterDB->getRegisteredToken($twitter_id);
$Manetter->setTokenToHeader($token['access_token']);

$my_info = $Manetter->getMyInfo();

?>
<?php
$site_title = '設定/アカウント - Manetter';
require_once('head.php');
?>

    <body>
        <div class="l-container--2col__fluid">
            <?php require_once('header.php'); ?>
            <div class="l-container--2col__main">
                <div class="c-page">
                    <h2 class="c-page__title"><i class="fa-solid fa-gear"></i><span class="u-margin--l--5">設定/アカウント</span></h2>
                    <h3 class="c-page__sub_title u-margin--t--20">■ 退会する</h3>
                    <a href="withdraw.php" class="c-page__sub_title u-margin--l--20 u-font_color--mainBlue">    - 退会ははこちら</a>
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
                $(".js-nav__setting").addClass("js-nav--active");
            });
        </script>
        <script src="app.js"></script>
    </body>
</html>
