<?php
require_once __DIR__ . '/../../src/helpers.php';
$posts = load_data('posts');
$post_id = $_GET['id'] ?? '';
$current_post = null;
$post_index = -1;

foreach ($posts as $index => $p) {
    if ($p['id'] === $post_id) {
        $current_post = $p;
        $post_index = $index;
        break;
    }
}

if (!$current_post) { die('投稿が見つかりません。'); }

if (is_logged_in() && $_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['comment'])) {
    $user = get_user_by_id($_SESSION['user_id']);
    $new_comment = [
        'username' => $user['username'],
        'text' => htmlspecialchars($_POST['comment']),
        'timestamp' => date('Y-m-d H:i:s')
    ];
    
    $posts[$post_index]['comments'][] = $new_comment;
    save_data('posts', $posts);
    header('Location: post_detail.php?id=' . $post_id);
    exit;
}
?>

<?php include 'src/templates/header.php'; ?>
<div class="container">
    <div class="post-full">
        <h2><?= htmlspecialchars($current_post['title']) ?></h2>
        <p class="post-meta">投稿者: <?= htmlspecialchars($current_post['username']) ?></p>
        <img src="<?= htmlspecialchars($current_post['post_image']) ?>" alt="<?= htmlspecialchars($current_post['title']) ?>" class="post-main-image">
        <p class="post-description"><?= nl2br(htmlspecialchars($current_post['description'])) ?></p>
    </div>

    <hr>

    <div class="comments-section">
        <h3>コメント</h3>
        <?php if (is_logged_in()): ?>
            <form action="post_detail.php?id=<?= $post_id ?>" method="post">
                <textarea name="comment" placeholder="素敵なコメントを残しましょう" required></textarea>
                <button type="submit">コメントする</button>
            </form>
        <?php else: ?>
            <p><a href="/auth/login.php">ログイン</a>してコメントに参加しませんか？</p>
        <?php endif; ?>

        <ul>
            <?php if (empty($current_post['comments'])): ?>
                <li><p>まだコメントはありません。</p></li>
            <?php else: ?>
                <?php foreach (array_reverse($current_post['comments']) as $comment): ?>
                    <li>
                        <p><strong><?= htmlspecialchars($comment['username']) ?>:</strong></p>
                        <p><?= nl2br(htmlspecialchars($comment['text'])) ?></p>
                        <span><?= $comment['timestamp'] ?></span>
                    </li>
                <?php endforeach; ?>
            <?php endif; ?>
        </ul>
    </div>
</div>
<?php include 'templates/footer.php'; ?>
