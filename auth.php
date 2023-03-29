<?php 

if(empty($_SESSION['login_date'])){
    debug('AUTH : 未ログインユーザー');
    debug(print_r($_SESSION, true));
    if(basename($_SERVER['PHP_SELF']) !== 'login.php'){
        header('Location:login.php');
    }
}else{
    if($_SESSION['login_date'] + $_SESSION['login_limit'] < time()){
        debug('AUTH : ログインリミット超過ユーザー');
        session_destroy();
        header('Location:login.php');
    }else{
        debug('AUTH : ログイン済みユーザー');
        $_SESSION['login_date'] = time();
        if(basename($_SERVER['PHP_SELF']) === 'login.php'){
            header('Location:index.php');
        }
        
    }
}