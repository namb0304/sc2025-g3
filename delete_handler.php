<?php
require_once 'helpers.php';
login_check();

// POSTリクエスト以外は受け付けない
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die('不正なアクセスです。');
}

// フォームから送信された情報を取得
$type = $_POST['type'] ?? '';
$current_user_id = $_SESSION['user_id'];

// 削除する対象によって処理を分岐
switch ($type) {
    
    // ■ クローゼットアイテムの削除
    case 'closet_item':
        $id = $_POST['id'] ?? '';
        if (empty($id)) die('IDが指定されていません。');

        $closet = load_data('closet');
        $item_index = find_index_by_id($closet, $id);

        if ($item_index !== false) {
            // アイテムの所有者がログインユーザー本人か確認
            if ($closet[$item_index]['user_id'] == $current_user_id) {
                // 配列からアイテムを削除
                array_splice($closet, $item_index, 1);
                save_data('closet', $closet);
                $_SESSION['message'] = "アイテムを削除しました。";
            } else {
                $_SESSION['message'] = "他人のアイテムは削除できません。";
            }
        }
        // 処理後はクローゼットページに戻る
        header('Location: ' . BASE_URL . '/closet.php');
        exit;

    // ■ 投稿の削除
    case 'post':
        $id = $_POST['id'] ?? '';
        if (empty($id)) die('IDが指定されていません。');

        $posts = load_data('posts');
        $post_index = find_index_by_id($posts, $id);

        if ($post_index !== false) {
            // 投稿の所有者がログインユーザー本人か確認
            if ($posts[$post_index]['user_id'] == $current_user_id) {
                array_splice($posts, $post_index, 1);
                save_data('posts', $posts);
                $_SESSION['message'] = "投稿を削除しました。";
            } else {
                $_SESSION['message'] = "他人の投稿は削除できません。";
            }
        }
        // 処理後はマイページに戻る
        header('Location: ' . BASE_URL . '/mypage.php');
        exit;

    // ■ コメントの削除
    case 'comment':
        $post_id = $_POST['post_id'] ?? '';
        $comment_id = $_POST['comment_id'] ?? '';
        if (empty($post_id) || empty($comment_id)) die('IDが指定されていません。');

        $posts = load_data('posts');
        $post_index = find_index_by_id($posts, $post_id);

        if ($post_index !== false) {
            $comments = $posts[$post_index]['comments'] ?? [];
            $comment_index_to_delete = -1;

            // 削除するコメントを探す
            foreach ($comments as $index => $comment) {
                if ($comment['id'] === $comment_id) {
                    // コメントの所有者がログインユーザー本人か確認
                    if ($comment['user_id'] == $current_user_id) {
                        $comment_index_to_delete = $index;
                        break;
                    }
                }
            }

            // 見つかった場合、コメントを削除
            if ($comment_index_to_delete !== -1) {
                array_splice($posts[$post_index]['comments'], $comment_index_to_delete, 1);
                save_data('posts', $posts);
            }
        }
        // 処理後は元の投稿詳細ページに戻る
        header('Location: ' . BASE_URL . '/post_detail.php?id=' . $post_id);
        exit;

    // 不正なタイプが指定された場合
    default:
        die('不正なリクエストタイプです。');
}
?>
