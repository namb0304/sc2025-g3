<?php
require_once 'helpers.php';
login_check();

$message = '';
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES["item_image"])) {
    $user = get_user_by_id($_SESSION['user_id']);
    $closet = load_data('closet');
    
    $target_dir = 'uploads/' . $user['username'] . '/closet/';
    $filename = time() . '_' . basename($_FILES["item_image"]["name"]);
    $target_file = $target_dir . $filename;

    if (move_uploaded_file($_FILES["item_image"]["tmp_name"], $target_file)) {
        // AI分析CGIを呼び出す (サーバーの絶対パスを指定)
        $absolute_path = realpath($target_file);
        $command = "python3 " . __DIR__ . "/cgi-bin/analyze_fashion.py " . escapeshellarg($absolute_path);
        $ai_result_json = shell_exec($command);
        $ai_tags = json_decode($ai_result_json, true);

        $new_item = [
            'id' => uniqid(),
            'user_id' => $user['id'],
            'image_path' => '/' . $target_file,
            'tags' => $ai_tags
        ];
        $closet[] = $new_item;
        save_data('closet', $closet);
        $message = "アイテムが登録されました。";
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

<?php include 'templates/header.php'; ?>
<div class="container">
    <h2>マイクローゼット</h2>
    <h3>アイテムを登録</h3>
    <?php if($message): ?><p><?= $message ?></p><?php endif; ?>
    <form action="closet.php" method="post" enctype="multipart/form-data">
        <input type="file" name="item_image" required>
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
<?php include 'templates/footer.php'; ?>
