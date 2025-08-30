<?php
require_once 'helpers.php';
login_check();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die('不正なアクセスです。');
}

$type = $_POST['type'] ?? '';
$current_user_id = $_SESSION['user_id'];

switch ($type) {
    case 'closet_item':
        $id = $_POST['id'] ?? '';
        if (empty($id)) die('IDが指定されていません。');

        // DBからアイテムを削除。所有者チェックは関数内で実行
        if (delete_closet_item($id, $current_user_id)) {
            $_SESSION['message'] = "アイテムを削除しました。";
        } else {
            $_SESSION['message'] = "アイテムの削除に失敗しました。他人のアイテムは削除できません。";
        }
        header('Location: ' . BASE_URL . '/closet.php');
        exit;

    case 'post':
        $id = $_POST['id'] ?? '';
        if (empty($id)) die('IDが指定されていません。');
        
        // delete_post 関数を helpers.php に作成する必要があります
        if (delete_post($id, $current_user_id)) {
            $_SESSION['message'] = "投稿を削除しました。";
        } else {
            $_SESSION['message'] = "投稿の削除に失敗しました。";
        }
        header('Location: ' . BASE_URL . '/mypage.php');
        exit;

    case 'comment':
        $comment_id = $_POST['comment_id'] ?? '';
        if (empty($comment_id)) die('IDが指定されていません。');

        // delete_comment 関数を helpers.php に作成する必要があります
        if (delete_comment($comment_id, $current_user_id)) {
            // 成功メッセージは不要な場合が多い
        }
        // 元の投稿詳細ページに戻る
        header('Location: ' . BASE_URL . '/post_detail.php?id=' . $_POST['post_id']);
        exit;

    default:
        die('不正なリクエストタイプです。');
}