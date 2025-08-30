<?php
// ▼▼▼ デバッグのためにこの2行を追加 ▼▼▼
ini_set('display_errors', 1);
error_reporting(E_ALL);
// ▲▲▲ ここまで ▲▲▲

require_once 'helpers.php';
login_check();

$user_id = $_SESSION['user_id'];
$user = get_user_by_id($user_id);

// --- フォーム送信(POST)の処理 ---
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES["item_image"])) {
    
    // --- ① 先にPHPのアップロードエラーをチェック ---
    $upload_error = $_FILES['item_image']['error'];
    if ($upload_error !== UPLOAD_ERR_OK) {
        $error_messages = [
            UPLOAD_ERR_INI_SIZE   => 'ファイルサイズがサーバーの上限(php.ini)を超えています。',
            UPLOAD_ERR_FORM_SIZE  => 'ファイルサイズがフォームの上限を超えています。',
            UPLOAD_ERR_PARTIAL    => 'ファイルが部分的にしかアップロードされませんでした。',
            UPLOAD_ERR_NO_FILE    => 'ファイルが選択されていません。',
            UPLOAD_ERR_NO_TMP_DIR => 'サーバーに一時保存フォルダがありません。',
            UPLOAD_ERR_CANT_WRITE => 'サーバーへのファイル書き込みに失敗しました。パーミッションを確認してください。',
            UPLOAD_ERR_EXTENSION  => 'PHPの拡張機能によりアップロードが中断されました。',
        ];
        $_SESSION['message'] = "アップロードエラー: " . ($error_messages[$upload_error] ?? '不明なエラーです。');
        header('Location: ' . BASE_URL . '/closet.php');
        exit;
    }

    // --- ② ①でエラーがなければ、DBへの保存処理 ---
    $image_data = file_get_contents($_FILES['item_image']['tmp_name']);
    $mime_type = $_FILES['item_image']['type'];

    if (create_closet_item_in_db(
        $user_id,
        $image_data,
        $mime_type,
        $_POST['category'] ?? '未分類',
        $_POST['genres'] ?? [],
        htmlspecialchars($_POST['notes'] ?? '')
    )) {
        $_SESSION['message'] = "アイテムがデータベースに登録されました。";
    } else {
        $_SESSION['message'] = "データベースへの登録に失敗しました。";
    }
    
    header('Location: ' . BASE_URL . '/closet.php');
    exit;
}

// --- ページ表示(GET)の処理 ---
$message = $_SESSION['message'] ?? '';
unset($_SESSION['message']);
$genre_options = ['カジュアル', 'きれいめ', 'ストリート', 'フェミニン', 'モード', 'オフィス', 'アウトドア'];
$my_closet_items = get_closet_items_by_user_id($user_id);

include 'templates/header.php';
?>
<div class="container">
    <h2>マイクローゼット</h2>
    <h3>アイテムを登録</h3>
    <?php if($message): ?><p class="message-box"><?= htmlspecialchars($message) ?></p><?php endif; ?>
    <form class="closet-form" action="<?= BASE_URL ?>/closet.php" method="post" enctype="multipart/form-data">
        <p><strong>1. 写真を選択 (必須)</strong></p>
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
        <?php if (empty($my_closet_items)): ?>
            <p>まだアイテムが登録されていません。</p>
        <?php else: ?>
            <?php foreach ($my_closet_items as $item): ?>
                <div class="closet-item">
                    <a href="<?= BASE_URL ?>/closet_detail.php?id=<?= $item['id'] ?>">
                        <img src="image.php?id=<?= $item['id'] ?>" alt="<?= htmlspecialchars($item['category']) ?>">
                    </a>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<style>
.closet-form p { font-weight: bold; margin-top: 20px; }
.radio-group, .checkbox-group { display: flex; flex-wrap: wrap; gap: 15px; margin-bottom: 10px; }
.radio-group label, .checkbox-group label { display: inline-flex; align-items: center; gap: 5px; padding: 5px 10px; background: #f0f0f0; border-radius: 5px; cursor: pointer; }
.message-box { padding: 15px; background-color: #eef7ff; border-left: 5px solid #007bff; margin: 20px 0; }
</style>

<?php include 'templates/footer.php'; ?>