import sys
import json
import random

def get_suggestion_dummy(request_data):
    """
    【ダミー関数】リクエストデータに基づきコーデを提案したかのように振る舞う。
    将来的にはここでGoogle Gemini APIを呼び出す。
    """
    closet_items = request_data.get('closet_items', [])
    request_text = request_data.get('request_text', '')
    
    if not closet_items or len(closet_items) < 2:
        return {"reason": "提案するにはアイテムが少なすぎます。2つ以上登録してください。", "items": []}

    # ダミーの提案ロジック (ランダムに2つ選ぶ)
    selected_items = random.sample(closet_items, k=2)
    
    reason = f"「{request_text}」というご要望を考慮し、AIがあなたのクローゼットから最高の組み合わせを選びました。"

    return {
        "reason": reason,
        "items": [item['image_path'] for item in selected_items]
    }

if __name__ == "__main__":
    # PHPから標準入力経由で送られてきたJSONデータを読み込む
    try:
        input_json = sys.stdin.read()
        request_data = json.loads(input_json)
        suggestion = get_suggestion_dummy(request_data)
    except json.JSONDecodeError:
        suggestion = {"error": "Invalid input from PHP."}
    
    # HTTPヘッダーと結果のJSONを出力
    print("Content-Type: application/json\n")
    print(json.dumps(suggestion, ensure_ascii=False))
