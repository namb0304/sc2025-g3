<?php
// 設定ファイルとDB接続ファイルを読み込む
// (これらのファイル名はあなたの環境に合わせてください)
require_once __DIR__ . '/config.php'; 
require_once __DIR__ . '/db.php';

// セッションを開始
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// --- ユーザー (fashion_users) 関連 ---

function find_user_by_username($username) {
    $pdo = get_db_connection();
    // ★★★ 修正点 ★★★
    $stmt = $pdo->prepare('SELECT * FROM fashion_users WHERE username = ?');
    $stmt->execute([$username]);
    return $stmt->fetch();
}

function get_user_by_id($id) {
    if (!$id) return null;
    $pdo = get_db_connection();
    // ★★★ 修正点 ★★★
    $stmt = $pdo->prepare('SELECT id, username FROM fashion_users WHERE id = ?');
    $stmt->execute([$id]);
    return $stmt->fetch();
}

function create_user($username, $password) {
    $pdo = get_db_connection();
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    // ★★★ 修正点 ★★★
    $stmt = $pdo->prepare('INSERT INTO fashion_users (username, password) VALUES (?, ?)');
    return $stmt->execute([$username, $hashed_password]);
}

// --- クローゼット (closet_items) 関連 ---

function get_closet_items_by_user_id($user_id) {
    $pdo = get_db_connection();
    $stmt = $pdo->prepare('SELECT id, user_id, category, genres, notes, created_at, mime_type FROM closet_items WHERE user_id = ? ORDER BY created_at DESC');
    $stmt->execute([$user_id]);
    return $stmt->fetchAll();
}

function find_closet_item_by_id($item_id, $user_id = null) {
    $pdo = get_db_connection();
    $sql = 'SELECT id, user_id, category, genres, notes, created_at, mime_type FROM closet_items WHERE id = ?';
    $params = [$item_id];
    if ($user_id) {
        $sql .= ' AND user_id = ?';
        $params[] = $user_id;
    }
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetch();
}

function create_closet_item_in_db($user_id, $image_data, $mime_type, $category, $genres, $notes) {
    $pdo = get_db_connection();
    $genres_pg_array = '{' . implode(',', array_map('trim', $genres)) . '}';
    $stmt = $pdo->prepare(
        'INSERT INTO closet_items (user_id, image_data, mime_type, category, genres, notes) VALUES (?, ?, ?, ?, ?, ?)'
    );
    $stmt->bindParam(1, $user_id);
    $stmt->bindParam(2, $image_data, PDO::PARAM_LOB);
    $stmt->bindParam(3, $mime_type);
    $stmt->bindParam(4, $category);
    $stmt->bindParam(5, $genres_pg_array);
    $stmt->bindParam(6, $notes);
    
    return $stmt->execute();
}

function get_image_data_by_item_id($item_id) {
    $pdo = get_db_connection();
    $stmt = $pdo->prepare('SELECT image_data, mime_type FROM closet_items WHERE id = ?');
    $stmt->execute([$item_id]);
    
    $result = $stmt->fetch();
    
    if ($result && is_resource($result['image_data'])) {
        $result['image_data'] = stream_get_contents($result['image_data']);
    }
    
    return $result;
}

function delete_closet_item($item_id, $user_id) {
    $pdo = get_db_connection();
    $stmt = $pdo->prepare('DELETE FROM closet_items WHERE id = ? AND user_id = ?');
    return $stmt->execute([$item_id, $user_id]);
}

// --- 投稿 (posts) 関連 ---

function create_post($user_id, $title, $description, $image_path, $closet_item_id = null) {
    $pdo = get_db_connection();
    $stmt = $pdo->prepare(
        'INSERT INTO posts (user_id, title, description, post_image, closet_item_id) VALUES (?, ?, ?, ?, ?)'
    );
    return $stmt->execute([$user_id, $title, $description, $image_path, $closet_item_id]);
}

function get_all_posts($search_query = '') {
    $pdo = get_db_connection();
    // ★★★ 修正点 ★★★
    $sql = "SELECT p.*, u.username FROM posts p JOIN fashion_users u ON p.user_id = u.id";
    $params = [];
    if (!empty($search_query)) {
        $sql .= " WHERE p.title ILIKE ? OR u.username ILIKE ?";
        $params = ['%' . $search_query . '%', '%' . $search_query . '%'];
    }
    $sql .= " ORDER BY p.created_at DESC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll();
}

function get_posts_by_user_id($user_id) {
    $pdo = get_db_connection();
    $stmt = $pdo->prepare('SELECT * FROM posts WHERE user_id = ? ORDER BY created_at DESC');
    $stmt->execute([$user_id]);
    return $stmt->fetchAll();
}

function get_post_by_id($post_id) {
    $pdo = get_db_connection();
    // ★★★ 修正点 ★★★
    $stmt = $pdo->prepare('SELECT p.*, u.username FROM posts p JOIN fashion_users u ON p.user_id = u.id WHERE p.id = ?');
    $stmt->execute([$post_id]);
    return $stmt->fetch();
}

function delete_post($post_id, $user_id) {
    $pdo = get_db_connection();
    $stmt = $pdo->prepare('DELETE FROM posts WHERE id = ? AND user_id = ?');
    return $stmt->execute([$post_id, $user_id]);
}

// --- コメント (comments) 関連 ---

function get_comments_by_post_id($post_id) {
    $pdo = get_db_connection();
    // ★★★ 修正点 ★★★
    $stmt = $pdo->prepare('SELECT c.*, u.username FROM comments c JOIN fashion_users u ON c.user_id = u.id WHERE c.post_id = ? ORDER BY c.created_at DESC');
    $stmt->execute([$post_id]);
    return $stmt->fetchAll();
}

function add_comment($post_id, $user_id, $text) {
    $pdo = get_db_connection();
    $stmt = $pdo->prepare('INSERT INTO comments (post_id, user_id, text) VALUES (?, ?, ?)');
    return $stmt->execute([$post_id, $user_id, $text]);
}

function delete_comment($comment_id, $user_id) {
    $pdo = get_db_connection();
    $stmt = $pdo->prepare('DELETE FROM comments WHERE id = ? AND user_id = ?');
    return $stmt->execute([$comment_id, $user_id]);
}

// --- いいね (likes) 関連 ---

function toggle_like($post_id, $user_id, $like_type) {
    $pdo = get_db_connection();
    $stmt = $pdo->prepare('SELECT like_type FROM likes WHERE post_id = ? AND user_id = ?');
    $stmt->execute([$post_id, $user_id]);
    $existing = $stmt->fetch();

    if ($existing) {
        if ($existing['like_type'] == $like_type) {
            $stmt = $pdo->prepare('DELETE FROM likes WHERE post_id = ? AND user_id = ?');
            return $stmt->execute([$post_id, $user_id]);
        } else {
            $stmt = $pdo->prepare('UPDATE likes SET like_type = ? WHERE post_id = ? AND user_id = ?');
            return $stmt->execute([$like_type, $post_id, $user_id]);
        }
    } else {
        $stmt = $pdo->prepare('INSERT INTO likes (post_id, user_id, like_type) VALUES (?, ?, ?)');
        return $stmt->execute([$post_id, $user_id, $like_type]);
    }
}

function get_like_status($post_id, $user_id) {
    $pdo = get_db_connection();
    $stmt = $pdo->prepare('SELECT like_type FROM likes WHERE post_id = ? AND user_id = ?');
    $stmt->execute([$post_id, $user_id]);
    $result = $stmt->fetch();
    return $result ? $result['like_type'] : 0;
}

function get_like_counts($post_id) {
    $pdo = get_db_connection();
    $stmt = $pdo->prepare(
        "SELECT
            (SELECT COUNT(*) FROM likes WHERE post_id = ? AND like_type = 1) as likes,
            (SELECT COUNT(*) FROM likes WHERE post_id = ? AND like_type = -1) as dislikes"
    );
    $stmt->execute([$post_id, $post_id]);
    return $stmt->fetch();
}

// --- 認証関連 ---

function is_logged_in() {
    return isset($_SESSION['user_id']);
}

function login_check() {
    if (!is_logged_in()) {
        header('Location: ' . BASE_URL . '/login.php');
        exit;
    }
}

