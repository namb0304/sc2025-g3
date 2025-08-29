<?php
// sc2025-g3/public/mypage/closet.php

// ★ 変更点: パスを修正
require_once __DIR__ . '/../src/helpers.php';
login_check();

$message = '';
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES["item_image"])) {
    $user = get_user_by_id($_SESSION['user_id']);
    
    // ★ 変更点: uploadsディレクトリのパスを修正 (Webサーバーのルートからのパス)
    $target_dir_web = '/uploads/' . $user['username'] . '/closet/';
    // ★ 変更点: ファイル保存用の絶対パスを定義
    $target_dir_fs = $_SERVER['DOCUMENT_ROOT'] . $target_dir_web;

    if (!is_dir($target_dir_fs)) {
        mkdir($target_dir_fs, 0777, true);
    }
    
    $filename = time() . '_' . basename($_FILES["item_image"]["name"]);
    $target_file_fs = $target_dir_fs . $filename;

    if (move_uploaded_file($_FILES["item_image"]["tmp_name"], $target_file_fs)) {
        // ★ 変更点: Pythonスクリプトへのパスを修正 & エラー出力を追加 `2>&1`
        $command = "python3 " . __DIR__ . "/../../cgi-bin/analyze_fashion.py " . escapeshellarg($target_file_fs) . " 2>&1";
        $ai_result_json = shell_exec($command);
        $ai_tags = json_decode($ai_result_json, true);

        // ★ 変更点: AI分析が成功したかチェック
        if ($ai_tags === null || isset($ai_tags['error'])) {
            $message = "AI分析に失敗しました。";
            // デバッグ用にエラー内容をログに出力すると便利
            // error_log("AI analysis error: " . $ai_result_json);
        } else {
            $closet = load_data('closet');
            $new_item = [
                'id' => uniqid(),
                'user_id' => $user['id'],
                'image_path' => $target_dir_web . $filename, // Web表示用のパス
                'tags' => $ai_tags
            ];
            $closet[] = $new_item;
            save_data('closet', $closet);
            $message = "アイテムが登録されました。";
        }
    } else {
        $message = "ファイルのアップロードに失敗しました。";
    }
}

// 自分のアイテムのみを取得
$my_closet_items = [];
$all_closet_items = load_data('closet');
foreach ($all_closet_items as $item) {
    if ($item['user_id'] == $_SESSION['user_id']) {
        $my_closet_items[] = $item;
    }
}
?>

<?php include __DIR__ . '/../../src/templates/header.php'; // ★ 変更点: パスを修正 ?>
<div class="container">
    <h2>マイクローゼット</h2>
    <h3>アイテムを登録</h3>
    <?php if($message): ?><p><?= htmlspecialchars($message) ?></p><?php endif; ?>
    <form action="/mypage/closet.php" method="post" enctype="multipart/form-data"> <input type="file" name="item_image" required>
        <button type="submit">アップロードしてAI分析</button>
    </form>
    <hr>
    <h3>登録済みアイテム</h3>
    <div class="closet-grid">
        <?php foreach (array_reverse($my_closet_items) as $item): ?>
            <div class="closet-item">
                <img src="<?= htmlspecialchars($item['image_path']) ?>" alt="クローゼットアイテム">
            </div>
        <?php endforeach; ?>
    </div>
</div>
<?php include __DIR__ . '/../../src/templates/footer.php'; // ★ 変更点: パスを修正 ?>