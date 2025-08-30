<?php
require_once 'helpers.php';
login_check();

$closet_items = load_data('closet');
$item_id = $_GET['id'] ?? '';
$current_item = find_by_id($closet_items, $item_id);

if (!$current_item || $current_item['user_id'] != $_SESSION['user_id']) {
    die('アイテムが見つかりません。');
}

$manual_tags = $current_item['manual_tags'] ?? [];
$category = $manual_tags['category'] ?? '未分類';
$genres = $manual_tags['genres'] ?? [];
$notes = $manual_tags['notes'] ?? '備考はありません。';
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
                <p><?= htmlspecialchars($category) ?></p>
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
                    <p>ジャンルは登録されていません。</p>
                <?php endif; ?>
            </div>

            <div class="info-group">
                <h3>備考</h3>
                <p><?= nl2br(htmlspecialchars($notes)) ?></p>
            </div>
            
            <div class="item-actions-footer">
                <a href="<?= BASE_URL ?>/closet.php" class="back-link">クローゼットに戻る</a>
                <form action="<?= BASE_URL ?>/delete_handler.php" method="post" onsubmit="return confirm('本当にこのアイテムを削除しますか？\nこのアイテムを使った投稿は削除されません。');">
                    <input type="hidden" name="type" value="closet_item">
                    <input type="hidden" name="id" value="<?= $current_item['id'] ?>">
                    <button type="submit" class="delete-button">このアイテムを削除</button>
                </form>
            </div>
        </div>
    </div>
</div>
<?php include 'templates/footer.php'; ?>
