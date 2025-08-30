<?php
require_once 'helpers.php';
login_check();

// --- フォーム送信(POST)の処理をページの描画より先に行う ---
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES["item_image"])) {
    $user = get_user_by_id($_SESSION['user_id']);
    $closet = load_data('closet');
    
    $category = $_POST['category'] ?? '未分類';
    $genres = $_POST['genres'] ?? [];
    $notes = $_POST['notes'] ?? '';

    $target_dir = 'uploads/' . $user['username'] . '/closet/';
    if (!is_dir($target_dir)) mkdir($target_dir, 0777, true);

    $filename = time() . '_' . basename($_FILES["item_image"]["name"]);
    $target_file = $target_dir . $filename;

    if (move_uploaded_file($_FILES["item_image"]["tmp_name"], $target_file)) {
        $new_item = [
            'id' => uniqid('item_'),
            'user_id' => $user['id'],
            'image_path' => $target_file,
            'manual_tags' => [
                'category' => $category,
                'genres' => $genres,
                'notes' => htmlspecialchars($notes)
            ]
        ];
        $closet[] = $new_item;
        save_data('closet', $closet);
        
        // メッセージをセッションに保存
        $_SESSION['message'] = "アイテムが登録されました。";
    } else {
        $_SESSION['message'] = "ファイルのアップロードに失敗しました。";
    }

    // 【重要】処理が終わったら同じページにリダイレクトする
    header('Location: ' . BASE_URL . '/closet.php');
    exit; // リダイレクト後にスクリプトの実行を停止
}

// --- ここから下はページ表示(GET)の処理 ---

// セッションからメッセージを取得して、表示後に消去する
$message = '';
if (isset($_SESSION['message'])) {
    $message = $_SESSION['message'];
    unset($_SESSION['message']);
}

$genre_options = ['カジュアル', 'きれいめ', 'ストリート', 'フェミニン', 'モード', 'オフィス', 'アウトドア'];

// 自分のアイテムのみを取得
$my_closet_items = [];
$all_closet_items = load_data('closet');
if (!empty($all_closet_items)) {
    foreach ($all_closet_items as $item) {
        if ($item['user_id'] == $_SESSION['user_id']) {
            $my_closet_items[] = $item;
        }
    }
}

include 'templates/header.php';
?>
<div class="container">
    <h2>マイクローゼット</h2>
    <h3>アイテムを登録</h3>
    <?php if($message): ?><p class="message-box"><?= htmlspecialchars($message) ?></p><?php endif; ?>
    <form class="closet-form" action="<?= BASE_URL ?>/closet.php" method="post" enctype="multipart/form-data">
        <p><strong>1. 写真を選択</strong></p>
        <input type="file" name="item_image" required>
        <p><strong>2. 種類を選択 (必須)</strong></p>
        <div class="radio-group">
            <label><input type="radio" name="category" value="トップス" required> トップス</label>
            <label><input type="radio" name="category" value="ボトムス"> ボトムス</label>
            <label><input type="radio" name="category" value="アウター"> アウター</label>
            <label><input type="radio" name="category" value="ワンピース"> ワンピース</label>
            <label><input type="radio" name="category" value="シューズ"> シューズ</label>
            <label><input type="radio" name="category" value="バッグ"> バッグ</label>
            <label><input type="radio" name="category" value="その他"> その他</label>
        </div>
        <p><strong>3. ジャンルを選択 (複数可)</strong></p>
        <div class="checkbox-group">
            <?php foreach($genre_options as $genre): ?>
                <label><input type="checkbox" name="genres[]" value="<?= $genre ?>"> <?= $genre ?></label>
            <?php endforeach; ?>
        </div>
        <p><strong>4. 備考</strong></p>
        <textarea name="notes" placeholder="ブランド名、購入時期、素材など自由に入力できます"></textarea>
        <button type="submit">クローゼットに登録</button>
    </form>
    <hr>
    <h3>登録済みアイテム</h3>
    <div class="closet-grid">
        <?php foreach (array_reverse($my_closet_items) as $item): ?>
            <div class="closet-item">
                <a href="<?= BASE_URL ?>/closet_detail.php?id=<?= $item['id'] ?>">
                    <img src="<?= htmlspecialchars($item['image_path']) ?>" alt="クローゼットアイテム">
                </a>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<style>
.closet-form p { font-weight: bold; margin-top: 20px; }
.radio-group, .checkbox-group { display: flex; flex-wrap: wrap; gap: 15px; margin-bottom: 10px; }
.radio-group label, .checkbox-group label { display: inline-flex; align-items: center; gap: 5px; padding: 5px 10px; background: #f0f0f0; border-radius: 5px; cursor: pointer; }
.message-box { padding: 15px; background-color: #eef7ff; border-left: 5px solid #007bff; margin: 20px 0; }
</style>

<?php include 'templates/footer.php'; ?>
