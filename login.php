<?php 
require_once('access.php');
require_once('func_common.php');
require_once('Manetter.php');
debug('[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[');
debug('- LOGIN -');
debug('[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[');

require_once('auth.php');

$Manetter = new Manetter($key, $secret, $bearer, $dsn, $user, $password, $client_id, $client_secret, $redirect_uri);

?>

<?php 
$site_title = 'ログイン/登録 - Manetter';
require_once('head.php'); 
?>

<body>
    <?php require_once('frontHeader.php') ?>
    
    <section id="main" class="l-container__1col">
        <div class="p-sign">
            <h1 class="p-sign__title">ログイン・登録</h1>
            <div class="p-sign__form">
                <h2 class="p-sign__form__title">SNSでログイン・登録</h2>
                <a href=<?php echo  $Manetter->makeAuthorizeUrl(); ?> class="p-sign__twitter">
                    <i class="fa-brands fa-twitter p-sign__twitter__icon"></i>
                    <span>Twitter</span>
                </a>

            </div>
        </div>
        
    </section>
    <?php require_once('footer.php') ?>
</body>