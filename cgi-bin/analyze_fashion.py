import sys
import json
import random

def analyze_image_dummy(image_path):
    """
    【ダミー関数】画像パスを受け取り、AIで分析したかのように振る舞う。
    将来的にはここでGoogle Gemini APIなどを呼び出す。
    """
    categories = ["トップス", "ボトムス", "アウター", "ワンピース", "シューズ"]
    seasons = [["春", "夏"], ["夏"], ["秋", "冬"], ["春", "秋"], ["通年"]]
    styles = ["カジュアル", "きれいめ", "ストリート", "フェミニン", "モード"]
    
    result = {
        "category": random.choice(categories),
        "color": "#" + ''.join(random.choices('0123456789abcdef', k=6)),
        "season": random.choice(seasons),
        "style_tags": random.sample(styles, k=random.randint(1, 2))
    }
    return result

if __name__ == "__main__":
    # PHPから渡されたコマンドライン引数（画像パス）を取得
    if len(sys.argv) > 1:
        image_path = sys.argv[1]
        analysis_result = analyze_image_dummy(image_path)
        # 結果をJSON形式で標準出力に出力する (これがPHPの戻り値になる)
        print(json.dumps(analysis_result, ensure_ascii=False))
    else:
        error_result = {"error": "Image path not provided."}
        print(json.dumps(error_result, ensure_ascii=False))
