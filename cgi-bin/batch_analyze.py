#!/usr/bin/env python3
import sys
import json
import random

def analyze_image_dummy(image_path):
    """
    【ダミー関数】画像パスを受け取り、AIで分析したかのように振る舞う。
    """
    if not image_path:
        return {} # パスが無効な場合は空の結果を返す
        
    categories = ["トップス", "ボトムス", "アウター", "ワンピース", "シューズ"]
    seasons = [["春", "夏"], ["夏"], ["秋", "冬"], ["春", "秋"], ["通年"]]
    styles = ["カジュアル", "きれいめ", "ストリート", "フェミニン", "モード"]
    
    return {
        "ai_category": random.choice(categories),
        "ai_season": random.choice(seasons),
        "ai_style_tags": random.sample(styles, k=random.randint(1, 2))
    }

if __name__ == "__main__":
    try:
        # PHPから標準入力経由でJSONデータを読み込む
        input_json = sys.stdin.read()
        items_to_analyze = json.loads(input_json)
        
        results = {}
        for item in items_to_analyze:
            item_id = item.get('id')
            absolute_path = item.get('absolute_path')
            
            if item_id and absolute_path:
                # 各アイテムを分析
                analysis_result = analyze_image_dummy(absolute_path)
                results[item_id] = analysis_result
        
        # HTTPヘッダーと、最終的な結果のJSONを出力
        print("Content-Type: application/json\n")
        print(json.dumps(results, ensure_ascii=False, indent=2))

    except Exception as e:
        # エラーが発生した場合もJSON形式でエラー情報を返す
        print("Content-Type: application/json\n")
        print(json.dumps({"error": str(e)}))
