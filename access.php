<?php
require_once('func_common.php');
try{
    //開発環境用
    require __DIR__ . '/vendor/autoload.php';
    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
    $dotenv->load();
}catch(Exception $e){
    //本番環境用//何もしない
}

if($_ENV['API_KEY']){
    //開発環境用
    $key = $_ENV['API_KEY'];
    $secret = $_ENV['API_SECRET'];
    $bearer = $_ENV['BEARER'];
    $dsn = $_ENV['DSN'];
    $user = $_ENV['RDB_USER'];
    $password = $_ENV['RDB_PASSWORD'];
    $client_id = $_ENV['CLIENT_ID'];
    $client_secret = $_ENV['CLIENT_SECRET'];
    $redirect_uri = $_ENV['REDIRECT_URI'];
}else{
    //本番環境用
    $key = getenv('API_KEY');
    debug('KEY : ' .print_r($key, true));
    $secret = getenv('API_SECRET');
    $bearer = getenv('BEARER');
    $dsn = getenv('DSN');
    $user = getenv('RDB_USER');
    $password = getenv('RDB_PASSWORD');
    $client_id = getenv('CLIENT_ID');
    $client_secret = getenv('CLIENT_SECRET');
    $redirect_uri = getenv('REDIRECT_URI');
}
    




