<?php 
require_once __DIR__ . '/../helpers.php'; 
$is_logged_in = is_logged_in();
if ($is_logged_in) {
    // ログイン中のユーザー情報を取得
    $current_user = get_user_by_id($_SESSION['user_id']);
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>ファッション共有サイト</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/style.css">
</head>
<body>
    <header>
        <div class="header-container">
            <h1><a href="<?= BASE_URL ?>/">ファッション共有サイト</a></h1>
            <nav>
                <a href="<?= BASE_URL ?>/">ホーム</a>
                <?php if ($is_logged_in): ?>
                    <a href="<?= BASE_URL ?>/post.php">投稿</a>
                    <a href="<?= BASE_URL ?>/mypage.php">マイページ</a>
                    <a href="<?= BASE_URL ?>/logout.php">ログアウト</a>
                    
                    <div class="nav-user-info">
                        <?= htmlspecialchars($current_user['username']) ?> さん
                    </div>
                <?php else: ?>
                    <a href="<?= BASE_URL ?>/login.php">ログイン</a>
                    <a href="<?= BASE_URL ?>/register.php">新規登録</a>
                <?php endif; ?>
            </nav>
        </div>
    </header>
    <main class="container">