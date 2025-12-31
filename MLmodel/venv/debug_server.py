# debug_server.py
import requests
import json
import sys

def test_connection():
    """Test if the Flask server is running"""
    print("=" * 50)
    print("Testing Flask server connection...")
    print("=" * 50)
    
    try:
        # Try to connect to the server
        response = requests.get("http://localhost:5000/", timeout=5)
        print(f"✅ Server is running")
        print(f"   Status Code: {response.status_code}")
        print(f"   Response: {response.text[:100]}...")
        return True
    except requests.exceptions.ConnectionError:
        print("❌ ERROR: Flask server is not running!")
        print("   Make sure to run: python spam_api.py")
        return False
    except Exception as e:
        print(f"❌ Connection error: {e}")
        return False

def test_predict_endpoint():
    """Test the /predict endpoint with sample data"""
    print("\n" + "=" * 50)
    print("Testing /predict endpoint...")
    print("=" * 50)
    
    # Test cases - spam and non-spam examples
    test_cases = [
        {"text": "WINNER!! You have been selected for a free iPhone! Claim now!"},
        {"text": "Hi John, just checking if we're still meeting tomorrow at 3pm?"},
        {"text": "URGENT: Your bank account needs verification. Click link to update."},
        {"text": "Meeting agenda attached. Please review before our call."},
        {"text": "Congratulations! You won a $1000 Walmart gift card!"}
    ]
    
    for i, test_data in enumerate(test_cases, 1):
        print(f"\nTest {i}: {test_data['text'][:50]}...")
        
        try:
            response = requests.post(
                "http://localhost:5000/predict",
                json=test_data,
                headers={"Content-Type": "application/json"},
                timeout=10
            )
            
            print(f"   Status Code: {response.status_code}")
            
            if response.status_code == 200:
                result = response.json()
                print(f"   ✅ Success!")
                print(f"   Prediction: {'SPAM' if result['spam'] else 'NOT SPAM'}")
                print(f"   Score: {result['spam_score']:.2%}")
            else:
                print(f"   ❌ Error: {response.text}")
                
        except requests.exceptions.ConnectionError:
            print("   ❌ Cannot connect to server. Is it running?")
            break
        except Exception as e:
            print(f"   ❌ Request failed: {e}")

def test_invalid_requests():
    """Test error handling with invalid requests"""
    print("\n" + "=" * 50)
    print("Testing error handling...")
    print("=" * 50)
    
    # Test 1: No JSON data
    print("\n1. Testing with no JSON data:")
    try:
        response = requests.post("http://localhost:5000/predict", timeout=5)
        print(f"   Status: {response.status_code}")
        print(f"   Response: {response.text}")
    except Exception as e:
        print(f"   Error: {e}")
    
    # Test 2: Empty JSON
    print("\n2. Testing with empty JSON:")
    try:
        response = requests.post(
            "http://localhost:5000/predict",
            json={},
            timeout=5
        )
        print(f"   Status: {response.status_code}")
        print(f"   Response: {response.text}")
    except Exception as e:
        print(f"   Error: {e}")
    
    # Test 3: Missing 'text' field
    print("\n3. Testing with wrong field name:")
    try:
        response = requests.post(
            "http://localhost:5000/predict",
            json={"message": "Hello"},
            timeout=5
        )
        print(f"   Status: {response.status_code}")
        print(f"   Response: {response.text}")
    except Exception as e:
        print(f"   Error: {e}")

def test_server_info():
    """Get server information"""
    print("\n" + "=" * 50)
    print("Server Information")
    print("=" * 50)
    
    # Check if model is loaded
    print("\n1. Checking if model is loaded:")
    try:
        # We'll infer from a test prediction
        test_data = {"text": "test"}
        response = requests.post(
            "http://localhost:5000/predict",
            json=test_data,
            timeout=5
        )
        if response.status_code == 500 and "model" in response.text.lower():
            print("   ❌ Model not loaded properly")
        else:
            print("   ✅ Model seems to be working")
    except:
        print("   ⚠️ Could not check model status")
    
    # Check other endpoints (if you add them later)
    print("\n2. Available endpoints:")
    endpoints = [
        ("/predict", "POST", "Make predictions"),
    ]
    
    for endpoint, method, description in endpoints:
        print(f"   {method:6} http://localhost:5000{endpoint:<20} - {description}")

def interactive_test():
    """Interactive mode for testing custom messages"""
    print("\n" + "=" * 50)
    print("Interactive Testing Mode")
    print("=" * 50)
    
    while True:
        print("\nEnter a message to test (or 'quit' to exit):")
        user_input = input("> ")
        
        if user_input.lower() in ['quit', 'exit', 'q']:
            break
        
        if not user_input.strip():
            continue
        
        try:
            response = requests.post(
                "http://localhost:5000/predict",
                json={"text": user_input},
                timeout=10
            )
            
            if response.status_code == 200:
                result = response.json()
                print(f"\nResult:")
                print(f"  Message: {user_input[:80]}...")
                print(f"  Is Spam: {'✅ YES' if result['spam'] else '✅ NO'}")
                print(f"  Confidence: {result['spam_score']:.2%}")
                
                # Visual indicator
                if result['spam_score'] > 0.8:
                    print("  🚨 HIGH SPAM RISK")
                elif result['spam_score'] > 0.6:
                    print("  ⚠️  Likely Spam")
                elif result['spam_score'] > 0.4:
                    print("  🤔 Uncertain")
                else:
                    print("  👍 Likely Not Spam")
            else:
                print(f"\n❌ Error ({response.status_code}): {response.text}")
                
        except requests.exceptions.ConnectionError:
            print("❌ Cannot connect to server. Make sure spam_api.py is running!")
            break
        except Exception as e:
            print(f"❌ Error: {e}")

def main():
    """Main function to run all tests"""
    print("🚀 Starting Flask API Debug Tool")
    print("Make sure you have started the Flask server first:")
    print("  python spam_api.py\n")
    
    # First, check if server is running
    if not test_connection():
        print("\n⚠️  Please start the Flask server first, then run this debug tool.")
        print("   Run in another terminal: python spam_api.py")
        return
    
    # Run all tests
    test_predict_endpoint()
    test_invalid_requests()
    test_server_info()
    
    # Ask if user wants interactive mode
    print("\n" + "=" * 50)
    choice = input("Start interactive testing? (y/n): ")
    if choice.lower() in ['y', 'yes']:
        interactive_test()
    
    print("\n" + "=" * 50)
    print("Debugging complete! ✅")
    print("=" * 50)

if __name__ == "__main__":
    # Install requests if not installed
    try:
        import requests
    except ImportError:
        print("Installing requests library...")
        import subprocess
        subprocess.check_call([sys.executable, "-m", "pip", "install", "requests"])
        import requests
    
    main()