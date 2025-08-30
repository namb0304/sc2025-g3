<?php
require_once 'helpers.php';

$current_user_id = $_SESSION['user_id'] ?? null;

// --- いいね・低評価のPOSTリクエスト処理 ---
if (is_logged_in() && $_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $post_id = $_POST['post_id'] ?? '';
    if ($post_id) {
        $like_type = ($_POST['action'] === 'like') ? 1 : -1;
        toggle_like($post_id, $current_user_id, $like_type);
    }
    // 処理後に同じページにリダイレクトして、フォームの再送信を防ぐ
    header('Location: ' . BASE_URL . '/index.php');
    exit;
}

// --- ページの表示処理 ---
$search_query = $_GET['search'] ?? '';
$posts = get_all_posts($search_query);

include 'templates/header.php';
?>
<div class="container">
    <h2>みんなのコーディネート</h2>
    <form action="index.php" method="get">
        <input type="text" name="search" placeholder="タイトルやユーザー名で検索" value="<?= htmlspecialchars($search_query) ?>">
        <button type="submit">検索</button>
    </form>

    <div class="post-grid">
        <?php if (empty($posts)): ?>
            <p>投稿が見つかりませんでした。</p>
        <?php else: ?>
            <?php foreach ($posts as $post): ?>
                <div class="post-card">
                    <a href="post_detail.php?id=<?= $post['id'] ?>">
                        <?php if (!empty($post['closet_item_id'])): ?>
                            <img src="image.php?id=<?= htmlspecialchars($post['closet_item_id']) ?>" alt="<?= htmlspecialchars($post['title']) ?>">
                        <?php endif; ?>
                        <div class="post-info">
                            <h3><?= htmlspecialchars($post['title']) ?></h3>
                            <p>by <?= htmlspecialchars($post['username']) ?></p>
                        </div>
                    </a>
                    
                    <?php // ▼▼▼ いいね・低評価ボタンを追加 ▼▼▼ ?>
                    <?php if (is_logged_in()): ?>
                        <div class="post-actions index-actions">
                            <form action="<?= BASE_URL ?>/index.php" method="post">
                                <input type="hidden" name="post_id" value="<?= $post['id'] ?>">
                                <button type="submit" name="action" value="like">
                                    👍 (<?= $post['likes_count'] ?>)
                                </button>
                                <button type="submit" name="action" value="dislike">
                                    👎 (<?= $post['dislikes_count'] ?>)
                                </button>
                            </form>
                        </div>
                    <?php endif; ?>
                    <?php // ▲▲▲ 追加ここまで ▲▲▲ ?>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>
<?php include 'templates/footer.php'; ?>