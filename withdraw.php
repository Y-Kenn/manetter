<?php 
require_once('access.php');
require_once('func_common.php');
require_once('Manetter.php');
debug('[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[');
debug('- WITHDRAW -');
debug('[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[');

require_once('auth.php');

$twitter_id = $_SESSION['twitter_id'];

$Manetter = new Manetter($key, $secret, $bearer, $dsn, $user, $password, $client_id, $client_secret, $redirect_uri);
debug('POST : ' .print_r($_POST, true));
if($_POST['submit'] === '退会'){
    //退会処理として各レコードのdelete_flgをtrueにし、データの削除はしない
    $Manetter->TwitterDB->deleteFlgToOneAll($twitter_id);
    session_destroy();
    header('Location:login.php');
}

?>

<?php 
$site_title = '退会 - Manetter';
require_once('head.php'); 
?>

<body>
    <?php require_once('frontHeader.php') ?>
    
    <section id="main" class="l-container__1col">
        <div class="p-withdraw">
            <h1 class="p-withdraw__title"><span class="font_weight--600">M<span class="u-font_color--mainPink">a</span>n<span class="u-font_color--mainGreen">e</span>t<span class="u-font_color--mainBlue">t</span>er</span>を退会する</h1>
            <form action="" method="post" class="p-withdraw__form">
                <div class="p-withdraw__form__textbox">
                    <div class="p-withdraw__form__text">ボタンを押すと退会が確定します。</div>
                    <div class="p-withdraw__form__text">アカウントの情報は一定期間保存されます。</div>
                </div>
                
                <a href="setting.php" class="p-withdraw__button p-withdraw__button__cancel">キャンセル</a>
                <input type="submit" name="submit" value="退会" class="p-withdraw__button p-withdraw__button__withdraw">
            </form>
            
            
        </div>
        
    </section>
    <?php require_once('footer.php') ?>
</body>