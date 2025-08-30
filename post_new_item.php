<?php
require_once 'helpers.php';
login_check();

// フォームで表示するジャンルの選択肢
$genre_options = ['カジュアル', 'きれいめ', 'ストリート', 'フェミニン', 'モード', 'オフィス', 'アウトドア'];
$user = get_user_by_id($_SESSION['user_id']);

// フォームが送信されたときの処理
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // --- ステップ1: クローゼットに新しいアイテムを登録 ---
    $closet = load_data('closet');
    $new_item_id = uniqid('item_'); // 先にアイテムのための一意なIDを生成
    $new_item_path = '';
    $error = '';

    if (!empty($_FILES['item_image']['name'])) {
        $category = $_POST['category'] ?? '未分類';
        $genres = $_POST['genres'] ?? [];
        $notes = $_POST['notes'] ?? '';

        $target_dir = 'uploads/' . $user['username'] . '/closet/';
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0777, true);
        }

        $filename = time() . '_' . basename($_FILES["item_image"]["name"]);
        $target_file = $target_dir . $filename;

        // ファイルのアップロードを実行
        if (move_uploaded_file($_FILES['item_image']['tmp_name'], $target_file)) {
            $new_item_path = $target_file;
            $new_item = [
                'id' => $new_item_id, // 生成したIDを使用
                'user_id' => $user['id'],
                'image_path' => $new_item_path,
                'manual_tags' => [
                    'category' => $category, 
                    'genres' => $genres, 
                    'notes' => htmlspecialchars($notes)
                ]
            ];
            $closet[] = $new_item;
            save_data('closet', $closet);
        } else {
             $error = "画像のアップロード中にエラーが発生しました。";
        }
    } else {
        $error = "画像が選択されていません。";
    }
    
    // --- ステップ2: 登録したアイテムを使って投稿を作成 ---
    // エラーがなく、アイテムが正常に登録された場合のみ実行
    if (empty($error) && !empty($new_item_path)) {
        $posts = load_data('posts');
        $title = htmlspecialchars($_POST['title']);
        $description = htmlspecialchars($_POST['description']);
        
        $new_post = [
            'id' => uniqid('post_'), 
            'user_id' => $user['id'], 
            'username' => $user['username'],
            'closet_item_id' => $new_item_id, // 新アイテムのIDを記録
            'post_image' => $new_item_path,
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
    }
    // ステップ1で$errorが発生していた場合は、下のHTML部分でエラーが表示される
}

// --- ここから下はページのHTML表示 ---
include 'templates/header.php';
?>
<div class="container">
    <h2>新しく服を登録して投稿</h2>
    <form action="<?= BASE_URL ?>/post_new_item.php" method="post" enctype="multipart/form-data">
        <?php if(isset($error) && !empty($error)): ?><p class="error"><?= htmlspecialchars($error) ?></p><?php endif; ?>

        <h3>1. 新しい服の情報を登録</h3>
        <div class="closet-form">
            <p><strong>写真を選択 (必須)</strong></p>
            <input type="file" name="item_image" required>

            <p><strong>種類を選択 (必須)</strong></p>
            <div class="radio-group">
                <label><input type="radio" name="category" value="トップス" required> トップス</label>
                <label><input type="radio" name="category" value="ボトムス"> ボトムス</label>
                <label><input type="radio" name="category" value="アウター"> アウター</label>
                <label><input type="radio" name="category" value="ワンピース"> ワンピース</label>
                <label><input type="radio" name="category" value="シューズ"> シューズ</label>
                <label><input type="radio" name="category" value="バッグ"> バッグ</label>
                <label><input type="radio" name="category" value="その他"> その他</label>
            </div>

            <p><strong>ジャンルを選択 (複数可)</strong></p>
            <div class="checkbox-group">
                <?php foreach($genre_options as $genre): ?>
                    <label><input type="checkbox" name="genres[]" value="<?= $genre ?>"> <?= $genre ?></label>
                <?php endforeach; ?>
            </div>
            
            <p><strong>備考</strong></p>
            <textarea name="notes" placeholder="ブランド名、購入時期、素材など自由に入力できます"></textarea>
        </div>

        <hr>
        
        <h3>2. コーディネート情報を入力</h3>
        <input type="text" name="title" placeholder="コーディネートのタイトル" required><br>
        <textarea name="description" placeholder="コーディネートの説明やポイント" required></textarea><br>
        <button type="submit">登録して投稿する</button>
    </form>
</div>
<?php include 'templates/footer.php'; ?>
