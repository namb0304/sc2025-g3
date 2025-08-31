<?php 
require_once __DIR__ . '/../helpers.php'; 
$is_logged_in = is_logged_in();
if ($is_logged_in) {
    $current_user = get_user_by_id($_SESSION['user_id']);
}

// 現在のページのファイル名を取得
$current_page = basename($_SERVER['PHP_SELF']);

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
    <header class="site-header">
        <div class="header-container">
            <div class="header-left">
                <div class="logo">ファッション共有サイト</div>
            </div>
            
            <nav class="header-center">
                <?php // 'active' を 'nav-item--active' に変更 ?>
                <a href="<?= BASE_URL ?>/" class="nav-item <?= ($current_page === 'index.php') ? 'nav-item--active' : '' ?>">ホーム</a>
                <?php if ($is_logged_in): ?>
                    <?php // 'active' を 'nav-item--active' に変更 ?>
                    <a href="<?= BASE_URL ?>/post.php" class="nav-item <?= ($current_page === 'post.php' || $current_page === 'post_from_closet.php' || $current_page === 'post_new_item.php') ? 'nav-item--active' : '' ?>">投稿</a>
                    <a href="<?= BASE_URL ?>/mypage.php" class="nav-item <?= ($current_page === 'mypage.php') ? 'nav-item--active' : '' ?>">マイページ</a>
                    <a href="<?= BASE_URL ?>/general.html" class="nav-item">ログアウト</a>
                <?php else: ?>
                    <a href="<?= BASE_URL ?>/login.php" class="nav-item <?= ($current_page === 'login.php') ? 'nav-item--active' : '' ?>">ログイン</a>
                    <a href="<?= BASE_URL ?>/register.php" class="nav-item <?= ($current_page === 'register.php') ? 'nav-item--active' : '' ?>">新規登録</a>
                <?php endif; ?>
            </nav>
            
            <div class="header-right">
                <?php if ($is_logged_in): ?>
                    <div class="user-welcome">
                        <i class="fas fa-user-circle"></i>
                        <span><?= htmlspecialchars($current_user['username']) ?> さん</span>
                    </div>
                <?php else: ?>
                    <div class="placeholder"></div>
                <?php endif; ?>
            </div>
        </div>
    </header>
    <main class="container">