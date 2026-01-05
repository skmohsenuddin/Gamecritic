import pandas as pd
from sklearn.feature_extraction.text import TfidfVectorizer
from sklearn.linear_model import LogisticRegression
from sklearn.pipeline import Pipeline
import joblib

data = pd.read_csv("dataset/game_review_spam_dataset.csv")

X = data["text"]
y = data["label"]

model = Pipeline([
    ("tfidf", TfidfVectorizer(
        max_features=5000,
        ngram_range=(1, 2),
        stop_words="english"
    )),
    ("clf", LogisticRegression(max_iter=1000))
])

model.fit(X, y)

joblib.dump(model, "spam_model.pkl")

print("Spam detection model trained and saved.")