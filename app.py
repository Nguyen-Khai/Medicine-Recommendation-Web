from flask import Flask, request, jsonify

app = Flask(__name__)

@app.route('/diagnose', methods=['POST'])
def diagnose():
    data = request.get_json()
    symptoms = data.get("symptoms", [])
    return jsonify({
        "message": "Chẩn đoán thành công!",
        "symptoms_received": symptoms
    })

if __name__ == '__main__':
    app.run(debug=True, host='127.0.0.1', port=5000)


