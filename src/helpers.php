<?php
// セッションを開始
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// データディレクトリのパスを定義
define('DATA_DIR', __DIR__ . '/data/');

/**
 * JSONファイルを安全に読み込む関数（修正版）
 * @param string $filename ファイル名 (拡張子なし)
 * @return array デコードされたデータ（失敗した場合は空配列）
 */
function load_data($filename) {
    $file = DATA_DIR . $filename . '.json';
    if (!file_exists($file)) {
        return [];
    }
    $content = file_get_contents($file);
    // ファイルが空の場合、空配列を返す
    if (empty($content)) {
        return [];
    }
    $data = json_decode($content, true);

    // json_decodeが失敗した場合（例: JSON形式が不正）、空配列を返す
    if (json_last_error() !== JSON_ERROR_NONE) {
        return []; 
    }

    return $data;
}

/**
 * データをJSONファイルに保存する関数
 * @param string $filename ファイル名 (拡張子なし)
 * @param array $data 保存するデータ
 */
function save_data($filename, $data) {
    $file = DATA_DIR . $filename . '.json';
    file_put_contents($file, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
}

/**
 * 配列の中からIDで特定の要素を検索する関数
 * @param array $data 検索対象の配列
 * @param string|int $id 検索するID
 * @return array|null 見つかった要素、またはnull
 */
function find_by_id($data, $id) {
    foreach ($data as $item) {
        if (isset($item['id']) && $item['id'] == $id) {
            return $item;
        }
    }
    return null;
}

/**
 * ログイン状態をチェックする関数
 * @return bool ログインしていればtrue
 */
function is_logged_in() {
    return isset($_SESSION['user_id']);
}

/**
 * 未ログイン時にログインページへリダイレクトする関数
 */
function login_check() {
    if (!is_logged_in()) {
        header('Location: login.php');
        exit;
    }
}

/**
 * ユーザーIDからユーザー情報を取得する関数
 * @param int $id ユーザーID
 * @return array|null ユーザー情報、またはnull
 */
function get_user_by_id($id) {
    $users = load_data('users');
    return find_by_id($users, $id);
}
?>
