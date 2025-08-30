<?php
require_once 'helpers.php';
login_check();

$my_posts = get_posts_by_user_id($_SESSION['user_id']);

include 'templates/header.php';
?>
<div class="container">
    <h2>マイページ</h2>
    <div class="mypage-section">
        <h3>AI機能</h3>
        <a href="<?= BASE_URL ?>/ai_analysis.php">AIアイテム分析</a>
        <p>クローゼットのアイテムをAIに分析させ、コーディネート提案の準備をします。</p>
    </div>
    <hr>
    <div class="mypage-section">
        <h3>自分の投稿一覧</h3>
        <?php if (empty($my_posts)): ?>
            <p>まだ投稿がありません。</p>
        <?php else: ?>
            <div class="my-posts-list">
                <?php foreach($my_posts as $post): ?>
                    <div class="my-post-item">
                        <a href="<?= BASE_URL ?>/post_detail.php?id=<?= $post['id'] ?>">
                            <img src="<?= htmlspecialchars($post['post_image']) ?>" alt="<?= htmlspecialchars($post['title']) ?>">
                        </a>
                        <div class="my-post-info">
                            <h4><a href="<?= BASE_URL ?>/post_detail.php?id=<?= $post['id'] ?>"><?= htmlspecialchars($post['title']) ?></a></h4>
                            <p><?= mb_substr(htmlspecialchars($post['description']), 0, 50) ?>...</p>
                            <form action="<?= BASE_URL ?>/delete_handler.php" method="post" onsubmit="return confirm('本当にこの投稿を削除しますか？');">
                                <input type="hidden" name="type" value="post">
                                <input type="hidden" name="id" value="<?= $post['id'] ?>">
                                <button type="submit" class="delete-button">投稿を削除</button>
                            </form>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>
<?php include 'templates/footer.php'; ?>