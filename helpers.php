<?php
// 設定ファイルを読み込む
require_once __DIR__ . '/config.php';

// セッションを開始
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// データディレクトリのパスを定義
define('DATA_DIR', __DIR__ . '/data/');

/**
 * JSONファイルを安全に読み込む関数
 */
function load_data($filename) {
    $file = DATA_DIR . $filename . '.json';
    if (!file_exists($file)) return [];
    $content = file_get_contents($file);
    if (empty($content)) return [];
    $data = json_decode($content, true);
    if (json_last_error() !== JSON_ERROR_NONE) return [];
    return $data;
}

/**
 * データをJSONファイルに保存する関数
 */
function save_data($filename, $data) {
    $file = DATA_DIR . $filename . '.json';
    file_put_contents($file, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
}

/**
 * 配列の中からIDで特定の要素を検索する関数 (堅牢版)
 */
function find_by_id($data, $id) {
    // データが配列でない、または空の場合はnullを返す
    if (!is_array($data) || empty($data)) return null;
    foreach ($data as $item) {
        if (isset($item['id']) && $item['id'] == $id) {
            return $item;
        }
    }
    return null;
}

/**
 * 配列の中からIDで特定の要素のインデックス(添字)を検索する関数 (堅牢版)
 */
function find_index_by_id($data, $id) {
    // データが配列でない、または空の場合はfalseを返す
    if (!is_array($data) || empty($data)) return false;
    foreach ($data as $index => $item) {
        if (isset($item['id']) && $item['id'] == $id) {
            return $index;
        }
    }
    return false;
}

/**
 * ログイン状態をチェックする関数
 */
function is_logged_in() {
    return isset($_SESSION['user_id']);
}

/**
 * 未ログイン時にログインページへリダイレクトする関数
 */
function login_check() {
    if (!is_logged_in()) {
        header('Location: ' . BASE_URL . '/login.php');
        exit;
    }
}

/**
 * ユーザーIDからユーザー情報を取得する関数
 */
function get_user_by_id($id) {
    $users = load_data('users');
    return find_by_id($users, $id);
}
?>
