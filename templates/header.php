<?php require_once __DIR__ . '/../helpers.php'; ?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>AI Fashion Coordinator</title>
    <link rel="stylesheet" href="<?= BASE_URL ?>/style.css">
</head>
<body>
    <header>
        <h1><a href="<?= BASE_URL ?>/">AI Fashion Coordinator</a></h1>
        <nav>
            <a href="<?= BASE_URL ?>/">ホーム</a>
            <?php if (is_logged_in()): ?>
                <a href="<?= BASE_URL ?>/post.php">投稿する</a>
                <a href="<?= BASE_URL ?>/closet.php">クローゼット</a>
                <a href="<?= BASE_URL ?>/mypage.php">マイページ</a>
                <a href="<?= BASE_URL ?>/logout.php">ログアウト</a>
            <?php else: ?>
                <a href="<?= BASE_URL ?>/login.php">ログイン</a>
                <a href="<?= BASE_URL ?>/register.php">新規登録</a>
            <?php endif; ?>
        </nav>
    </header>
    <main>
