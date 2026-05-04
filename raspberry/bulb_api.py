#!/usr/bin/env python3
# REST API do sterowania żarówkami Gosund WB4 przez WiFi

from flask import Flask, request, jsonify
from flask_cors import CORS
import tinytuya
import colorsys

app = Flask(__name__)
CORS(app)  # Pozwala na komunikację z frontendem

# Konfiguracja żarówek
BULBS = {
    'bulb1': tinytuya.Device(
        'bf0c97eb6b5cc6dea2joz2',
        '192.168.1.43',
        "AkE$5KY8gpw*'jcx",
        version=3.5
    ),
    'bulb2': tinytuya.Device(
        'bf593dd7699e5170aaeduq',
        '192.168.1.44',
        'IEE[7HR[/zsqDsjM',
        version=3.5
    )
}
# Konwersja koloru RGB na format HSV wymagany przez żarówki Gosund
def rgb_to_hsv_hex(r, g, b):
    h, s, v = colorsys.rgb_to_hsv(r/255.0, g/255.0, b/255.0)
    return f"{int(h*360):04x}{int(s*1000):04x}{int(v*1000):04x}"

# Pobranie listy żarówek - jedna konkretna lub wszystkie
def get_bulbs(bulb_id):
    if bulb_id == 'all':
        return list(BULBS.values())
    bulb = BULBS.get(bulb_id)
    return [bulb] if bulb else []

# Pobranie aktualnego stanu żarówek
@app.route('/bulbs/status', methods=['GET'])
def get_status():
    try:
        status = {}
        
        for bulb_id, bulb in BULBS.items():
            data = bulb.status()
            
            if 'dps' in data:
                status[bulb_id] = {
                    'online': True,
                    'on': data['dps'].get('20', False),
                    'mode': data['dps'].get('21', 'white'),
                    'brightness': round(data['dps'].get('22', 0) / 10),
                    'color': data['dps'].get('24', '000000000000')
                }
            else:
                status[bulb_id] = {'online': False}
        
        return jsonify({'success': True, 'data': status}), 200
    
    except Exception as e:
        return jsonify({'success': False, 'error': str(e)}), 500

# Włączenie żarówki (bulb1, bulb2 lub all)
@app.route('/bulbs/on', methods=['POST'])
def turn_on():
    data = request.get_json()
    bulb_id = data.get('bulb', 'all')
    
    for bulb in get_bulbs(bulb_id):
        bulb.set_value(20, True)
    
    return jsonify({'success': True}), 200

# Wyłączenie żarówki (bulb1, bulb2 lub all)
@app.route('/bulbs/off', methods=['POST'])
def turn_off():
    data = request.get_json()
    bulb_id = data.get('bulb', 'all')
    
    for bulb in get_bulbs(bulb_id):
        bulb.set_value(20, False)
    
    return jsonify({'success': True}), 200

# Ustawienie koloru RGB żarówki
@app.route('/bulbs/color', methods=['POST'])
def set_color():
    data = request.get_json()
    bulb_id = data.get('bulb', 'all')
    
    r = int(data.get('r', 255))
    g = int(data.get('g', 255))
    b = int(data.get('b', 255))
    
    hsv = rgb_to_hsv_hex(r, g, b)
    
    for bulb in get_bulbs(bulb_id):
        bulb.set_value(20, True)
        bulb.set_value(21, 'colour')
        bulb.set_value(24, hsv)
    
    return jsonify({'success': True, 'r': r, 'g': g, 'b': b}), 200

# Ustawienie jasności żarówki (zakres 0-100%)
@app.route('/bulbs/brightness', methods=['POST'])
def set_brightness():
    data = request.get_json()
    bulb_id = data.get('bulb', 'all')
    
    brightness = max(0, min(100, int(data.get('brightness', 100))))
    
    for bulb in get_bulbs(bulb_id):
        bulb.set_value(22, brightness * 10)
    
    return jsonify({'success': True, 'brightness': brightness}), 200

# Ustawienie białego światła z regulacją jasności i temperatury barwy
@app.route('/bulbs/white', methods=['POST'])
def set_white():
    data = request.get_json()
    bulb_id = data.get('bulb', 'all')
    
    brightness = max(0, min(100, int(data.get('brightness', 100))))
    temperature = max(0, min(100, int(data.get('temperature', 50))))
    
    for bulb in get_bulbs(bulb_id):
        bulb.set_value(20, True)
        bulb.set_value(21, 'white')
        bulb.set_value(22, brightness * 10)
        bulb.set_value(23, temperature * 10)
    
    return jsonify({'success': True}), 200

# Uruchomienie serwera Flask
if __name__ == '__main__':
    print("Flask API uruchomione na porcie 5000")
    app.run(host='0.0.0.0', port=5000, debug=False)