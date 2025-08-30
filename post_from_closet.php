<?php
require_once 'helpers.php';
login_check();

// --- ページ表示(GET)の処理 ---
$my_closet_items = get_closet_items_by_user_id($_SESSION['user_id']);
$error = ''; // エラーメッセージ用の変数を初期化

// --- フォーム送信(POST)の処理 ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $selected_item_id = $_POST['selected_item_id'] ?? '';
    $selected_item = find_closet_item_by_id($selected_item_id, $_SESSION['user_id']);

    if ($selected_item) {
        // 修正された4つの引数で create_post 関数を呼び出す
        if (create_post(
            $_SESSION['user_id'],
            htmlspecialchars($_POST['title']),
            htmlspecialchars($_POST['description']),
            $selected_item_id
        )) {
            // 成功したらホームへリダイレクト
            header('Location: ' . BASE_URL . '/index.php');
            exit;
        }
    }
    
    // 失敗した場合はエラーメッセージを設定してフォームを再表示
    $error = "投稿に失敗しました。もう一度お試しください。";
}

// --- HTML表示部分 ---
include 'templates/header.php';
?>
<div class="container">
    <h2>クローゼットから選択して投稿</h2>
    <?php if(!empty($error)): ?><p class="error"><?= $error ?></p><?php endif; ?>
    
    <form action="<?= BASE_URL ?>/post_from_closet.php" method="post">
        <h3>1. コーディネートの主役を選択</h3>
        <?php if (empty($my_closet_items)): ?>
            <p>投稿できるアイテムがありません。<a href="<?= BASE_URL ?>/closet.php">クローゼット登録</a>をしてください。</p>
        <?php else: ?>
            <div class="item-selection-grid">
                <?php foreach($my_closet_items as $item): ?>
                    <label class="selectable-item">
                        <input type="radio" name="selected_item_id" value="<?= $item['id'] ?>" required>
                        <img src="image.php?id=<?= $item['id'] ?>" alt="選択肢">
                    </label>
                <?php endforeach; ?>
            </div>
            <h3>2. コーディネート情報を入力</h3>
            <input type="text" name="title" placeholder="コーディネートのタイトル" required><br>
            <textarea name="description" placeholder="コーディネートの説明やポイント" required></textarea><br>
            <button type="submit">この内容で投稿する</button>
        <?php endif; ?>
    </form>
</div>
<?php include 'templates/footer.php'; ?>