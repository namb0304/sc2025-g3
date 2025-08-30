-- ユーザー情報を保存するテーブル
CREATE TABLE users (
    id SERIAL PRIMARY KEY,
    username VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMPTZ DEFAULT NOW()
);

-- クローゼットのアイテムを保存するテーブル
CREATE TABLE closet_items (
    id SERIAL PRIMARY KEY,
    user_id INTEGER NOT NULL REFERENCES users(id) ON DELETE CASCADE,
    image_path VARCHAR(255) NOT NULL,
    category VARCHAR(100),
    genres TEXT[], -- PostgreSQLの配列型を使ってジャンルを保存
    notes TEXT,
    created_at TIMESTAMPTZ DEFAULT NOW()
);

-- 投稿を保存するテーブル
CREATE TABLE posts (
    id SERIAL PRIMARY KEY,
    user_id INTEGER NOT NULL REFERENCES users(id) ON DELETE CASCADE,
    closet_item_id INTEGER REFERENCES closet_items(id) ON DELETE SET NULL, -- 紐づくアイテムが削除されても投稿は残す
    title VARCHAR(255) NOT NULL,
    post_image VARCHAR(255) NOT NULL,
    description TEXT,
    created_at TIMESTAMPTZ DEFAULT NOW()
);

-- コメントを保存するテーブル
CREATE TABLE comments (
    id SERIAL PRIMARY KEY,
    post_id INTEGER NOT NULL REFERENCES posts(id) ON DELETE CASCADE,
    user_id INTEGER NOT NULL REFERENCES users(id) ON DELETE CASCADE,
    text TEXT NOT NULL,
    created_at TIMESTAMPTZ DEFAULT NOW()
);

-- いいね・低評価を保存するテーブル
CREATE TABLE likes (
    id SERIAL PRIMARY KEY,
    post_id INTEGER NOT NULL REFERENCES posts(id) ON DELETE CASCADE,
    user_id INTEGER NOT NULL REFERENCES users(id) ON DELETE CASCADE,
    like_type SMALLINT NOT NULL, -- 1: いいね, -1: 低評価
    UNIQUE (post_id, user_id) -- 1人のユーザーは1つの投稿に1回しか評価できない
);

-- パフォーマンス向上のためのインデックス作成
CREATE INDEX ON closet_items (user_id);
CREATE INDEX ON posts (user_id);
CREATE INDEX ON comments (post_id);
CREATE INDEX ON likes (post_id);

-- 既存の image_path 列を削除します
ALTER TABLE closet_items DROP COLUMN image_path;

-- 画像のバイナリデータとMIMEタイプを保存するための列を追加します
ALTER TABLE closet_items ADD COLUMN image_data BYTEA;
ALTER TABLE closet_items ADD COLUMN mime_type VARCHAR(100);