from flask import Flask, request, jsonify
import joblib
import numpy as np

# Load the trained pipeline and label encoder
try:
    pipeline = joblib.load('best_random_forest_model.pkl')  # The trained pipeline
    label_encoder = joblib.load('label_encoder.pkl')        # The label encoder
    print("Pipeline and label encoder loaded successfully.")
except Exception as e:
    print(f"Error loading model or label encoder: {e}")
    exit(1)

# Initialize Flask app
app = Flask(__name__)

@app.route('/predict', methods=['POST'])
def predict():
    try:
        # Parse the input JSON payload
        data = request.json

        # Ensure all required fields are present
        required_fields = ['nitrogen', 'phosphorous', 'potassium', 'temperature', 'humidity', 'ph', 'rainfall']
        missing_fields = [field for field in required_fields if field not in data]
        if missing_fields:
            return jsonify({'error': f"Missing fields: {', '.join(missing_fields)}"}), 400

        # Prepare features for the model
        features = np.array([
            data['nitrogen'],
            data['phosphorous'],
            data['potassium'],
            data['temperature'],
            data['humidity'],
            data['ph'],
            data['rainfall']
        ]).reshape(1, -1)

        # If predict_proba is supported, calculate probabilities for all classes
        if hasattr(pipeline.named_steps['randomforestclassifier'], 'predict_proba'):
            probabilities = pipeline.predict_proba(features)[0]  # Get probabilities
            class_indices = np.argsort(probabilities)[::-1][:5]  # Get indices of top 5 crops
            top_crops = [
                {
                    'crop': label_encoder.inverse_transform([index])[0],
                    'confidence': probabilities[index]
                }
                for index in class_indices
            ]
        else:
            return jsonify({'error': 'Model does not support confidence probabilities'}), 400

        # Return the top 5 crops with their confidence levels
        return jsonify({'top_crops': top_crops})

    except Exception as e:
        return jsonify({'error': str(e)}), 500

if __name__ == '__main__':
    app.run(debug=True)
