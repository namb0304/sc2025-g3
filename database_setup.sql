-- =========== テーブルの削除 ==========
-- 外部キーで参照されているテーブルから先に削除していくか、
-- CASCADE を使って依存関係ごと強制的に削除します。

DROP TABLE IF EXISTS likes CASCADE;
DROP TABLE IF EXISTS comments CASCADE;
DROP TABLE IF EXISTS posts CASCADE;
DROP TABLE IF EXISTS closet_items CASCADE;
DROP TABLE IF EXISTS fashion_users CASCADE;


-- =========== テーブルの作成 ==========
-- 参照されるテーブルを先に作成します。

-- ユーザー情報を保存するテーブル
CREATE TABLE fashion_users (
    id SERIAL PRIMARY KEY,
    username VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMPTZ DEFAULT NOW()
);

-- クローゼットのアイテムを保存するテーブル
CREATE TABLE closet_items (
    id SERIAL PRIMARY KEY,
    user_id INTEGER NOT NULL REFERENCES fashion_users(id) ON DELETE CASCADE,
    category VARCHAR(100),
    genres TEXT[], -- PostgreSQLの配列型を使ってジャンルを保存
    notes TEXT,
    image_data BYTEA,
    mime_type VARCHAR(100),
    created_at TIMESTAMPTZ DEFAULT NOW()
);

-- 投稿を保存するテーブル
CREATE TABLE posts (
    id SERIAL PRIMARY KEY,
    user_id INTEGER NOT NULL REFERENCES fashion_users(id) ON DELETE CASCADE,
    closet_item_id INTEGER REFERENCES closet_items(id) ON DELETE SET NULL,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    created_at TIMESTAMPTZ DEFAULT NOW()
);

-- コメントを保存するテーブル
CREATE TABLE comments (
    id SERIAL PRIMARY KEY,
    post_id INTEGER NOT NULL REFERENCES posts(id) ON DELETE CASCADE,
    user_id INTEGER NOT NULL REFERENCES fashion_users(id) ON DELETE CASCADE,
    text TEXT NOT NULL,
    created_at TIMESTAMPTZ DEFAULT NOW()
);

-- いいね・低評価を保存するテーブル
CREATE TABLE likes (
    id SERIAL PRIMARY KEY,
    post_id INTEGER NOT NULL REFERENCES posts(id) ON DELETE CASCADE,
    user_id INTEGER NOT NULL REFERENCES fashion_users(id) ON DELETE CASCADE,
    like_type SMALLINT NOT NULL, -- 1: いいね, -1: 低評価
    UNIQUE (post_id, user_id)
);


-- =========== パフォーマンス向上のためのインデックス作成 ==========

CREATE INDEX ON closet_items (user_id);
CREATE INDEX ON posts (user_id);
CREATE INDEX ON posts (closet_item_id);
CREATE INDEX ON comments (post_id);
CREATE INDEX ON likes (post_id);
