<?php
require_once 'helpers.php';
login_check();
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>投稿方法の選択 - ファッション共有サイト</title>
    </head>
<body>

    <?php include 'templates/header.php'; ?>

    <style>
        /* post.php 専用のスタイル */
        .main-container { max-width: 900px; margin: 2rem auto; padding: 0 1rem; text-align: center; }
        .page-header { margin-bottom: 2.5rem; }
        .page-title { font-size: 2rem; font-weight: 700; color: #4a6fa5; display: inline-flex; align-items: center; gap: 0.75rem; }
        .page-subtitle { color: #777; font-size: 1.1rem; }

        .post-options-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2rem;
        }
        .option-card {
            display: flex;
            flex-direction: column;
            text-align: left;
            padding: 2rem;
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            transition: transform 0.3s, box-shadow 0.3s;
            text-decoration: none;
            color: inherit;
        }
        .option-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 8px 25px rgba(74, 111, 165, 0.2);
        }
        
        .option-card .icon {
            font-size: 2.5rem;
            color: #4a6fa5;
            margin-bottom: 1rem;
            width: 60px;
            height: 60px;
            background-color: #eef2f8;
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
            color: #777;
            flex-grow: 1;
        }

        .option-card .arrow {
            text-align: right;
            font-size: 1.2rem;
            color: #4a6fa5;
            margin-top: 1.5rem;
        }
    </style>
    
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