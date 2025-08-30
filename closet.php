<?php
// ▼▼▼ デバッグのためにこの2行を追加 ▼▼▼
ini_set('display_errors', 1);
error_reporting(E_ALL);
// ▲▲▲ ここまで ▲▲▲

require_once 'helpers.php';
login_check();

$user_id = $_SESSION['user_id'];
$user = get_user_by_id($user_id);

// --- フォーム送信(POST)の処理 ---
// このPHPロジックは一切変更していません
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES["item_image"])) {
    
    $upload_error = $_FILES['item_image']['error'];
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
        $_SESSION['message'] = "アップロードエラー: " . ($error_messages[$upload_error] ?? '不明なエラーです。');
        header('Location: ' . BASE_URL . '/mypage.php?tab=closet');
        exit;
    }

    $image_data = file_get_contents($_FILES['item_image']['tmp_name']);
    $mime_type = $_FILES['item_image']['type'];

    if (create_closet_item_in_db(
        $user_id,
        $image_data,
        $mime_type,
        $_POST['category'] ?? '未分類',
        $_POST['genres'] ?? [],
        htmlspecialchars($_POST['notes'] ?? '')
    )) {
        $_SESSION['message'] = "アイテムがデータベースに登録されました。";
    } else {
        $_SESSION['message'] = "データベースへの登録に失敗しました。";
    }

    header('Location: ' . BASE_URL . '/mypage.php?tab=closet');
    exit;
}

// --- ページ表示(GET)の処理 ---
$message = $_SESSION['message'] ?? '';
unset($_SESSION['message']);
$genre_options = ['カジュアル', 'きれいめ', 'ストリート', 'フェミニン', 'モード', 'オフィス', 'アウトドア'];
$my_closet_items = get_closet_items_by_user_id($user_id);

// header.php や footer.php を使う代わりに、このファイル内で完結させます
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>マイクローゼット - ファッション共有サイト</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* 先ほど解説したモダンなデザインのCSS */
        :root {
            --primary-color: #4a6fa5;
            --secondary-color: #eef2f8;
            --accent-color: #ff6b6b;
            --text-color: #333;
            --light-text: #777;
            --border-radius: 12px;
            --box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            --purple-color: #6c5ce7;
            --purple-light: #f1effe;
        }
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; line-height: 1.6; color: var(--text-color); background-color: #f8f9fa; }
        
        /* ヘッダー */
        header { background-color: white; box-shadow: 0 2px 10px rgba(0,0,0,0.1); position: sticky; top: 0; z-index: 100; }
        .header-container { max-width: 1200px; margin: 0 auto; display: flex; justify-content: space-between; align-items: center; padding: 1rem 2rem; }
        .logo { font-size: 1.5rem; font-weight: 700; color: var(--primary-color); text-decoration: none; }
        .nav-menu { display: flex; gap: 2rem; }
        .nav-item { text-decoration: none; color: var(--text-color); font-weight: 500; padding: 0.5rem; transition: color 0.3s; }
        
        /* メインコンテンツ */
        .main-container { max-width: 1200px; margin: 2rem auto; padding: 0 1rem; }
        .page-title { font-size: 2rem; font-weight: 700; color: var(--purple-color); display: flex; align-items: center; gap: 0.5rem; margin-bottom: 1.5rem; }
        
        /* フォーム */
        .form-container { background-color: white; border-radius: var(--border-radius); box-shadow: var(--box-shadow); padding: 2rem; max-width: 800px; margin: 2rem auto; }
        .form-header { display: flex; align-items: center; margin-bottom: 1.5rem; padding-bottom: 1rem; border-bottom: 1px solid #eee; color: var(--purple-color); font-size: 1.5rem; font-weight: 600; gap: 0.5rem; }
        .form-row { margin-bottom: 1.5rem; }
        .label { display: block; margin-bottom: 0.5rem; font-weight: 600; color: var(--text-color); }
        .select, textarea { width: 100%; padding: 0.8rem 1rem; border: 1px solid #ddd; border-radius: 6px; font-size: 1rem; transition: border-color 0.3s, box-shadow 0.3s; }
        .select:focus, textarea:focus { outline: none; border-color: var(--purple-color); box-shadow: 0 0 0 2px rgba(108, 92, 231, 0.1); }
        
        /* 画像プレビュー */
        .image-preview { width: 100%; height: 280px; border: 2px dashed #ddd; border-radius: var(--border-radius); display: flex; align-items: center; justify-content: center; overflow: hidden; transition: border-color 0.3s; cursor: pointer; }
        .image-preview:hover { border-color: var(--purple-color); }
        .image-preview img { max-width: 100%; max-height: 100%; display: none; }
        .image-placeholder { text-align: center; color: var(--light-text); }
        .image-placeholder i { font-size: 3rem; margin-bottom: 1rem; }
        
        /* チェックボックス */
        .checkbox-group { display: flex; flex-wrap: wrap; gap: 15px; margin-bottom: 10px; }
        .checkbox-group label { display: inline-flex; align-items: center; gap: 5px; padding: 5px 10px; background: #f0f0f0; border-radius: 5px; cursor: pointer; }

        /* ボタン */
        .btn-purple { background-color: var(--purple-color); color: white; border: none; width: 100%; padding: 0.8rem 1.5rem; border-radius: 6px; font-size: 1rem; cursor: pointer; transition: background-color 0.3s; font-weight: 500; display: inline-flex; align-items: center; justify-content: center; gap: 0.5rem; }
        .btn-purple:hover { background-color: #5549c0; }

        /* 登録済みアイテム */
        .closet-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(180px, 1fr)); gap: 1.5rem; }
        .closet-item a { display: block; border-radius: var(--border-radius); overflow: hidden; box-shadow: var(--box-shadow); transition: transform 0.3s, box-shadow 0.3s; }
        .closet-item a:hover { transform: scale(1.05); }
        .closet-item img { width: 100%; height: 100%; object-fit: cover; aspect-ratio: 1 / 1.2; display: block; }
        .message-box { padding: 15px; background-color: var(--purple-light); border-left: 5px solid var(--purple-color); margin: 20px 0; border-radius: 5px; font-weight: 500; }
        hr { border: none; border-top: 1px solid #eee; margin: 3rem 0; }
    </style>
</head>
<body>
    <header>
        <div class="header-container">
            <a href="<?= BASE_URL ?>/index.php" class="logo">ファッション共有サイト</a>
            <nav class="nav-menu">
                <a href="<?= BASE_URL ?>/index.php" class="nav-item">みんなの投稿</a>
                <a href="<?= BASE_URL ?>/mypage.php" class="nav-item">マイページ</a>
            </nav>
        </div>
    </header>
    
    <div class="main-container">
        <h1 class="page-title"><i class="fas fa-tshirt"></i> マイクローゼット</h1>

        <?php if($message): ?>
            <p class="message-box"><?= htmlspecialchars($message) ?></p>
        <?php endif; ?>

        <div class="form-container">
            <div class="form-header"><i class="fas fa-plus-circle"></i> アイテム情報を入力</div>
            
            <form action="<?= BASE_URL ?>/closet.php" method="post" enctype="multipart/form-data">
                
                <div class="form-row">
                    <label for="itemImage" class="label">写真 (必須)</label>
                    <div class="image-preview" id="imagePreview">
                        <img src="" alt="画像プレビュー" id="previewImage">
                        <div class="image-placeholder" id="imagePlaceholder">
                            <i class="fas fa-camera"></i>
                            <p>クリックして画像を選択</p>
                        </div>
                    </div>
                    <input type="file" id="itemImage" name="item_image" accept="image/*" required style="display: none;">
                </div>
                
                <div class="form-row">
                    <label for="category" class="label">カテゴリー (必須)</label>
                    <select id="category" name="category" class="select" required>
                        <option value="" disabled selected>カテゴリーを選択</option>
                        <option value="トップス">トップス</option>
                        <option value="ボトムス">ボトムス</option>
                        <option value="アウター">アウター</option>
                        <option value="ワンピース">ワンピース</option>
                        <option value="シューズ">シューズ</option>
                        <option value="バッグ">バッグ</option>
                        <option value="その他">その他</option>
                    </select>
                </div>
                
                <div class="form-row">
                    <label class="label">ジャンル (複数可)</label>
                    <div class="checkbox-group">
                        <?php foreach($genre_options as $genre): ?>
                            <label><input type="checkbox" name="genres[]" value="<?= $genre ?>"> <?= $genre ?></label>
                        <?php endforeach; ?>
                    </div>
                </div>

                <div class="form-row">
                    <label for="notes" class="label">備考</label>
                    <textarea id="notes" name="notes" placeholder="ブランド名、購入時期、素材など自由に入力できます"></textarea>
                </div>
                
                <div class="form-row">
                    <button type="submit" class="btn-purple">
                        <i class="fas fa-save"></i> クローゼットに登録
                    </button>
                </div>
            </form>
        </div>

        <hr>

        <h3>登録済みアイテム</h3>
        <div class="closet-grid">
            <?php if (empty($my_closet_items)): ?>
                <p>まだアイテムが登録されていません。</p>
            <?php else: ?>
                <?php foreach ($my_closet_items as $item): ?>
                    <div class="closet-item">
                        <a href="<?= BASE_URL ?>/closet_detail.php?id=<?= $item['id'] ?>">
                            <img src="image.php?id=<?= $item['id'] ?>" alt="<?= htmlspecialchars($item['category']) ?>">
                        </a>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // 画像プレビュー領域クリックでファイル選択を起動
            const imagePreview = document.getElementById('imagePreview');
            const itemImage = document.getElementById('itemImage');
            
            imagePreview.addEventListener('click', function() {
                itemImage.click();
            });
            
            // 写真のプレビュー表示
            itemImage.addEventListener('change', function(e) {
                const preview = document.getElementById('previewImage');
                const placeholder = document.getElementById('imagePlaceholder');
                const file = e.target.files[0];
                
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        preview.src = e.target.result;
                        preview.style.display = 'block';
                        placeholder.style.display = 'none';
                    };
                    reader.readAsDataURL(file);
                }
            });
        });
    </script>
</body>
</html>