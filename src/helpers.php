<?php
// sc2025-g3/src/helpers.php

// セッションを開始
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// データディレクトリのパスを定義

define('DATA_DIR', __DIR__ . '/../data/');

/**
 * JSONファイルを安全に読み込む関数
 */
function load_data($filename) {
    $file = DATA_DIR . $filename . '.json';
    if (!file_exists($file)) {
        return [];
    }
    $content = file_get_contents($file);
    if (empty($content)) {
        return [];
    }
    $data = json_decode($content, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        return []; 
    }
    return $data;
}

/**
 * ★ 変更点: flockを使って安全に書き込む関数に置き換え
 * データをJSONファイルに安全に保存する関数
 * @param string $filename ファイル名 (拡張子なし)
 * @param array $data 保存するデータ
 * @return bool 成功した場合はtrue、失敗した場合はfalse
 */
function save_data($filename, $data) {
    $file = DATA_DIR . $filename . '.json';
    $fp = fopen($file, 'w');
    if ($fp === false) {
        return false;
    }
    if (flock($fp, LOCK_EX)) {
        fwrite($fp, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        fflush($fp);
        flock($fp, LOCK_UN);
    } else {
        fclose($fp);
        return false;
    }
    fclose($fp);
    return true;
}

/**
 * IDで要素を検索する関数
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
 */
function is_logged_in() {
    return isset($_SESSION['user_id']);
}

/**
 * 未ログイン時にログインページへリダイレクトする関数
 */
function login_check() {
    if (!is_logged_in()) {
        // ★ 変更点: パスを絶対パスに修正
        header('Location: ../public/auth/login.php');
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