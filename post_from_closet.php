<?php
require_once 'helpers.php';
login_check();

$my_closet_items = get_closet_items_by_user_id($_SESSION['user_id']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $selected_item_id = $_POST['selected_item_id'] ?? '';
    $selected_item = find_closet_item_by_id($selected_item_id, $_SESSION['user_id']);

    if ($selected_item) {
        create_post(
            $_SESSION['user_id'],
            htmlspecialchars($_POST['title']),
            htmlspecialchars($_POST['description']),
            $selected_item['image_path'],
            $selected_item_id
        );
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
            <p>投稿できるアイテムがありません。<a href="<?= BASE_URL ?>/closet.php">クローゼット登録</a>をしてください。</p>
        <?php else: ?>
            <div class="item-selection-grid">
                <?php foreach($my_closet_items as $item): ?>
                    <label class="selectable-item">
                        <input type="radio" name="selected_item_id" value="<?= $item['id'] ?>" required>
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