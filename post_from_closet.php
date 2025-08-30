<?php
require_once 'helpers.php';
login_check();

// --- ページ表示(GET)の処理 ---
$my_closet_items = get_closet_items_by_user_id($_SESSION['user_id']);

// --- フォーム送信(POST)の処理 ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $selected_item_id = $_POST['selected_item_id'] ?? '';
    
    // --- デバッグ1：選択されたアイテムIDを確認 ---
    if (empty($selected_item_id)) {
        die("エラー：アイテムが選択されていません。フォームから正しくIDが送信されているか確認してください。");
    }

    $selected_item = find_closet_item_by_id($selected_item_id, $_SESSION['user_id']);

    // --- デバッグ2：アイテムがDBから見つかったか確認 ---
    if (!$selected_item) {
        die("エラー：選択されたアイテム (ID: " . htmlspecialchars($selected_item_id) . ") がデータベースに見つかりません。");
    }

    // --- デバッグ3：create_postの呼び出しと結果の確認 ---
    // ★★★ おそらく、この下の行でエラーが発生しています ★★★
    // 'image_path' はもう存在しないため、$selected_item['image_path'] でエラーになります。
    
    // 根本的な解決策を適用するまでは、この行はコメントアウトするか、修正が必要です。
    // 例として、ダミーのパスを渡してみます。
    $image_path_for_post = 'dummy_path.jpg'; // 本来は closet_item_id から画像を取得すべき

    $result = create_post(
        $_SESSION['user_id'],
        htmlspecialchars($_POST['title']),
        htmlspecialchars($_POST['description']),
        $image_path_for_post, // ダミーのパスを使用
        $selected_item_id
    );

    if ($result) {
        header('Location: ' . BASE_URL . '/index.php');
        exit;
    } else {
        die("エラー：データベースへの投稿作成に失敗しました。postsテーブルの定義や、create_post関数を確認してください。");
    }
}

// --- HTML表示部分 ---
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