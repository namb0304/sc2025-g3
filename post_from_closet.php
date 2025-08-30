<?php
require_once 'helpers.php';
login_check();

// 自分のクローゼットアイテムを取得
$my_closet_items = [];
$all_closet_items = load_data('closet');
if (!empty($all_closet_items)) {
    foreach ($all_closet_items as $item) {
        if ($item['user_id'] == $_SESSION['user_id']) {
            $my_closet_items[] = $item;
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $posts = load_data('posts');
    $user = get_user_by_id($_SESSION['user_id']);
    
    $title = htmlspecialchars($_POST['title']);
    $description = htmlspecialchars($_POST['description']);
    $selected_item_id = $_POST['selected_item_id'] ?? ''; // IDを受け取る

    $selected_item = find_by_id($my_closet_items, $selected_item_id);

    if ($selected_item) {
        $new_post = [
            'id' => uniqid('post_'),
            'user_id' => $user['id'],
            'username' => $user['username'],
            'closet_item_id' => $selected_item_id, // 【重要】どのアイテムかIDを記録
            'post_image' => $selected_item['image_path'], // 画像パスも保存
            'title' => $title,
            'description' => $description,
            'comments' => [],
            'likes' => [],
            'dislikes' => []
        ];
        $posts[] = $new_post;
        save_data('posts', $posts);
        header('Location: ' . BASE_URL . '/index.php');
        exit;
    } else {
        $error = "投稿するアイテムを正しく選択してください。";
    }
}

include 'templates/header.php';
?>
<div class="container">
    <h2>クローゼットから選択して投稿</h2>
    <form action="<?= BASE_URL ?>/post_from_closet.php" method="post">
        
        <h3>1. コーディネートの主役を選択</h3>
        <?php if (empty($my_closet_items)): ?>
            <p>投稿できるアイテムがクローゼットにありません。先に<a href="<?= BASE_URL ?>/closet.php">クローゼット登録</a>をしてください。</p>
        <?php else: ?>
            <div class="item-selection-grid">
                <?php foreach(array_reverse($my_closet_items) as $item): ?>
                    <label class="selectable-item">
                        <input type="radio" name="selected_item_id" value="<?= htmlspecialchars($item['id']) ?>" required>
                        <img src="<?= htmlspecialchars($item['image_path']) ?>" alt="選択肢">
                    </label>
                <?php endforeach; ?>
            </div>
        
            <h3>2. コーディネート情報を入力</h3>
            <?php if(isset($error)): ?><p class="error"><?= $error ?></p><?php endif; ?>
            <input type="text" name="title" placeholder="コーディネートのタイトル" required><br>
            <textarea name="description" placeholder="コーディネートの説明やポイント" required></textarea><br>
            <button type="submit">この内容で投稿する</button>
        <?php endif; ?>
    </form>
</div>
<?php include 'templates/footer.php'; ?>
