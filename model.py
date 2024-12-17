from flask import Flask, request, jsonify
import joblib
import numpy as np


try:
    pipeline = joblib.load('best_random_forest_model.pkl')  
    label_encoder = joblib.load('label_encoder.pkl')        
    print("Pipeline and label encoder loaded successfully.")
except Exception as e:
    print(f"Error loading model or label encoder: {e}")
    exit(1)

app = Flask(__name__)

@app.route('/predict', methods=['POST'])
def predict():
    try:

        data = request.json

        required_fields = ['nitrogen', 'phosphorous', 'potassium', 'temperature', 'humidity', 'ph', 'rainfall']
        missing_fields = [field for field in required_fields if field not in data]
        if missing_fields:
            return jsonify({'error': f"Missing fields: {', '.join(missing_fields)}"}), 400

        features = np.array([
            data['nitrogen'],
            data['phosphorous'],
            data['potassium'],
            data['temperature'],
            data['humidity'],
            data['ph'],
            data['rainfall']
        ]).reshape(1, -1)

        if hasattr(pipeline.named_steps['randomforestclassifier'], 'predict_proba'):
            probabilities = pipeline.predict_proba(features)[0]  
            class_indices = np.argsort(probabilities)[::-1][:5]  
            top_crops = [
                {
                    'crop': label_encoder.inverse_transform([index])[0],
                    'confidence': probabilities[index]
                }
                for index in class_indices
            ]
        else:
            return jsonify({'error': 'Model does not support confidence probabilities'}), 400

        return jsonify({'top_crops': top_crops})

    except Exception as e:
        return jsonify({'error': str(e)}), 500

if __name__ == '__main__':
    app.run(debug=True)
