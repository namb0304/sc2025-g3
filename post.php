<?php
require_once 'helpers.php';
login_check();

include 'templates/header.php';
?>
<div class="container">
    <h2>コーディネートを投稿する</h2>
    <p>投稿の方法を選択してください。</p>

    <div class="post-method-selection">
        <a href="<?= BASE_URL ?>/post_from_closet.php" class="method-box">
            <h3>クローゼットから選択して投稿</h3>
            <p>既に登録済みのあなたの服を使って、新しいコーディネートを投稿します。</p>
        </a>
        <a href="<?= BASE_URL ?>/post_new_item.php" class="method-box">
            <h3>新しく服を登録して投稿</h3>
            <p>新しい服の写真をアップロードし、クローゼットに登録すると同時にコーディネートを投稿します。</p>
        </a>
    </div>
</div>

<style>
/* このページ専用のスタイル */
.post-method-selection {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 20px;
    margin-top: 20px;
}
.method-box {
    display: block;
    padding: 30px;
    background: #fff;
    border: 1px solid #ddd;
    border-radius: 8px;
    text-decoration: none;
    color: #333;
    transition: all .3s ease;
    box-shadow: 0 2px 5px rgba(0,0,0,0.05);
}
.method-box:hover {
    transform: translateY(-5px);
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    border-color: #007bff;
}
.method-box h3 {
    margin: 0 0 10px 0;
    color: #007bff;
}
</style>

<?php include 'templates/footer.php'; ?>
