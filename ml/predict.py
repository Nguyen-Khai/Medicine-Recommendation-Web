import sys
import json
import joblib
import warnings
import os

warnings.filterwarnings("ignore")

# Lấy đường dẫn tuyệt đối đến thư mục chứa file .py
BASE_DIR = os.path.dirname(os.path.abspath(__file__))

# Load dữ liệu
with open(os.path.join(BASE_DIR, "symptoms.json"), "r", encoding="utf-8") as f:
    all_symptoms = json.load(f)

with open(os.path.join(BASE_DIR, "disease_labels.json"), "r", encoding="utf-8") as f:
    label_map = json.load(f)

model = joblib.load(os.path.join(BASE_DIR, "randomforest.pkl"))

# Nhận dữ liệu từ PHP
input_text = sys.argv[1] if len(sys.argv) > 1 else ""

# Chuẩn hoá và tạo vector
input_symptoms = [s.strip().lower() for s in input_text.split(',') if s.strip()]
input_vector = [1 if s in input_symptoms else 0 for s in all_symptoms]

# Nếu không có triệu chứng hợp lệ nào
if sum(input_vector) == 0:
    print(json.dumps({"disease": "Không xác định"}))
    sys.exit()

# Dự đoán
try:
    pred_index = model.predict([input_vector])[0]
    disease = label_map.get(str(pred_index), "Không xác định")
except Exception as e:
    disease = "Không xác định"

# Xuất JSON để PHP lấy kết quả
print(json.dumps({"disease": disease}))
