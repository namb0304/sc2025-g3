<?php
require_once 'helpers.php';

$post_id = $_GET['id'] ?? '';
$current_user_id = $_SESSION['user_id'] ?? null;

// --- いいね・コメントなどのPOSTリクエスト処理 ---
if (is_logged_in() && $_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        $like_type = ($_POST['action'] === 'like') ? 1 : -1;
        toggle_like($post_id, $current_user_id, $like_type);
    }
    if (isset($_POST['comment']) && !empty(trim($_POST['comment']))) {
        add_comment($post_id, $current_user_id, trim($_POST['comment']));
    }
    header('Location: ' . BASE_URL . '/post_detail.php?id=' . $post_id);
    exit;
}

// --- ページの表示処理 ---
$current_post = get_post_by_id($post_id);
if (!$current_post) {
    die('投稿が見つかりません。');
}

$closet_item = null;
if (!empty($current_post['closet_item_id'])) {
    $closet_item = find_closet_item_by_id($current_post['closet_item_id']);
}

$comments = get_comments_by_post_id($post_id);
$like_counts = get_like_counts($post_id);
$user_like_status = is_logged_in() ? get_like_status($post_id, $current_user_id) : 0;

include 'templates/header.php';
?>
<div class="container">
    <div class="post-full">
        <h2><?= htmlspecialchars($current_post['title']) ?></h2>

        <?php if (!empty($current_post['closet_item_id'])): ?>
            <img src="image.php?id=<?= htmlspecialchars($current_post['closet_item_id']) ?>" alt="<?= htmlspecialchars($current_post['title']) ?>" style="max-width: 100%; border-radius: 8px; margin-bottom: 20px;">
        <?php endif; ?>

        <p class="post-description"><?= nl2br(htmlspecialchars($current_post['description'])) ?></p>
        <p class="post-meta">投稿者: <?= htmlspecialchars($current_post['username']) ?></p>

        <?php if ($closet_item): ?>
            <hr class="divider">
            <h3>コーディネートアイテム</h3>
            <div class="item-detail-container-small">
                <div class="item-image-view-small">
                    <img src="image.php?id=<?= htmlspecialchars($closet_item['id']) ?>" alt="アイテム画像">
                </div>
                <div class="item-info-view-small">
                    <div class="info-group">
                        <h4>種類</h4>
                        <p><?= htmlspecialchars($closet_item['category'] ?? '未分類') ?></p>
                    </div>
                    <div class="info-group">
                        <h4>備考</h4>
                        <p><?= nl2br(htmlspecialchars($closet_item['notes'] ?: 'なし')) ?></p>
                    </div>
                </div>
            </div>
        <?php endif; ?>
        
        <div class="post-actions">
            <form action="<?= BASE_URL ?>/post_detail.php?id=<?= $post_id ?>" method="post">
                <button type="submit" name="action" value="like" class="<?= $user_like_status == 1 ? 'active' : '' ?>">
                    👍 いいね (<?= $like_counts['likes'] ?>)
                </button>
                <button type="submit" name="action" value="dislike" class="<?= $user_like_status == -1 ? 'active' : '' ?>">
                    👎 低評価 (<?= $like_counts['dislikes'] ?>)
                </button>
            </form>
        </div>
    </div>
    <hr>
    <div class="comments-section">
        <h3>コメント</h3>
        <?php if (is_logged_in()): ?>
            <form action="<?= BASE_URL ?>/post_detail.php?id=<?= $post_id ?>" method="post">
                <textarea name="comment" placeholder="素敵なコメントを残しましょう" required></textarea>
                <button type="submit">コメントする</button>
            </form>
        <?php else: ?>
            <p><a href="<?= BASE_URL ?>/login.php">ログイン</a>してコメントに参加しませんか？</p>
        <?php endif; ?>

        <ul>
            <?php if (empty($comments)): ?>
                <li><p>まだコメントはありません。</p></li>
            <?php else: ?>
                <?php foreach ($comments as $comment): ?>
                    <li>
                        <p><strong><?= htmlspecialchars($comment['username']) ?>:</strong></p>
                        <p><?= nl2br(htmlspecialchars($comment['text'])) ?></p>
                        <div class="comment-footer">
                            <span><?= date('Y/m/d H:i', strtotime($comment['created_at'])) ?></span>
                            <?php if (is_logged_in() && $current_user_id == $comment['user_id']): ?>
                                <form action="<?= BASE_URL ?>/delete_handler.php" method="post" onsubmit="return confirm('このコメントを削除しますか？');">
                                    <input type="hidden" name="type" value="comment">
                                    <input type="hidden" name="post_id" value="<?= $post_id ?>">
                                    <input type="hidden" name="comment_id" value="<?= $comment['id'] ?>">
                                    <button type="submit" class="delete-button-small">削除</button>
                                </form>
                            <?php endif; ?>
                        </div>
                    </li>
                <?php endforeach; ?>
            <?php endif; ?>
        </ul>
    </div>
</div>
<?php include 'templates/footer.php'; ?>