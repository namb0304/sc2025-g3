<?php
// helpers.php を読み込んで BASE_URL を使えるようにする
require_once 'helpers.php';

// セッションを開始しないとセッション関連の操作ができない
session_start();

// セッション変数を全て空の配列で上書き
$_SESSION = array();

// セッションを完全に破壊
session_destroy();

// ログアウト後のリダイレクト先を指定
header('Location: ' . BASE_URL . '/general.html');
exit; // リダイレクト後にスクリプトの実行を確実に停止
?>