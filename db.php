<?php
// データベース接続情報を定数として定義
define('DB_HOST', 'localhost');      // ご自身の環境に合わせて変更してください
define('DB_PORT', '5432');           // PostgreSQLのデフォルトポートは5432
define('DB_NAME', 'nambo');    // 作成したデータベース名
define('DB_USER', 'nambo');  // PostgreSQLのユーザー名
define('DB_PASS', 'e6Q9JGJS');  // PostgreSQLのパスワード

/**
 * データベース接続(PDO)を取得する関数
 * @return PDO
 */
function get_db_connection() {
    // static変数に接続を保存することで、リクエスト中に何度も接続しないようにする
    static $pdo = null;

    if ($pdo === null) {
        try {
            $dsn = 'pgsql:host=' . DB_HOST . ';port=' . DB_PORT . ';dbname=' . DB_NAME;
            $pdo = new PDO($dsn, DB_USER, DB_PASS);
            // エラー発生時に例外をスローするように設定
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            // 結果を連想配列で取得するように設定
            $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            // 接続に失敗した場合はエラーメッセージを表示して終了
            die("データベースへの接続に失敗しました: " . $e->getMessage());
        }
    }
    return $pdo;
}