from flask import Flask, request, jsonify
from flask_cors import CORS
import joblib
import os
import traceback
import numpy as np

app = Flask(__name__)
CORS(app) 
app.config['DEBUG'] = True

model_path = os.path.join(os.path.dirname(__file__), "spam_model.pkl")
try:
    model = joblib.load(model_path)
    print("✅ Model loaded successfully")
    print(f"Model type: {type(model)}")
except Exception as e:
    print("❌ ERROR LOADING MODEL:", e)
    traceback.print_exc()
    model = None

@app.route("/", methods=["GET"])
def home():
    """Home endpoint to check if API is running"""
    return jsonify({
        "status": "running",
        "service": "spam_detection_api",
        "endpoints": {
            "GET /": "This page",
            "POST /predict": "Predict if text is spam (requires JSON: {'text': 'message'})"
        }
    })

@app.route("/predict", methods=["POST"])
def predict():
    try:
        if not request.is_json:
            return jsonify({
                "error": "Request must be JSON",
                "hint": "Set Content-Type: application/json header"
            }), 400
        
        try:
            data = request.get_json()
        
        except Exception as e:
            return jsonify({
                "error": "Invalid JSON format",
                "details": str(e)
            }), 400
        
        if not data:
            return jsonify({
                "error": "Empty JSON body",
                "hint": "Send JSON like: {'text': 'your message'}"
            }), 400
        
        if "text" not in data:
            return jsonify({
                "error": "Missing 'text' field in JSON",
                "hint": "Send JSON like: {'text': 'your message'}"
            }), 400
        
        text = data["text"]
        
        if not isinstance(text, str):
            return jsonify({
                "error": "Field 'text' must be a string",
                "received_type": type(text).__name__
            }), 400
        
        if not text.strip():
            return jsonify({
                "error": "Text cannot be empty or whitespace only"
            }), 400
        
        if model is None:
            return jsonify({"error": "Model not loaded"}), 500        
        
        print(f"Making prediction for text: {text[:100]}...")

        try:
            if hasattr(model, 'predict_proba'):
                spam_prob = model.predict_proba([text])[0][1]
                is_spam = bool(spam_prob > 0.5)
            elif hasattr(model, 'predict'):
                prediction = model.predict([text])[0]
                if prediction in [0, 1]:
                    is_spam = bool(prediction == 1)
                    spam_prob = float(prediction)
                elif isinstance(prediction, (bool, np.bool_)):
                    is_spam = bool(prediction)
                    spam_prob = 1.0 if is_spam else 0.0
                else:
                    is_spam = bool(prediction)
                    spam_prob = 1.0 if is_spam else 0.0
            else:
                return jsonify({"error": "Model doesn't have predict or predict_proba methods"}), 500
                
        except Exception as e:
            print(f"Prediction error: {e}")
            traceback.print_exc()
            return jsonify({
                "error": "Prediction failed",
                "details": str(e)
            }), 500

        is_spam = bool(is_spam)
        spam_prob = float(spam_prob)      
        print(f"Prediction: is_spam={is_spam}, spam_prob={spam_prob:.2%}")
        
        return jsonify({
            "spam": is_spam, 
            "spam_score": spam_prob,
            "confidence": f"{spam_prob:.1%}",
            "message": "SPAM detected" if is_spam else "Not spam",
            "text_preview": text[:100] + ("..." if len(text) > 100 else "")
        })

    except Exception as e:
        print(f"General error: {e}")
        traceback.print_exc()
        return jsonify({
            "error": "Internal server error",
            "details": str(e)
        }), 500

if __name__ == "__main__":
    app.run(port=5000, debug=True)