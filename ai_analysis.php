<?php
require_once 'helpers.php';
login_check();

$message = '';
$user_id = $_SESSION['user_id'];
$user = get_user_by_id($user_id);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['run_analysis'])) {
    $my_items = get_closet_items_by_user_id($user_id);

    if (empty($my_items)) {
        $message = "分析するアイテムが登録されていません。";
    } else {
        $items_to_analyze = array_map(function($item) {
            return ['id' => $item['id'], 'absolute_path' => realpath($item['image_path'])];
        }, $my_items);

        // (Pythonスクリプトの呼び出し部分は同じ)
        // ... $result_json にPythonからの結果が入ると仮定 ...
        
        $analysis_results = json_decode($result_json, true);

        if ($analysis_results && !isset($analysis_results['error'])) {
            $pdo = get_db_connection();
            // 各アイテムの分析結果をDBに保存
            foreach ($analysis_results as $item_id => $tags) {
                $stmt = $pdo->prepare("UPDATE closet_items SET ai_tags = ? WHERE id = ? AND user_id = ?");
                $stmt->execute([json_encode($tags), $item_id, $user_id]);
            }
            $message = "AI分析が完了しました！";
        } else {
            $message = "AI分析の実行に失敗しました。";
        }
    }
}
?>
<?php include 'templates/header.php'; ?>
<div class="container">
    <h2>AIアイテム分析</h2>
    <?php if ($message): ?><p class="message-box"><?= htmlspecialchars($message) ?></p><?php endif; ?>
    <form action="<?= BASE_URL ?>/ai_analysis.php" method="post">
        <button type="submit" name="run_analysis">AI分析を実行する</button>
    </form>
</div>
<?php include 'templates/footer.php'; ?>