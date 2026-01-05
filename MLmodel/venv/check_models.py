import pickle
import os

def check_model_files():
    print("Checking for model files...")
    
    required_files = ['spam_model.pkl', 'vectorizer.pkl']
    
    for file in required_files:
        if os.path.exists(file):
            print(f"✅ Found: {file}")
            # Try to load it
            try:
                with open(file, 'rb') as f:
                    loaded = pickle.load(f)
                    print(f"   Type: {type(loaded)}")
                    print(f"   Successfully loaded")
            except Exception as e:
                print(f"   ❌ Error loading {file}: {e}")
        else:
            print(f"❌ Missing: {file}")
            print(f"   Current directory: {os.getcwd()}")
            print(f"   Files in directory: {os.listdir()}")

if __name__ == "__main__":
    check_model_files()