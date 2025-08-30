login_check();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // --- ステップ1: クローゼットに新しいアイテムを登録 ---
    $new_item_path = '';

    if (!empty($_FILES['item_image']['name'])) {
        $category = $_POST['category'] ?? '未分類';
        $genres = $_POST['genres'] ?? [];
        $notes = $_POST['notes'] ?? '';

        $target_dir = 'uploads/' . $user['username'] . '/closet/';
        if (!is_dir($target_dir)) mkdir($target_dir, 0777, true);

        $filename = time() . '_' . basename($_FILES["item_image"]["name"]);
        $target_file = $target_dir . $filename;

            $new_item_path = $target_file;
                'user_id' => $user['id'],
                'image_path' => $new_item_path,
                'manual_tags' => [
                ]
            ];
            $closet[] = $new_item;
            save_data('closet', $closet);
        }
    }

    // --- ステップ2: 登録したアイテムを使って投稿を作成 ---
    if (!empty($new_item_path)) {
        $posts = load_data('posts');
            'comments' => [], 'likes' => [], 'dislikes' => []
        ];
        $posts[] = $new_post;
        $error = "画像のアップロードに失敗しました。";
    }
}

<div class="container">
    <h2>新しく服を登録して投稿</h2>

        <h3>1. 新しい服の情報を登録</h3>
        <div class="closet-form">
            <p><strong>写真を選択</strong></p>
                </div>
    } else {
        $error = "画像のアップロードに失敗しました。";
    }
}

include 'templates/header.php';
?>
<div class="container">
    <h2>新しく服を登録して投稿</h2>
    <form action="<?= BASE_URL ?>/post_new_item.php" method="post" enctype="multipart/form-data">
        <?php if(isset($error)): ?><p class="error"><?= $error ?></p><?php endif; ?>

        <h3>1. 新しい服の情報を登録</h3>
        <div class="closet-form">
            <p><strong>写真を選択</strong></p>
            <input type="file" name="item_image" required>
            <p><strong>種類を選択 (必須)</strong></p>
            <div class="radio-group">
                <label><input type="radio" name="category" value="トップス" required> トップス</label>
                <label><input type="radio" name="category" value="ボトムス"> ボトムス</label>
                </div>
            <p><strong>ジャンルを選択 (複数可)</strong></p>
            <div class="checkbox-group">
                <?php foreach($genre_options as $genre): ?>
                    <label><input type="checkbox" name="genres[]" value="<?= $genre ?>"> <?= $genre ?></label>
                <?php endforeach; ?>
            </div>
            <p><strong>備考</strong></p>
            <textarea name="notes" placeholder="ブランド名、購入時期など"></textarea>
        </div>

        <hr>
        
        <h3>2. コーディネート情報を入力</h3>
        <input type="text" name="title" placeholder="コーディネートのタイトル" required><br>
        <textarea name="description" placeholder="コーディネートの説明やポイント" required></textarea><br>
        <button type="submit">登録して投稿する</button>
    </form>
</div>
<?php include 'templates/footer.php'; ?>
