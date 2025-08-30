<?php
require_once 'helpers.php';
login_check();

// 自分の投稿を取得
$my_posts = [];
$all_posts = load_data('posts');
if (!empty($all_posts)) {
    foreach ($all_posts as $post) {
        if ($post['user_id'] == $_SESSION['user_id']) {
            $my_posts[] = $post;
        }
    }
}
?>

<?php include 'templates/header.php'; ?>
<div class="container">
    <h2>マイページ</h2>

    <div class="mypage-section">
        <h3>AI機能</h3>
        <div class="mypage-menu">
            <a href="<?= BASE_URL ?>/ai_analysis.php" class="menu-button">AIアイテム分析</a>
            <p>クローゼットのアイテムをAIに分析させ、コーディネート提案の準備をします。</p>
        </div>
        </div>

    <hr>

    <div class="mypage-section">
        <h3>自分の投稿一覧</h3>
        <?php if (empty($my_posts)): ?>
            <p>まだ投稿がありません。</p>
        <?php else: ?>
            <div class="my-posts-list">
                <?php foreach(array_reverse($my_posts) as $post): ?>
                    <div class="my-post-item">
                        <a href="<?= BASE_URL ?>/post_detail.php?id=<?= $post['id'] ?>">
                            <img src="<?= htmlspecialchars($post['post_image']) ?>" alt="<?= htmlspecialchars($post['title']) ?>">
                        </a>
                        <div class="my-post-info">
                            <h4><a href="<?= BASE_URL ?>/post_detail.php?id=<?= $post['id'] ?>"><?= htmlspecialchars($post['title']) ?></a></h4>
                            <p><?= mb_substr(htmlspecialchars($post['description']), 0, 50) ?>...</p>
                            <div class="my-post-actions">
                                <form action="<?= BASE_URL ?>/delete_handler.php" method="post" onsubmit="return confirm('本当にこの投稿を削除しますか？');">
                                    <input type="hidden" name="type" value="post">
                                    <input type="hidden" name="id" value="<?= $post['id'] ?>">
                                    <button type="submit" class="delete-button">投稿を削除</button>
                                </form>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>
<?php include 'templates/footer.php'; ?>
