<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once 'helpers.php';

$error = '';
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    // バリデーション
    if (empty($username) || empty($password)) {
        $error = "ユーザー名とパスワードを入力してください。";
    } elseif (strlen($password) < 3) {
        $error = "パスワードは3文字以上で設定してください。";
    } else {
        // DBでユーザー名が既に存在するかチェック
        if (find_user_by_username($username)) {
            $error = "このユーザー名は既に使用されています。";
        }
    }

    // エラーがなければユーザーを作成
    if (empty($error)) {
        if (create_user($username, $password)) {
            // ユーザーごとのディレクトリを作成
            $user_upload_dir = 'uploads/' . $username;
            if (!is_dir($user_upload_dir)) {
                mkdir($user_upload_dir, 0777, true);
                mkdir($user_upload_dir . '/closet', 0777, true);
                mkdir($user_upload_dir . '/posts', 0777, true);
            }
            // ログインページにリダイレクト
            header('Location: ' . BASE_URL . '/login.php?registered=success');
            exit;
        } else {
            $error = "ユーザー登録に失敗しました。";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>新規登録</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container auth-container">
        <h2>新規登録</h2>
        <?php if($error): ?><p class="error"><?= htmlspecialchars($error) ?></p><?php endif; ?>
        <form action="<?= BASE_URL ?>/register.php" method="post">
            <input type="text" name="username" placeholder="ユーザー名" required>
            <input type="password" name="password" placeholder="パスワード" required>
            <button type="submit">登録</button>
        </form>
        <p>アカウントをお持ちですか？ <a href="<?= BASE_URL ?>/login.php">ログイン</a></p>
    </div>
</body>
</html>