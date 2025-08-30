<?php
require_once 'helpers.php';

// 表示したいアイテムのIDをURLパラメータから取得 (例: image.php?id=123)
$item_id = $_GET['id'] ?? 0;

// IDが0または不正な場合は404エラーを返す
if (!filter_var($item_id, FILTER_VALIDATE_INT) || $item_id <= 0) {
    http_response_code(404);
    exit;
}

// ヘルパー関数を使ってDBから画像データを取得
$image = get_image_data_by_item_id($item_id);

if ($image && !empty($image['image_data'])) {
    // データベースから取得したMIMEタイプをContent-Typeヘッダーとして送信
    // これにより、ブラウザはこれが画像であることを認識できる
    header('Content-Type: ' . $image['mime_type']);
    
    // データベースから取得した画像データ（バイナリ）をそのまま出力
    echo $image['image_data'];
} else {
    // 画像が見つからなかった場合も404エラーを返す
    http_response_code(404);
    // 代替画像を表示するか、エラーメッセージを出力しても良い
    // echo "Image not found.";
}