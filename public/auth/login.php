<?php
require_once __DIR__ . '/../../src/helpers.php';

$error = '';
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $users = load_data('users');
    $username = $_POST['username'];
    $password = $_POST['password'];

    foreach ($users as $user) {
        if ($user['username'] === $username && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            header("Location: home.php");
            exit;
        }
    }
    $error = "ユーザー名またはパスワードが正しくありません。";
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>ログイン</title>
    <link rel="stylesheet" href="/public/style.css">
</head>
<body>
    <div class="container auth-container">
        <h2>ログイン</h2>
        <?php if($error): ?><p class="error"><?= $error ?></p><?php endif; ?>
        <form action="login.php" method="post">
            <input type="text" name="username" placeholder="ユーザー名" required>
            <input type="password" name="password" placeholder="パスワード" required>
            <button type="submit">ログイン</button>
        </form>
        <p>アカウントがありませんか？ <a href="register.php">新規登録</a></p>
    </div>
</body>
</html>
