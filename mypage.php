<?php
require_once 'helpers.php';
login_check();

$suggestion_html = '';
$user = get_user_by_id($_SESSION['user_id']);
$ai_tags_file = 'uploads/' . $user['username'] . '/ai_tags.json';

// AI提案ボタンが押されたとき
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['get_suggestion'])) {

    // AI分析ファイルが存在するかチェック
    if (!file_exists($ai_tags_file)) {
        $suggestion_html = 'AIによるコーディネート提案を利用するには、先に「AIアイテム分析」ページで分析を実行してください。';
    } else {
        $ai_tags_data = json_decode(file_get_contents($ai_tags_file), true);
        $all_my_items = [];
        $closet = load_data('closet');
        foreach ($closet as $item) {
            if ($item['user_id'] == $_SESSION['user_id']) {
                $all_my_items[$item['id']] = $item; // IDをキーにして連想配列化
            }
        }

        // AIタグとアイテム情報を結合
        $items_for_suggestion = [];
        foreach ($ai_tags_data as $item_id => $tags) {
            if (isset($all_my_items[$item_id])) {
                $items_for_suggestion[] = [
                    'id' => $item_id,
                    'image_path' => $all_my_items[$item_id]['image_path'],
                    'ai_tags' => $tags // AIによる分析結果
                ];
            }
        }

        if (!empty($items_for_suggestion)) {
            $request_data = [
                'weather' => ['location' => '東京', 'temp' => 25, 'condition' => '晴れ'],
                'request_text' => $_POST['request_text'] ?? 'おまかせ',
                'closet_items' => $items_for_suggestion
            ];
            
            $command = "python3 " . __DIR__ . "/cgi-bin/suggest_coord.py";
            $descriptorspec = [ 0 => ["pipe", "r"], 1 => ["pipe", "w"], 2 => ["pipe", "w"] ];
            $process = proc_open($command, $descriptorspec, $pipes);

            if (is_resource($process)) {
                fwrite($pipes[0], json_encode($request_data));
                fclose($pipes[0]);
                $suggestion_json = stream_get_contents($pipes[1]);
                fclose($pipes[1]);
                proc_close($process);

                $suggestion_data = json_decode($suggestion_json, true);
                if (isset($suggestion_data['reason'])) {
                    $suggestion_html = '<h4>AIからの提案</h4>';
                    $suggestion_html .= '<p><strong>理由：</strong>' . htmlspecialchars($suggestion_data['reason']) . '</p>';
                    $suggestion_html .= '<div class="closet-grid">';
                    foreach($suggestion_data['items'] as $item_path) {
                        $suggestion_html .= '<div class="closet-item"><img src="'.htmlspecialchars($item_path).'"></div>';
                    }
                    $suggestion_html .= '</div>';
                } else {
                    $suggestion_html = 'AIからの提案取得に失敗しました。';
                }
            }
        } else {
            $suggestion_html = '提案できるアイテムが見つかりませんでした。AI分析を再度実行してみてください。';
        }
    }
}
?>

<?php include 'templates/header.php'; ?>
<div class="container">
    <h2>マイページ</h2>

    <div class="mypage-menu">
        <a href="<?= BASE_URL ?>/ai_analysis.php" class="menu-button">AIアイテム分析</a>
        <p>クローゼットのアイテムをAIに分析させ、コーディネート提案の準備をします。</p>
    </div>

    <hr>
    
    <h3>AIコーディネート提案</h3>
    <p>AI分析を実行すると、あなたのクローゼットの中身と今日の気分からAIがコーディネートを提案します。</p>
    <form action="<?= BASE_URL ?>/mypage.php" method="post">
        <textarea name="request_text" placeholder="今日の気分やTPOを自由に入力 (例: 少し涼しい日のカジュアルなカフェ巡りコーデ)"></textarea>
        <button type="submit" name="get_suggestion">今日のファッションを提案してもらう</button>
    </form>
    
    <?php if($suggestion_html): ?>
    <div class="suggestion-box">
        <?= $suggestion_html ?>
    </div>
    <?php endif; ?>
</div>

<style>
/* このページ専用のスタイル */
.mypage-menu { background: #f9f9f9; padding: 20px; border-radius: 8px; margin-bottom: 20px; }
.menu-button { background-color: #28a745; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; font-weight: bold; display: inline-block; }
.menu-button:hover { background-color: #218838; }
.mypage-menu p { margin-top: 10px; color: #555; }
</style>
<?php include 'templates/footer.php'; ?>
