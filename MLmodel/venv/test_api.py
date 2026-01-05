import requests

url = "http://127.0.0.1:5000/predict"
data = {"text": "Buy cheap coins now"}

response = requests.post(url, json=data)
print("Status code:", response.status_code)
print("Response:", response.text)

try:
    print("JSON:", response.json())
except Exception as e:
    print("Failed to decode JSON:", e)
