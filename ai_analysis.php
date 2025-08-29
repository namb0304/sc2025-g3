<?php
require_once 'helpers.php';
login_check();

$message = '';
$user = get_user_by_id($_SESSION['user_id']);

// AI分析ボタンが押されたときの処理
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['run_analysis'])) {
    // 自分のアイテムだけを取得
    $my_items = [];
    $all_closet_items = load_data('closet');
    if (!empty($all_closet_items)) {
        foreach ($all_closet_items as $item) {
            if ($item['user_id'] == $user['id']) {
                $my_items[] = $item;
            }
        }
    }

    if (empty($my_items)) {
        $message = "分析するアイテムがクローゼットに登録されていません。";
    } else {
        // Pythonスクリプトに渡すためのデータを作成
        $items_to_analyze = [];
        foreach($my_items as $item) {
            $items_to_analyze[] = [
                'id' => $item['id'],
                'absolute_path' => realpath($item['image_path'])
            ];
        }

        $command = "python3 " . __DIR__ . "/cgi-bin/batch_analyze.py";
        $descriptorspec = [0 => ["pipe", "r"], 1 => ["pipe", "w"], 2 => ["pipe", "w"]];
        $process = proc_open($command, $descriptorspec, $pipes);

        if (is_resource($process)) {
            // PythonにJSONデータを渡す
            fwrite($pipes[0], json_encode($items_to_analyze));
            fclose($pipes[0]);

            // Pythonからの結果を受け取る
            $result_json = stream_get_contents($pipes[1]);
            fclose($pipes[1]);
            proc_close($process);
            
            // ユーザーフォルダに結果を保存
            $user_dir = 'uploads/' . $user['username'];
            if (!is_dir($user_dir)) {
                mkdir($user_dir, 0777, true);
            }
            file_put_contents($user_dir . '/ai_tags.json', $result_json);
            
            $message = "AI分析が完了しました！ マイページからコーディネート提案を試せます。";
        } else {
            $message = "AI分析の実行に失敗しました。";
        }
    }
}

?>
<?php include 'templates/header.php'; ?>
<div class="container">
    <h2>AIアイテム分析</h2>
    <p>このページでは、あなたのクローゼットに登録されている全てのアイテムをAIが一括で分析します。</br>
    分析結果はコーディネート提案機能で利用されます。アイテムを追加・変更した際は、再度分析を実行してください。</p>

    <?php if ($message): ?>
        <div class="message-box"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>

    <form action="<?= BASE_URL ?>/ai_analysis.php" method="post">
        <button type="submit" name="run_analysis">AI分析を実行する</button>
    </form>
</div>

<style>
/* このページ専用のスタイル */
.message-box { padding: 15px; background-color: #eef7ff; border-left: 5px solid #007bff; margin: 20px 0; }
</style>
<?php include 'templates/footer.php'; ?>
