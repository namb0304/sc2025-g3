<?php
require_once 'helpers.php';
login_check();

$user_id = $_SESSION['user_id'];
$user = get_user_by_id($user_id);

// URLからどのタブを表示するか取得 (?tab=... がなければ 'posts' になる)
$tab = $_GET['tab'] ?? 'posts';

// タブに応じて必要なデータだけを取得する
if ($tab === 'closet') {
    // クローゼットタブを表示する場合のデータ取得 (closet.phpから移植)
    $message = $_SESSION['message'] ?? '';
    unset($_SESSION['message']);
    $genre_options = ['カジュアル', 'きれいめ', 'ストリート', 'フェミニン', 'モード', 'オフィス', 'アウトドア'];
    $my_closet_items = get_closet_items_by_user_id($user_id);
} elseif ($tab === 'likes') {
    // いいねタブのデータ取得 (今回は空)
    $liked_posts = [];
} else {
    // 投稿タブのデータ取得 (デフォルト)
    $my_posts = get_posts_by_user_id($user_id);
}

include 'templates/header.php';
?>
<div class="mypage-container">
    <div class="profile-card">
        <div class="profile-avatar">
            <span><?= strtoupper(htmlspecialchars(mb_substr($user['username'], 0, 1))) ?></span>
            <button class="avatar-edit-btn"><i class="fa fa-camera"></i></button>
        </div>
        <div class="profile-info">
            <h2 class="profile-username"><?= htmlspecialchars($user['username']) ?></h2>
            <p class="profile-handle">@<?= htmlspecialchars($user['username']) ?></p>
        </div>
        <div class="profile-actions">
            <button class="btn-primary">プロフィールを編集</button>
            <button class="btn-secondary">設定</button>
        </div>
    </div>

    <div class="tabs-container">
        <a href="?tab=posts" class="tab-link <?= $tab === 'posts' ? 'active' : '' ?>">投稿</a>
        <a href="?tab=closet" class="tab-link <?= $tab === 'closet' ? 'active' : '' ?>">デジタルクローゼット</a>
        <a href="?tab=likes" class="tab-link <?= $tab === 'likes' ? 'active' : '' ?>">いいね</a>
    </div>

    <div class="tab-content">
        <?php if ($tab === 'posts'): ?>
            <?php if (empty($my_posts)): ?>
                <div class="empty-state">まだ投稿がありません。</div>
            <?php else: ?>
                <div class="my-posts-list">
                    <?php foreach($my_posts as $post): ?>
                        <div class="my-post-item">
                            <a href="<?= BASE_URL ?>/post_detail.php?id=<?= $post['id'] ?>"><img src="image.php?id=<?= $post['post_image_id'] ?>" alt="<?= htmlspecialchars($post['title']) ?>"></a>
                            <div class="my-post-info">
                                <h4><a href="<?= BASE_URL ?>/post_detail.php?id=<?= $post['id'] ?>"><?= htmlspecialchars($post['title']) ?></a></h4>
                                <p><?= mb_substr(htmlspecialchars($post['description']), 0, 50) ?>...</p>
                                <form action="<?= BASE_URL ?>/delete_handler.php" method="post" onsubmit="return confirm('本当にこの投稿を削除しますか？');">
                                    <input type="hidden" name="type" value="post"><input type="hidden" name="id" value="<?= $post['id'] ?>">
                                    <button type="submit" class="delete-button">投稿を削除</button>
                                </form>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

        <?php elseif ($tab === 'closet'): ?>
            <?php if($message): ?><p class="message-box"><?= htmlspecialchars($message) ?></p><?php endif; ?>
            <div class="form-container">
                <div class="form-header"><i class="fas fa-plus-circle"></i> アイテム情報を入力</div>
                <form action="<?= BASE_URL ?>/closet.php" method="post" enctype="multipart/form-data">
                    <div class="form-row">
                        <label for="itemImage" class="label">写真 (必須)</label>
                        <div class="image-preview" id="imagePreview">
                            <img src="" alt="画像プレビュー" id="previewImage"><div class="image-placeholder" id="imagePlaceholder"><i class="fas fa-camera"></i><p>クリックして画像を選択</p></div>
                        </div>
                        <input type="file" id="itemImage" name="item_image" accept="image/*" required style="display: none;">
                    </div>
                    <div class="form-row">
                        <label for="category" class="label">カテゴリー (必須)</label>
                        <select id="category" name="category" class="select" required><option value="" disabled selected>カテゴリーを選択</option><option value="トップス">トップス</option><option value="ボトムス">ボトムス</option><option value="アウター">アウター</option><option value="ワンピース">ワンピース</option><option value="シューズ">シューズ</option><option value="バッグ">バッグ</option><option value="その他">その他</option></select>
                    </div>
                    <div class="form-row">
                        <label class="label">ジャンル (複数可)</label>
                        <div class="checkbox-group"><?php foreach($genre_options as $genre): ?><label><input type="checkbox" name="genres[]" value="<?= $genre ?>"> <?= $genre ?></label><?php endforeach; ?></div>
                    </div>
                    <div class="form-row">
                        <label for="notes" class="label">備考</label><textarea id="notes" name="notes" placeholder="ブランド名、購入時期、素材など自由に入力できます"></textarea>
                    </div>
                    <button type="submit" class="btn-purple"><i class="fas fa-save"></i> クローゼットに登録</button>
                </form>
            </div>
            <hr><h3>登録済みアイテム</h3>
            <div class="closet-grid">
                <?php if (empty($my_closet_items)): ?><p>まだアイテムが登録されていません。</p>
                <?php else: ?><?php foreach ($my_closet_items as $item): ?>
                    <div class="closet-item"><a href="<?= BASE_URL ?>/closet_detail.php?id=<?= $item['id'] ?>"><img src="image.php?id=<?= $item['id'] ?>" alt="<?= htmlspecialchars($item['category']) ?>"></a></div>
                <?php endforeach; ?><?php endif; ?>
            </div>
            <script>
            document.addEventListener('DOMContentLoaded', function() {
                if (document.getElementById('imagePreview')) {
                    const imagePreview = document.getElementById('imagePreview');
                    const itemImage = document.getElementById('itemImage');
                    imagePreview.addEventListener('click', function() { itemImage.click(); });
                    itemImage.addEventListener('change', function(e) {
                        const preview = document.getElementById('previewImage');
                        const placeholder = document.getElementById('imagePlaceholder');
                        const file = e.target.files[0];
                        if (file) {
                            const reader = new FileReader();
                            reader.onload = function(e) {
                                preview.src = e.target.result;
                                preview.style.display = 'block';
                                placeholder.style.display = 'none';
                            };
                            reader.readAsDataURL(file);
                        }
                    });
                }
            });
            </script>
        <?php elseif ($tab === 'likes'): ?>
            <div class="empty-state">
                <h3>「いいね！」した投稿はまだありません</h3>
                <p>気になる投稿に「いいね！」してみましょう！</p>
            </div>
        <?php endif; ?>
    </div>
</div>
<?php include 'templates/footer.php'; ?>