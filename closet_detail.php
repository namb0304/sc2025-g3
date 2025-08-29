<?php
require_once 'helpers.php';
login_check();

$closet_items = load_data('closet');
$item_id = $_GET['id'] ?? '';
$current_item = find_by_id($closet_items, $item_id);

// アイテムが見つからない、または他人のアイテムの場合はエラー
if (!$current_item || $current_item['user_id'] != $_SESSION['user_id']) {
    die('アイテムが見つかりません。');
}

$manual_tags = $current_item['manual_tags'];
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
                <p><?= htmlspecialchars($manual_tags['category']) ?></p>
            </div>

            <div class="info-group">
                <h3>ジャンル</h3>
                <?php if (!empty($manual_tags['genres'])): ?>
                    <div class="genre-tags">
                        <?php foreach($manual_tags['genres'] as $genre): ?>
                            <span class="tag"><?= htmlspecialchars($genre) ?></span>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <p>ジャンルは登録されていません。</p>
                <?php endif; ?>
            </div>

            <div class="info-group">
                <h3>備考</h3>
                <p><?= nl2br(htmlspecialchars($manual_tags['notes'] ?: '備考はありません。')) ?></p>
            </div>

            <a href="<?= BASE_URL ?>/closet.php" class="back-link">クローゼットに戻る</a>
        </div>
    </div>
</div>

<style>
/* このページ専用のスタイル */
.item-detail-container { display: flex; gap: 30px; background: #fff; padding: 30px; border-radius: 8px; box-shadow: 0 0 15px rgba(0,0,0,0.1); }
.item-image-view { flex: 1; }
.item-image-view img { width: 100%; border-radius: 8px; }
.item-info-view { flex: 1; }
.info-group { margin-bottom: 20px; }
.info-group h3 { margin: 0 0 5px 0; border-bottom: 2px solid #f0f0f0; padding-bottom: 5px; }
.genre-tags { display: flex; flex-wrap: wrap; gap: 10px; }
.tag { background: #007bff; color: white; padding: 5px 12px; border-radius: 15px; font-size: 14px; }
.back-link { display: inline-block; margin-top: 20px; color: #555; }
</style>

<?php include 'templates/footer.php'; ?>
