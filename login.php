<?php
require_once 'helpers.php';

$error = '';
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $user = find_user_by_username($username);

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        header('Location: ' . BASE_URL . '/index.php');
        exit;
    } else {
        $error = "ユーザー名またはパスワードが正しくありません。";
    }
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>ログイン</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container auth-container">
        <h2>ログイン</h2>
        <?php if($error): ?><p class="error"><?= htmlspecialchars($error) ?></p><?php endif; ?>
        <?php if(isset($_GET['registered'])): ?><p class="message-box">ユーザー登録が完了しました。ログインしてください。</p><?php endif; ?>
        <form action="<?= BASE_URL ?>/login.php" method="post">
            <input type="text" name="username" placeholder="ユーザー名" required>
            <input type="password" name="password" placeholder="パスワード" required>
            <button type="submit">ログイン</button>
        </form>
        <p>アカウントがありませんか？ <a href="<?= BASE_URL ?>/register.php">新規登録</a></p>
    </div>
</body>
</html>