<?php require_once __DIR__ . '/../helpers.php'; ?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>AI Fashion Coordinator</title>
    
    <link rel="stylesheet" href="style.css">

</head>
<body>
    <header>
        <h1><a href="/">AI Fashion Coordinator</a></h1>
        <nav>
            <a href="/">ホーム</a>
            <?php if (is_logged_in()): ?>
                <a href="/post.php">投稿する</a>
                <a href="/closet.php">クローゼット</a>
                <a href="/mypage.php">マイページ</a>
                <a href="/logout.php">ログアウト</a>
            <?php else: ?>
                <a href="/login.php">ログイン</a>
                <a href="/register.php">新規登録</a>
            <?php endif; ?>
        </nav>
    </header>
    <main>
