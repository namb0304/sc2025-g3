<?php
require_once 'helpers.php';

$post_id = $_GET['id'] ?? '';
$current_user_id = $_SESSION['user_id'] ?? null;
$is_logged_in = is_logged_in();

// --- いいね・コメントなどのPOSTリクエスト処理 ---
if ($is_logged_in && $_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        $like_type = ($_POST['action'] === 'like') ? 1 : -1;
        toggle_like($post_id, $current_user_id, $like_type);
    }
    if (isset($_POST['comment']) && !empty(trim($_POST['comment']))) {
        add_comment($post_id, $current_user_id, trim($_POST['comment']));
    }
    // 処理後に同じページにリダイレクト（フォームの再送信防止）
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
$user_like_status = $is_logged_in ? get_like_status($post_id, $current_user_id) : 0;

include 'templates/header.php';
?>
<style>
    .post-detail-container {
        max-width: 800px;
        margin: 0 auto;
    }
    .post-full {
        background: #fff;
        padding: 2.5rem;
        border-radius: 12px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.08);
    }
    .post-main-image-wrapper {
        width: 100%;
        max-width: 600px;
        margin: 0 auto 1.5rem auto;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    }
    .post-main-image-wrapper img {
        width: 100%;
        height: auto;
        display: block;
    }
    .post-full h2 { margin-top: 0; }
    .post-description { font-size: 1.1rem; line-height: 1.8; white-space: pre-wrap; }
    .post-meta { color: #777; margin-bottom: 2rem; }
    .tag { background-color: #e9ecef; color: #495057; padding: 4px 10px; border-radius: 15px; font-size: 0.9em; display: inline-block; margin-right: 5px; margin-bottom: 5px;}

    /* ▼▼▼ いいね・コメント機能のデザイン ▼▼▼ */
    .post-actions { margin-top: 20px; }
    .post-actions form { display: flex; gap: 10px; }
    .action-btn { background: #f0f2f5; border: 1px solid #ddd; color: #555; padding: 10px 20px; border-radius: 20px; cursor: pointer; font-weight: 600; display: inline-flex; align-items: center; gap: 8px; transition: all 0.2s ease; }
    .action-btn:hover { background: #e4e6eb; }
    .action-btn.active.like { background-color: #007bff; color: white; border-color: #007bff; }
    .action-btn.active.dislike { background-color: #dc3545; color: white; border-color: #dc3545; }
    
    .comments-section { background: #fff; padding: 2.5rem; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.08); margin-top: 2rem; }
    .comments-section h3 { margin-top: 0; }
    .comments-section textarea { width: 100%; padding: 10px; margin-bottom: 10px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box; min-height: 100px; resize: vertical; }
    .comments-section .submit-btn { background-color: #007bff; color: #fff; border: none; padding: 10px 20px; cursor: pointer; border-radius: 4px; font-weight: 600; }
    .comment-list { list-style: none; padding: 0; margin-top: 2rem; }
    .comment-item { border-bottom: 1px solid #e9ecef; padding: 1.5rem 0; }
    .comment-item:last-child { border-bottom: none; }
    .comment-body { margin: 0.5rem 0; }
    .comment-footer { display: flex; justify-content: space-between; align-items: center; font-size: 0.85em; color: #6c757d; }
    .delete-button-small { background: none; border: none; color: #dc3545; cursor: pointer; font-size: 1em; padding: 0; }
    /* ▲▲▲ デザインここまで ▲▲▲ */
</style>

<div class="post-detail-container">
    <div class="post-full">
        <h2><?= htmlspecialchars($current_post['title']) ?></h2>
        <p class="post-meta">投稿者: <?= htmlspecialchars($current_post['username']) ?>・<?= date('Y/m/d H:i', strtotime($current_post['created_at'])) ?></p>
        
        <?php if (!empty($current_post['closet_item_id'])): ?>
            <div class="post-main-image-wrapper">
                <img src="image.php?type=closet&id=<?= htmlspecialchars($current_post['closet_item_id']) ?>" alt="<?= htmlspecialchars($current_post['title']) ?>">
            </div>
        <?php endif; ?>

        <div class="post-description"><?= nl2br(htmlspecialchars($current_post['description'])) ?></div>
        
        <hr class="divider">
        
        <?php if ($closet_item): ?>
            <h3><i class="fas fa-tshirt"></i> コーディネートアイテム</h3>
            <div class="item-detail-container-small">
                <div class="item-info-view-small">
                    <div class="info-group">
                        <h4>種類</h4>
                        <div class="category-tags">
                            <?php 
                            $categories = json_decode($closet_item['category'], true) ?? [];
                            foreach($categories as $cat) { echo '<span class="tag">' . htmlspecialchars($cat) . '</span>'; }
                            ?>
                        </div>
                    </div>
                     <div class="info-group">
                        <h4>ジャンル</h4>
                        <div class="genre-tags">
                            <?php 
                            foreach($closet_item['genres'] as $genre) { echo '<span class="tag">' . htmlspecialchars($genre) . '</span>'; }
                            ?>
                        </div>
                    </div>
                    <div class="info-group">
                        <h4>備考</h4>
                        <p><?= nl2br(htmlspecialchars($closet_item['notes'] ?: 'なし')) ?></p>
                    </div>
                </div>
            </div>
        <?php endif; ?>
        
        <div class="post-actions">
            <?php if ($is_logged_in): ?>
                <form action="<?= BASE_URL ?>/post_detail.php?id=<?= $post_id ?>" method="post">
                    <button type="submit" name="action" value="like" class="action-btn like <?= $user_like_status == 1 ? 'active' : '' ?>">
                        <i class="fas fa-thumbs-up"></i> いいね (<?= $like_counts['likes'] ?>)
                    </button>
                    <button type="submit" name="action" value="dislike" class="action-btn dislike <?= $user_like_status == -1 ? 'active' : '' ?>">
                        <i class="fas fa-thumbs-down"></i> 低評価 (<?= $like_counts['dislikes'] ?>)
                    </button>
                </form>
            <?php endif; ?>
        </div>
        </div>

    <div class="comments-section">
        <h3><i class="fas fa-comments"></i> コメント</h3>
        <?php if ($is_logged_in): ?>
            <form action="<?= BASE_URL ?>/post_detail.php?id=<?= $post_id ?>" method="post">
                <textarea name="comment" placeholder="素敵なコメントを残しましょう" required></textarea>
                <button type="submit" class="submit-btn">コメントする</button>
            </form>
        <?php else: ?>
            <p><a href="<?= BASE_URL ?>/login.php">ログイン</a>してコメントに参加しませんか？</p>
        <?php endif; ?>

        <ul class="comment-list">
            <?php if (empty($comments)): ?>
                <li class="comment-item"><p>まだコメントはありません。最初のコメントを投稿しよう！</p></li>
            <?php else: ?>
                <?php foreach ($comments as $comment): ?>
                    <li class="comment-item">
                        <p><strong><?= htmlspecialchars($comment['username']) ?>:</strong></p>
                        <p class="comment-body"><?= nl2br(htmlspecialchars($comment['text'])) ?></p>
                        <div class="comment-footer">
                            <span><?= date('Y/m/d H:i', strtotime($comment['created_at'])) ?></span>
                            <?php if ($is_logged_in && $current_user_id == $comment['user_id']): ?>
                                <form action="<?= BASE_URL ?>/delete_handler.php" method="post" onsubmit="return confirm('このコメントを削除しますか？');" style="margin:0;">
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