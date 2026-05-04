#!/usr/bin/env python3
# Skrypt do zbierania danych z czujników DHT11 i HC-SR04
# Wysyła odczyty do MongoDB Atlas

import adafruit_dht
import board
import time
from datetime import datetime
import RPi.GPIO as GPIO
from pymongo import MongoClient
from pymongo.errors import ConnectionFailure, PyMongoError
import sys

# Dane połączenia z bazą MongoDB
MONGODB_URI = "mongodb+srv://admin:admin@inzynierka.mcehlvv.mongodb.net/laravel?appName=Inzynierka"
DATABASE_NAME = "laravel"
TEMPERATURE_COLLECTION = "temperature_readings"
DISTANCE_COLLECTION = "distance_readings"

# Konfiguracja pinów
DHT_PIN = board.D17  # Czujnik DHT11 - temperatura i wilgotność
TRIG_PIN = 23        # HC-SR04 - pin wyzwalający
ECHO_PIN = 24        # HC-SR04 - pin odbierający echo

# Inicjalizacja GPIO
GPIO.setwarnings(False)
GPIO.cleanup()
GPIO.setmode(GPIO.BCM)
GPIO.setup(TRIG_PIN, GPIO.OUT)
GPIO.setup(ECHO_PIN, GPIO.IN)

dht_device = None  # Obiekt czujnika DHT11, inicjalizowany później

def connect_to_mongodb():
    # Łączenie z bazą danych MongoDB
    try:
        client = MongoClient(MONGODB_URI, serverSelectionTimeoutMS=5000, connectTimeoutMS=5000)
        client.admin.command('ping')  # Sprawdzenie czy baza odpowiada
        db = client[DATABASE_NAME]
        print(f"Polaczono z MongoDB Atlas (baza: {DATABASE_NAME})")
        return db
    except ConnectionFailure as e:
        print(f"Blad polaczenia z MongoDB: {e}")
        return None
    except Exception as e:
        print(f"Nieoczekiwany blad MongoDB: {e}")
        return None

def read_dht11():
    # Odczyt temperatury i wilgotności z DHT11 - max 3 próby w razie błędu
    global dht_device
    
    # Inicjalizacja czujnika przy pierwszym uruchomieniu
    if dht_device is None:
        try:
            dht_device = adafruit_dht.DHT11(DHT_PIN, use_pulseio=False)
            time.sleep(1)
        except Exception as e:
            print(f"Blad inicjalizacji DHT11: {e}")
            return {
                'timestamp': datetime.now().replace(microsecond=0).isoformat(),
                'temperature': None,
                'humidity': None,
                'status': 'init_error'
            }
    
    # Próbujemy odczytać dane 3 razy
    for attempt in range(3):
        try:
            temperature = dht_device.temperature
            humidity = dht_device.humidity
            
            if temperature is not None and humidity is not None:
                return {
                    'timestamp': datetime.now().replace(microsecond=0).isoformat(),
                    'temperature': round(temperature, 1),
                    'humidity': round(humidity, 1),
                    'status': 'ok'
                }
            
            if attempt < 2:
                time.sleep(2)  # Czekamy 2 sekundy przed kolejną próbą
                continue
            
        except RuntimeError as e:
            # RuntimeError to normalny błąd DHT11, nie ma się czym martwić
            if attempt < 2:
                time.sleep(2)
                continue
        except Exception as e:
            print(f"DHT11 blad (proba {attempt + 1}/3): {e}")
            if attempt < 2:
                time.sleep(2)
                continue
    
    # Żadna z prób nie zadziałała
    return {
        'timestamp': datetime.now().replace(microsecond=0).isoformat(),
        'temperature': None,
        'humidity': None,
        'status': 'error_after_3_attempts'
    }

def read_hcsr04():
    # Pomiar odległości czujnikiem HC-SR04 - max 3 próby
    
    for attempt in range(3):
        try:
            # Wysyłamy impuls wyzwalający
            GPIO.output(TRIG_PIN, False)
            time.sleep(0.01)
            GPIO.output(TRIG_PIN, True)
            time.sleep(0.00001)
            GPIO.output(TRIG_PIN, False)
            
            # Mierzymy czas odbicia fali ultradźwiękowej
            timeout = time.time() + 1.0
            pulse_start = time.time()
            pulse_end = time.time()
            
            # Czekamy aż ECHO przejdzie na HIGH
            while GPIO.input(ECHO_PIN) == 0:
                pulse_start = time.time()
                if pulse_start > timeout:
                    if attempt < 2:
                        time.sleep(0.05)
                        break
                    return {
                        'timestamp': datetime.now().replace(microsecond=0).isoformat(),
                        'distance': None,
                        'status': 'timeout_after_3_attempts'
                    }
            
            # Czekamy aż ECHO wróci na LOW
            while GPIO.input(ECHO_PIN) == 1:
                pulse_end = time.time()
                if pulse_end > timeout:
                    if attempt < 2:
                        time.sleep(0.05)
                        break
                    return {
                        'timestamp': datetime.now().replace(microsecond=0).isoformat(),
                        'distance': None,
                        'status': 'timeout_after_3_attempts'
                    }
            
            # Przeliczamy czas na odległość (prędkość dźwięku / 2)
            pulse_duration = pulse_end - pulse_start
            distance = pulse_duration * 17150
            distance = round(distance, 2)
            
            if distance > 0:
                return {
                    'timestamp': datetime.now().replace(microsecond=0).isoformat(),
                    'distance': distance,
                    'status': 'ok'
                }
            
            if attempt < 2:
                time.sleep(0.05)
                continue
            
        except Exception as e:
            print(f"HC-SR04 blad (proba {attempt + 1}/3): {e}")
            if attempt < 2:
                time.sleep(0.05)
                continue
    
    # Żadna z prób nie zadziałała
    return {
        'timestamp': datetime.now().replace(microsecond=0).isoformat(),
        'distance': None,
        'status': 'error_after_3_attempts'
    }

def save_to_mongodb(db, temperature_data, distance_data):
    # Zapis do MongoDB - tylko dane z statusem 'ok'
    
    try:
        # Zapisujemy temperaturę jeśli odczyt się udał
        if temperature_data and temperature_data['status'] == 'ok':
            temp_collection = db[TEMPERATURE_COLLECTION]
            result = temp_collection.insert_one(temperature_data)
            print(f"Temperatura zapisana (ID: {result.inserted_id})")
        elif temperature_data:
            print(f"Temperatura NIE zapisana (status: {temperature_data['status']})")
        
        # Zapisujemy odległość jeśli odczyt się udał
        if distance_data and distance_data['status'] == 'ok':
            dist_collection = db[DISTANCE_COLLECTION]
            result = dist_collection.insert_one(distance_data)
            print(f"Odleglosc zapisana (ID: {result.inserted_id})")
        elif distance_data:
            print(f"Odleglosc NIE zapisana (status: {distance_data['status']})")
        
        return True
        
    except PyMongoError as e:
        print(f"Blad zapisu do MongoDB: {e}")
        return False
    except Exception as e:
        print(f"Nieoczekiwany blad: {e}")
        return False

def main():
    # Start programu - odczyt czujników i zapis do bazy
    
    print("="*60)
    print("SENSORS DATA COLLECTOR -> MongoDB Atlas")
    print("="*60)
    print(f"{datetime.now().strftime('%Y-%m-%d %H:%M:%S')}")
    print("="*60)
    
    # Łączymy się z bazą
    db = connect_to_mongodb()
    if db is None:
        print("Nie mozna polaczyc sie z baza. Koncze.")
        sys.exit(1)
    
    print("\nZbieranie danych z czujnikow...")
    print("-"*60)
    
    # Odczyt DHT11
    print("Odczyt DHT11 (max 3 proby)...")
    temperature_data = read_dht11()
    
    if temperature_data['status'] == 'ok':
        print(f"   Temperatura: {temperature_data['temperature']} C")
        print(f"   Wilgotnosc: {temperature_data['humidity']}%")
    else:
        print(f"   Status: {temperature_data['status']}")
    
    # Odczyt HC-SR04
    print("\nOdczyt HC-SR04 (max 3 proby)...")
    distance_data = read_hcsr04()
    
    if distance_data['status'] == 'ok':
        print(f"   Odleglosc: {distance_data['distance']} cm")
    else:
        print(f"   Status: {distance_data['status']}")
    
    # Wysyłamy dane do bazy
    print("\nWysylanie do MongoDB Atlas...")
    print("-"*60)
    
    if save_to_mongodb(db, temperature_data, distance_data):
        print("\nDane zapisane pomyslnie!")
    else:
        print("\nBlad zapisu danych")
        sys.exit(1)
    
    print("\n" + "="*60)
    print("Zakończono pomyslnie")
    print("="*60)

def cleanup():
    # Zwalniamy zasoby GPIO i zamykamy czujnik DHT11
    global dht_device
    
    try:
        if dht_device is not None:
            dht_device.exit()
    except:
        pass
    
    try:
        GPIO.cleanup()
    except:
        pass

# Uruchomienie programu
if __name__ == "__main__":
    try:
        main()
    except KeyboardInterrupt:
        print("\nPrzerwano przez uzytkownika")
    except Exception as e:
        print(f"\nNieoczekiwany blad: {e}")
        import traceback
        traceback.print_exc()
        sys.exit(1)
    finally:
        cleanup()