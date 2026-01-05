import joblib

model = joblib.load("spam_model.pkl")

tests = [
    "Amazing gameplay and story",
    "Buy gold cheap now click here",
    "Worst game ever",
    "Free skins free coins visit site"
]

for text in tests:
    prob = model.predict_proba([text])[0][1]
    print(f"{text} → Spam score: {prob:.2f}")