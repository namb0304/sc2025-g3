<?php
session_start();
$_SESSION = array(); // セッション変数を空にする
session_destroy(); // セッションを破壊する
header("Location: /auth/login.php");
exit;
?>
