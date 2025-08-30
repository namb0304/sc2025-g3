<?php
require_once 'helpers.php';

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
                        <img src="<?= htmlspecialchars($post['post_image']) ?>" alt="<?= htmlspecialchars($post['title']) ?>">
                        <div class="post-info">
                            <h3><?= htmlspecialchars($post['title']) ?></h3>
                            <p>by <?= htmlspecialchars($post['username']) ?></p>
                        </div>
                    </a>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>
<?php include 'templates/footer.php'; ?>