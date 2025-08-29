<?php
// sc2025-g3/src/templates/header.php

// このファイル自体はヘルパーを必要としませんが、
// 呼び出し元のPHPで先にヘルパーが読み込まれている想定です。
// 安全のためにここでも読み込むようにしてもOKです。
// require_once __DIR__ . '/../helpers.php'; 
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>AI Fashion Coordinator</title>
    <link rel="stylesheet" href="/style.css"> 
</head>
<body>
    <header>
        <h1><a href="/">AI Fashion Coordinator</a></h1>
        <nav>
            <a href="/">ホーム</a>
            <?php if (is_logged_in()): ?>
                <a href="/post/create.php">投稿する</a>
                <a href="/mypage/closet.php">クローゼット</a>
                <a href="/mypage/mypage.php">マイページ</a>
                <a href="/auth/logout.php">ログアウト</a>
            <?php else: ?>
                <a href="/auth/login.php">ログイン</a>
                <a href="/auth/register.php">新規登録</a>
            <?php endif; ?>
        </nav>
    </header>
    <main>