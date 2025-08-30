<?php
require_once 'helpers.php';

$posts = load_data('posts');
$post_id = $_GET['id'] ?? '';
$current_user_id = $_SESSION['user_id'] ?? null;

// 投稿IDを元に、投稿が配列の何番目にあるかを探す
$post_index = find_index_by_id($posts, $post_id);

// 投稿が見つからなかった場合はエラーを表示して終了
if ($post_index === false) {
    die('投稿が見つかりません。');
}

// --- いいね、コメントなどのPOSTリクエスト処理 ---
if (is_logged_in() && $_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // いいね・低評価ボタンが押された場合
    if (isset($_POST['action'])) {
        $action = $_POST['action'];

        // いいね・低評価の配列がなければ、この投稿データに空の配列を作成する
        if (!isset($posts[$post_index]['likes'])) {
            $posts[$post_index]['likes'] = [];
        }
        if (!isset($posts[$post_index]['dislikes'])) {
            $posts[$post_index]['dislikes'] = [];
        }

        // いいね(like)ボタンが押された時の処理
        if ($action === 'like') {
            $like_index = array_search($current_user_id, $posts[$post_index]['likes']);
            $dislike_index = array_search($current_user_id, $posts[$post_index]['dislikes']);

            // 1. もし既にいいねしていたら、いいねを取り消す
            if ($like_index !== false) {
                array_splice($posts[$post_index]['likes'], $like_index, 1);
            } else {
            // 2. もし低評価していたら、低評価を取り消してからいいねする
                if ($dislike_index !== false) {
                    array_splice($posts[$post_index]['dislikes'], $dislike_index, 1);
                }
            // 3. いいねしていなければ、いいねリストにユーザーIDを追加
                $posts[$post_index]['likes'][] = $current_user_id;
            }
        }
        
        // 低評価(dislike)ボタンが押された時の処理
        if ($action === 'dislike') {
            $like_index = array_search($current_user_id, $posts[$post_index]['likes']);
            $dislike_index = array_search($current_user_id, $posts[$post_index]['dislikes']);

            // 1. もし既に低評価していたら、低評価を取り消す
            if ($dislike_index !== false) {
                array_splice($posts[$post_index]['dislikes'], $dislike_index, 1);
            } else {
            // 2. もしいいねしていたら、いいねを取り消してから低評価する
                if ($like_index !== false) {
                    array_splice($posts[$post_index]['likes'], $like_index, 1);
                }
            // 3. 低評価していなければ、低評価リストにユーザーIDを追加
                $posts[$post_index]['dislikes'][] = $current_user_id;
            }
        }
    }

    // コメントが投稿された場合
    if (isset($_POST['comment']) && !empty(trim($_POST['comment']))) {
        $user = get_user_by_id($current_user_id);
        
        // コメント配列がなければ作成する
        if (!isset($posts[$post_index]['comments'])) {
            $posts[$post_index]['comments'] = [];
        }

        $new_comment = [
            'id' => uniqid('comment_'), // コメント削除時に使うためのユニークID
            'user_id' => $current_user_id,
            'username' => $user['username'],
            'text' => htmlspecialchars(trim($_POST['comment'])),
            'timestamp' => date('Y-m-d H:i:s')
        ];
        // 配列の先頭に新しいコメントを追加する（新しい順に表示しやすいため）
        array_unshift($posts[$post_index]['comments'], $new_comment);
    }

    // 変更を保存して、フォーム再送信を防ぐためにリダイレクト
    save_data('posts', $posts);
    header('Location: ' . BASE_URL . '/post_detail.php?id=' . $post_id);
    exit;
}

// --- ここから下はページの表示処理 ---

// 処理後の最新の投稿データを取得
$current_post = $posts[$post_index];

// 投稿データに紐づくクローゼットアイテム情報を取得
$closet_item = null;
if (isset($current_post['closet_item_id'])) {
    $closet_data = load_data('closet');
    $closet_item = find_by_id($closet_data, $current_post['closet_item_id']);
}

// 安全のため、各項目が存在するかチェックし、なければデフォルト値を設定
$post_title = $current_post['title'] ?? '無題';
$post_username = $current_post['username'] ?? '不明なユーザー';
$post_description = $current_post['description'] ?? '説明はありません。';
$likes = $current_post['likes'] ?? [];
$dislikes = $current_post['dislikes'] ?? [];
$comments = $current_post['comments'] ?? [];

$has_liked = is_logged_in() && in_array($current_user_id, $likes);
$has_disliked = is_logged_in() && in_array($current_user_id, $dislikes);

include 'templates/header.php';
?>
<div class="container">
    <div class="post-full">
        <h2><?= htmlspecialchars($post_title) ?></h2>
        <p class="post-description"><?= nl2br(htmlspecialchars($post_description)) ?></p>
        <p class="post-meta">投稿者: <?= htmlspecialchars($post_username) ?></p>

        <hr class="divider">

        <h3>コーディネートアイテム</h3>
        <?php if ($closet_item): ?>
            <div class="item-detail-container-small">
                <div class="item-image-view-small">
                    <img src="<?= BASE_URL ?>/<?= htmlspecialchars($closet_item['image_path']) ?>" alt="アイテム画像">
                </div>
                <div class="item-info-view-small">
                    <div class="info-group">
                        <h4>種類</h4>
                        <p><?= htmlspecialchars($closet_item['manual_tags']['category'] ?? '未分類') ?></p>
                    </div>
                    <div class="info-group">
                        <h4>ジャンル</h4>
                        <?php if (!empty($closet_item['manual_tags']['genres'])): ?>
                            <div class="genre-tags">
                                <?php foreach($closet_item['manual_tags']['genres'] as $genre): ?>
                                    <span class="tag"><?= htmlspecialchars($genre) ?></span>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="info-group">
                        <h4>備考</h4>
                        <p><?= nl2br(htmlspecialchars($closet_item['manual_tags']['notes'] ?: 'なし')) ?></p>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <p>この投稿に関連付けられたクローゼットアイテム情報はありません。</p>
        <?php endif; ?>
        
        <div class="post-actions">
            <form action="<?= BASE_URL ?>/post_detail.php?id=<?= $post_id ?>" method="post">
                <button type="submit" name="action" value="like" class="<?= $has_liked ? 'active' : '' ?>">
                    👍 いいね (<?= count($likes) ?>)
                </button>
                <button type="submit" name="action" value="dislike" class="<?= $has_disliked ? 'active' : '' ?>">
                    👎 低評価 (<?= count($dislikes) ?>)
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
                <?php foreach ($comments as $comment): // 新しい順に保存されているので、そのままループ ?>
                    <li>
                        <p><strong><?= htmlspecialchars($comment['username']) ?>:</strong></p>
                        <p><?= nl2br(htmlspecialchars($comment['text'])) ?></p>
                        <div class="comment-footer">
                            <span><?= $comment['timestamp'] ?></span>
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
