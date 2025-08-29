<?php
require_once 'helpers.php';

$error = '';
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $users = load_data('users');
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    if (!empty($users)) {
        foreach ($users as $user) {
            if ($user['username'] === $username) {
                $error = "このユーザー名は既に使用されています。";
            }
        }
    }

    if (empty($error)) {
        $new_user = [
            'id' => empty($users) ? 1 : max(array_column($users, 'id')) + 1,
            'username' => $username,
            'password' => password_hash($password, PASSWORD_DEFAULT)
        ];
        $users[] = $new_user;
        save_data('users', $users);

        $user_upload_dir = 'uploads/' . $username;
        if (!is_dir($user_upload_dir)) {
            mkdir($user_upload_dir, 0777, true);
            mkdir($user_upload_dir . '/closet', 0777, true);
            mkdir($user_upload_dir . '/posts', 0777, true);
        }
        header('Location: ' . BASE_URL . '/login.php');
        exit;
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
