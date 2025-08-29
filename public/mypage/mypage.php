<?php
require_once 'helpers.php';
login_check();

$suggestion_html = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['get_suggestion'])) {
    // 自分のクローゼット情報を取得
    $all_items = load_data('closet');
    $my_items = [];
    foreach ($all_items as $item) {
        if ($item['user_id'] == $_SESSION['user_id']) {
            $my_items[] = [
                'id' => $item['id'],
                'image_path' => $item['image_path'],
                'tags' => $item['tags']
            ];
        }
    }

    if (!empty($my_items)) {
        $request_data = [
            'weather' => ['location' => '東京', 'temp' => 25, 'condition' => '晴れ'],
            'request_text' => $_POST['request_text'] ?? 'おまかせ',
            'closet_items' => $my_items
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
        $suggestion_html = '先にクローゼットにアイテムを登録してください。';
    }
}
?>

<?php include 'templates/header.php'; ?>
<div class="container">
    <h2>マイページ</h2>
    <h3>AIコーディネート提案</h3>
    <form action="mypage.php" method="post">
        <textarea name="request_text" placeholder="今日の気分やTPOを自由に入力 (例: 少し涼しい日のカジュアルなカフェ巡りコーデ)"></textarea>
        <button type="submit" name="get_suggestion">今日のファッションを提案してもらう</button>
    </form>
    
    <?php if($suggestion_html): ?>
    <div class="suggestion-box">
        <?= $suggestion_html ?>
    </div>
    <?php endif; ?>
</div>
<?php include 'templates/footer.php'; ?>
