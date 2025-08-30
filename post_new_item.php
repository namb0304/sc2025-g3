<?php
require_once 'helpers.php';
login_check();

$user_id = $_SESSION['user_id'];
$user = get_user_by_id($user_id);
$genre_options = ['カジュアル', 'きれいめ', 'ストリート', 'フェミニン', 'モード', 'オフィス', 'アウトドア'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $error = '';
    // --- ステップ1: アイテム登録 ---
    if (!empty($_FILES['item_image']['name'])) {
        $target_dir = 'uploads/' . $user['username'] . '/closet/';
        if (!is_dir($target_dir)) mkdir($target_dir, 0777, true);
        $filename = time() . '_' . basename($_FILES["item_image"]["name"]);
        $target_file = $target_dir . $filename;

        if (move_uploaded_file($_FILES['item_image']['tmp_name'], $target_file)) {
            $new_item_id = create_closet_item(
                $user_id, $target_file, $_POST['category'] ?? '未分類',
                $_POST['genres'] ?? [], htmlspecialchars($_POST['notes'] ?? '')
            );
        } else {
             $error = "画像アップロードエラー。";
        }
    } else {
        $error = "画像が選択されていません。";
    }

    // --- ステップ2: 投稿作成 ---
    if (empty($error)) {
        create_post(
            $user_id, htmlspecialchars($_POST['title']), htmlspecialchars($_POST['description']),
            $target_file, $new_item_id
        );
        header('Location: ' . BASE_URL . '/index.php');
        exit;
    }
}

include 'templates/header.php';
?>
<div class="container">
    <h2>新しく服を登録して投稿</h2>
    <form action="<?= BASE_URL ?>/post_new_item.php" method="post" enctype="multipart/form-data">
        <?php if(isset($error) && !empty($error)): ?><p class="error"><?= $error ?></p><?php endif; ?>
        <h3>1. 新しい服の情報を登録</h3>
        <p><strong>写真 (必須)</strong></p>
        <input type="file" name="item_image" required>
        <hr>
        <h3>2. コーディネート情報を入力</h3>
        <input type="text" name="title" placeholder="コーディネートのタイトル" required><br>
        <textarea name="description" placeholder="コーディネートの説明やポイント" required></textarea><br>
        <button type="submit">登録して投稿する</button>
    </form>
</div>
<?php include 'templates/footer.php'; ?>