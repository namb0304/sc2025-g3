<?php
require_once __DIR__ . '/../../src/helpers.php';
login_check();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $posts = load_data('posts');
    $user = get_user_by_id($_SESSION['user_id']);
    
    $title = htmlspecialchars($_POST['title']);
    $description = htmlspecialchars($_POST['description']);
    $post_image_path = '';

    if (!empty($_FILES['post_image']['name'])) {
        $target_dir = 'uploads/' . $user['username'] . '/posts/';
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0777, true);
        }
        $filename = time() . '_' . basename($_FILES['post_image']['name']);
        $target_file = $target_dir . $filename;
        if (move_uploaded_file($_FILES['post_image']['tmp_name'], $target_file)) {
            $post_image_path = '/' . $target_file;
        }
    }

    if (!empty($post_image_path)) {
        $new_post = [
            'id' => uniqid(),
            'user_id' => $user['id'],
            'username' => $user['username'],
            'title' => $title,
            'description' => $description,
            'post_image' => $post_image_path,
            'comments' => [],
            'likes' => 0
        ];
        $posts[] = $new_post;
        save_data('posts', $posts);
        header('Location: index.php');
        exit;
    }
}
?>

<?php include '/src/templates/header.php'; ?>
<div class="container">
    <h2>コーディネートを投稿する</h2>
    <form action="post.php" method="post" enctype="multipart/form-data">
        <input type="text" name="title" placeholder="タイトル" required><br>
        <textarea name="description" placeholder="コーディネートの説明" required></textarea><br>
        <label>メイン画像 (必須):</label>
        <input type="file" name="post_image" required><br>
        <button type="submit">投稿する</button>
    </form>
</div>
<?php include '/src/templates/footer.php'; ?>
