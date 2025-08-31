<?php
require_once 'helpers.php';
login_check();

$user_id = $_SESSION['user_id'];
$user = get_user_by_id($user_id);
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $new_username = trim($_POST['username'] ?? '');
    if (empty($new_username)) {
        $error = "ユーザー名を入力してください。";
    } elseif ($new_username === $user['username']) {
        $error = "ユーザー名が変更されていません。";
    } else {
        $result = update_user_profile($user_id, $new_username);
        if ($result === true) {
            $success = "プロフィールが更新されました！";
            // ユーザー情報を再取得してフォームに反映
            $user = get_user_by_id($user_id);
        } else {
            $error = $result;
        }
    }
}

include 'templates/header.php';
?>
<div class="container">
    <h2>プロフィールの編集</h2>
    <?php if ($error): ?><p class="error"><?= htmlspecialchars($error) ?></p><?php endif; ?>
    <?php if ($success): ?><p class="message-box"><?= htmlspecialchars($success) ?></p><?php endif; ?>

    <form action="edit_profile.php" method="post">
        <div class="form-row">
            <label for="username">ユーザー名</label>
            <input type="text" id="username" name="username" value="<?= htmlspecialchars($user['username']) ?>" required>
        </div>
        <button type="submit">更新する</button>
    </form>
    <br>
    <a href="mypage.php">マイページに戻る</a>
</div>
<?php include 'templates/footer.php'; ?>