<?php
require_once 'helpers.php';
login_check();

// header.php を使う代わりにこのファイルで完結させます
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>投稿方法の選択 - ファッション共有サイト</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* 参考デザインから持ってきた基本スタイル */
        :root {
            --primary-color: #4a6fa5;
            --secondary-color: #eef2f8;
            --text-color: #333;
            --light-text: #777;
            --border-radius: 12px;
            --box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; line-height: 1.6; color: var(--text-color); background-color: #f8f9fa; }
        a { text-decoration: none; color: inherit; }

        /* サイト共通のヘッダー */
        .header-container { max-width: 1200px; margin: 0 auto; display: flex; justify-content: space-between; align-items: center; padding: 1rem 2rem; background: white; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .logo { font-size: 1.5rem; font-weight: 700; color: var(--primary-color); }
        .nav-menu { display: flex; gap: 2rem; }
        .nav-item { font-weight: 500; }
        
        /* このページ専用のレイアウトとデザイン */
        .main-container { max-width: 900px; margin: 2rem auto; padding: 0 1rem; text-align: center; }
        .page-header { margin-bottom: 2.5rem; }
        .page-title { font-size: 2rem; font-weight: 700; color: var(--primary-color); display: inline-flex; align-items: center; gap: 0.75rem; }
        .page-subtitle { color: var(--light-text); font-size: 1.1rem; }

        .post-options-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); /* レスポンシブ対応 */
            gap: 2rem;
        }
        .option-card {
            display: flex;
            flex-direction: column;
            text-align: left;
            padding: 2rem;
            background: #fff;
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
            transition: transform 0.3s, box-shadow 0.3s;
        }
        .option-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 8px 25px rgba(74, 111, 165, 0.2);
        }
        
        .option-card .icon {
            font-size: 2.5rem;
            color: var(--primary-color);
            margin-bottom: 1rem;
            width: 60px;
            height: 60px;
            background-color: var(--secondary-color);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .option-card h3 {
            font-size: 1.5rem;
            margin-bottom: 0.5rem;
        }
        
        .option-card p {
            color: var(--light-text);
            flex-grow: 1; /* pタグを伸ばして高さを揃える */
        }

        .option-card .arrow {
            text-align: right;
            font-size: 1.2rem;
            color: var(--primary-color);
            margin-top: 1.5rem;
        }

    </style>
</head>
<body>

    <?php include 'templates/header.php'; ?>

    <div class="main-container">
        <div class="page-header">
            <h1 class="page-title"><i class="fas fa-paper-plane"></i> コーディネートを投稿する</h1>
            <p class="page-subtitle">今日のコーディネートをみんなにシェアしよう！</p>
        </div>
        
        <div class="post-options-container">
            <a href="<?= BASE_URL ?>/post_from_closet.php" class="option-card">
                <div class="icon"><i class="fas fa-warehouse"></i></div>
                <h3>デジタルクローゼットから選ぶ</h3>
                <p>登録済みのアイテムを組み合わせて、新しいコーディネートとして投稿します。</p>
                <div class="arrow"><i class="fas fa-arrow-right"></i></div>
            </a>
            
            <a href="<?= BASE_URL ?>/post_new_item.php" class="option-card">
                <div class="icon"><i class="fas fa-plus-circle"></i></div>
                <h3>新しく登録して投稿</h3>
                <p>新しい服をクローゼットに登録し、そのままコーディネートとして投稿します。</p>
                <div class="arrow"><i class="fas fa-arrow-right"></i></div>
            </a>
        </div>
    </div>

    <?php include 'templates/footer.php'; ?>

</body>
</html>