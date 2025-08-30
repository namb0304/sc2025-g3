<?php
require_once 'helpers.php';
login_check();

$user_id = $_SESSION['user_id'];
$genre_options = ['カジュアル', 'きれいめ', 'ストリート', 'フェミニン', 'モード', 'オフィス', 'アウトドア'];
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // --- ① 先にPHPのアップロードエラーをチェック ---
    $upload_error = $_FILES['item_image']['error'] ?? UPLOAD_ERR_NO_FILE;
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
        $error = "アップロードエラー: " . ($error_messages[$upload_error] ?? '不明なエラーです。');
    } else {
        // --- ② エラーがなければ、トランザクションを開始してDB処理 ---
        $pdo = get_db_connection();
        try {
            $pdo->beginTransaction();

            // ステップ1: 画像データを読み込み、クローゼットに登録
            $image_data = file_get_contents($_FILES['item_image']['tmp_name']);
            $mime_type = $_FILES['item_image']['type'];

            $new_item_id = create_closet_item_in_db(
                $user_id,
                $image_data,
                $mime_type,
                $_POST['category'] ?? '未分類',
                $_POST['genres'] ?? [],
                htmlspecialchars($_POST['notes'] ?? '')
            );

            if (!$new_item_id) {
                throw new Exception("クローゼットアイテムの作成に失敗しました。");
            }
            
            // ステップ2: 登録したアイテムを使って投稿を作成
            if (!create_post(
                $user_id,
                htmlspecialchars($_POST['title']),
                htmlspecialchars($_POST['description']),
                $new_item_id
            )) {
                throw new Exception("投稿の作成に失敗しました。");
            }

            // 全て成功したら、トランザクションを確定
            $pdo->commit();

            header('Location: ' . BASE_URL . '/index.php');
            exit;

        } catch (Exception $e) {
            // 何か一つでも失敗したら、全ての変更を取り消す
            $pdo->rollBack();
            $error = "エラーが発生しました: " . $e->getMessage();
        }
    }
}

include 'templates/header.php';
?>
<div class="container">
    <h2>新しく服を登録して投稿</h2>
    <form action="<?= BASE_URL ?>/post_new_item.php" method="post" enctype="multipart/form-data">
        <?php if(!empty($error)): ?><p class="error"><?= htmlspecialchars($error) ?></p><?php endif; ?>

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