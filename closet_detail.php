<?php
require_once 'helpers.php';
login_check();

$item_id = $_GET['id'] ?? '';
$current_item = find_closet_item_by_id($item_id, $_SESSION['user_id']);

if (!$current_item) {
    die('アイテムが見つからないか、アクセス権がありません。');
}

// PostgreSQLの配列 {a,b,c} をPHPの配列に変換
$genres = [];
if (!empty($current_item['genres'])) {
    $genres = explode(',', trim($current_item['genres'], '{}'));
}
?>
<?php include 'templates/header.php'; ?>
<div class="container">
    <div class="item-detail-container">
        <div class="item-image-view">
            <img src="<?= BASE_URL ?>/<?= htmlspecialchars($current_item['image_path']) ?>" alt="クローゼットアイテム">
        </div>
        <div class="item-info-view">
            <h2>アイテム情報</h2>
            <div class="info-group">
                <h3>種類</h3>
                <p><?= htmlspecialchars($current_item['category'] ?? '未分類') ?></p>
            </div>
            <div class="info-group">
                <h3>ジャンル</h3>
                <?php if (!empty($genres)): ?>
                    <div class="genre-tags">
                        <?php foreach($genres as $genre): ?>
                            <span class="tag"><?= htmlspecialchars($genre) ?></span>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <p>ジャンル未登録</p>
                <?php endif; ?>
            </div>
            <div class="info-group">
                <h3>備考</h3>
                <p><?= nl2br(htmlspecialchars($current_item['notes'] ?: '備考はありません。')) ?></p>
            </div>
            <a href="<?= BASE_URL ?>/closet.php">クローゼットに戻る</a>
            <form action="<?= BASE_URL ?>/delete_handler.php" method="post" onsubmit="return confirm('本当にこのアイテムを削除しますか？');">
                <input type="hidden" name="type" value="closet_item">
                <input type="hidden" name="id" value="<?= $current_item['id'] ?>">
                <button type="submit" class="delete-button">このアイテムを削除</button>
            </form>
        </div>
    </div>
</div>
<?php include 'templates/footer.php'; ?>