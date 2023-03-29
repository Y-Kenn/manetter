<?php 

require_once('func_common.php');
debug('[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[');
debug('- LOGOUT -');
debug('[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[');

session_destroy();
debug('SESSION : ' .print_r($_SESSION, true));
header('Location:login.php');

?>