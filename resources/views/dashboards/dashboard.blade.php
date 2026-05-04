@extends('layouts.app')

@section('title', 'Dashboard')

@section('styles')
    <link href="{{ asset('css/dashboard.css') }}" rel="stylesheet">
@endsection

@section('content')
<div style="min-height: 85vh;">

    <div class="row g-3">

        <div class="col-12 col-md-2">
            <div class="card shadow-sm mb-4">
                <div class="card-body py-3">
                    <div class="clock-wrapper">
                        <div>
                            <div id="clock" class="clock-time">--:--:--</div>
                            <div id="clock-date" class="clock-date">--- -- ----</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-12 col-md-5">
            <div class="card shadow-sm h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h6 class="mb-0">
                        <i class="fa fa-globe me-2"></i>Warunki zewnętrzne
                        @if(!empty($location['name']))
                            <span class="badge bg-secondary ms-2" style="font-size:0.75rem; font-weight:400;">
                                <i class="fa fa-location-dot me-1"></i>{{ $location['name'] }}
                            </span>
                        @endif
                    </h6>
                    <span id="weather-updated" class="text-muted" style="font-size:0.78rem;"></span>
                </div>
                <div class="card-body">
                    @if(empty($location['lat']))
                        <div class="alert alert-warning mb-0 py-2">
                            <i class="fa fa-triangle-exclamation me-2"></i>
                            Nie ustawiono lokalizacji. Przejdź do
                            <a href="{{ route('settings.general') }}" class="alert-link">Ustawień</a>.
                        </div>
                    @else
                        <div id="weather-loading" class="text-center py-3 text-muted">
                            <i class="fa fa-spinner fa-spin me-2"></i>Pobieranie danych...
                        </div>
                        <div id="weather-content" style="display:none;">
                            <div class="row g-2">
                                <div class="col-6">
                                    <div class="stat-box">
                                        <div class="stat-icon" id="weather-icon-box">
                                            <i id="weather-icon" class="fa fa-sun fa-2x"></i>
                                        </div>
                                        <div class="stat-label">Warunki</div>
                                        <div class="stat-value stat-value--sm" id="weather-desc">—</div>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="stat-box">
                                        <div class="stat-icon text-danger">
                                            <i class="fa fa-thermometer-half fa-2x"></i>
                                        </div>
                                        <div class="stat-label">Temperatura</div>
                                        <div class="stat-value"><span id="weather-temp">—</span> °C</div>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="stat-box">
                                        <div class="stat-icon text-info">
                                            <i class="fa fa-droplet fa-2x"></i>
                                        </div>
                                        <div class="stat-label">Wilgotność</div>
                                        <div class="stat-value"><span id="weather-hum">—</span> %</div>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="stat-box">
                                        <div class="stat-icon text-secondary">
                                            <i class="fa fa-wind fa-2x"></i>
                                        </div>
                                        <div class="stat-label">Wiatr</div>
                                        <div class="stat-value"><span id="weather-wind">—</span> km/h</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div id="weather-error" class="alert alert-danger mb-0 py-2" style="display:none;">
                            <i class="fa fa-circle-exclamation me-2"></i>
                            <span id="weather-error-msg">Błąd pobierania pogody.</span>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-12 col-md-5">
            <div class="card shadow-sm h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h6 class="mb-0">
                        <i class="fa fa-house me-2"></i>Warunki wewnętrzne
                    </h6>
                    @if($latestReading)
                        <span class="text-muted mb-0" style="font-size:0.78rem;">
                            <i class="fa fa-clock me-1"></i>
                            {{ \Carbon\Carbon::parse($latestReading->timestamp)->format('d.m.Y H:i') }}
                        </span>
                    @endif
                </div>
                <div class="card-body">
                    @if($latestReading)
                        <div class="row g-2">
                            <div class="col-6">
                                <div class="stat-box">
                                    <div class="stat-icon text-danger">
                                        <i class="fa fa-thermometer-half fa-2x"></i>
                                    </div>
                                    <div class="stat-label">Temperatura</div>
                                    <div class="stat-value">
                                        {{ $latestReading->temperature ?? '—' }}
                                        @if($latestReading->temperature) °C @endif
                                    </div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="stat-box">
                                    <div class="stat-icon text-info">
                                        <i class="fa fa-droplet fa-2x"></i>
                                    </div>
                                    <div class="stat-label">Wilgotność</div>
                                    <div class="stat-value">
                                        {{ $latestReading->humidity ?? '—' }}
                                        @if($latestReading->humidity) % @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="text-center py-3 text-muted">
                            <i class="fa fa-database me-2"></i>Brak danych z czujników
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="row g-2 mt-1" id="bulbs-row">
        
            <div class="col-12 col-md-5 offset-md-2">
                <div class="card shadow-sm bulb-card" id="bulb1-card">
                    <div class="card-header d-flex justify-content-between align-items-center"
                        data-bs-toggle="collapse" data-bs-target="#bulb1-body"
                        role="button" aria-expanded="true">
                        <h6 class="mb-0 d-flex align-items-center gap-2">
                            <span class="bulb-icon-wrap" id="bulb1-icon-wrap">
                                <i class="fa fa-lightbulb fa-lg" id="bulb1-icon"></i>
                            </span>
                            Żarówka 1
                            <span class="bulb-status-badge" id="bulb1-badge">—</span>
                        </h6>
                        <div class="d-flex align-items-center gap-2" onclick="event.stopPropagation()">
                            <div class="form-check form-switch mb-0">
                                <input class="form-check-input bulb-toggle"
                                    type="checkbox" role="switch"
                                    id="bulb1-switch" data-bulb="bulb1" disabled>
                            </div>
                            <i class="fa fa-chevron-down bulb-chevron text-muted" id="bulb1-chevron"></i>
                        </div>
                    </div>
        
                    <div class="collapse show" id="bulb1-body">
                        <div class="card-body pt-2 pb-3">
        
                            <div class="d-flex gap-2 mb-3" id="bulb1-mode-btns">
                                <button class="btn btn-sm bulb-mode-btn active" data-bulb="bulb1" data-mode="color">
                                    <i class="fa fa-palette me-1"></i>Kolor
                                </button>
                                <button class="btn btn-sm bulb-mode-btn" data-bulb="bulb1" data-mode="white">
                                    <i class="fa fa-sun me-1"></i>Biel
                                </button>
                            </div>
        
                            <div class="bulb-panel" id="bulb1-panel-color">
                                <label class="bulb-slider-label">
                                    <i class="fa fa-palette me-1 text-muted"></i>Kolor
                                </label>
                                <div class="d-flex align-items-center gap-2 mb-2">
                                    <input type="color" class="bulb-colorpicker" id="bulb1-color"
                                        value="#ffffff" data-bulb="bulb1">
                                    <span class="bulb-color-preview" id="bulb1-color-preview"
                                        style="background:#ffffff;"></span>
                                    <span class="text-muted" style="font-size:0.78rem;" id="bulb1-color-hex">#ffffff</span>
                                </div>
                                <label class="bulb-slider-label">
                                    <i class="fa fa-sun me-1 text-muted"></i>Jasność
                                    <span class="bulb-slider-val ms-1" id="bulb1-brightness-val">100%</span>
                                </label>
                                <input type="range" class="bulb-range" id="bulb1-brightness"
                                    min="10" max="100" value="100" data-bulb="bulb1" data-type="brightness">
                            </div>
        
                            <div class="bulb-panel d-none" id="bulb1-panel-white">
                                <label class="bulb-slider-label">
                                    <i class="fa fa-sun me-1 text-muted"></i>Jasność
                                    <span class="bulb-slider-val ms-1" id="bulb1-white-brightness-val">100%</span>
                                </label>
                                <input type="range" class="bulb-range" id="bulb1-white-brightness"
                                    min="10" max="100" value="100" data-bulb="bulb1" data-type="white-brightness">
        
                                <label class="bulb-slider-label mt-2">
                                    <i class="fa fa-temperature-half me-1 text-muted"></i>Temperatura
                                    <span class="bulb-slider-val ms-1" id="bulb1-temp-val">50</span>
                                    <span class="bulb-temp-hint ms-1">(ciepły ← → zimny)</span>
                                </label>
                                <input type="range" class="bulb-range bulb-range--temp" id="bulb1-temp"
                                    min="10" max="100" value="50" data-bulb="bulb1" data-type="white-temp">
                            </div>
        
                        </div>
                    </div>
                </div>
            </div>
        
            <div class="col-12 col-md-5">
                <div class="card shadow-sm bulb-card" id="bulb2-card">
                    <div class="card-header d-flex justify-content-between align-items-center"
                        data-bs-toggle="collapse" data-bs-target="#bulb2-body"
                        role="button" aria-expanded="true">
                        <h6 class="mb-0 d-flex align-items-center gap-2">
                            <span class="bulb-icon-wrap" id="bulb2-icon-wrap">
                                <i class="fa fa-lightbulb fa-lg" id="bulb2-icon"></i>
                            </span>
                            Żarówka 2
                            <span class="bulb-status-badge" id="bulb2-badge">—</span>
                        </h6>
                        <div class="d-flex align-items-center gap-2" onclick="event.stopPropagation()">
                            <div class="form-check form-switch mb-0">
                                <input class="form-check-input bulb-toggle"
                                    type="checkbox" role="switch"
                                    id="bulb2-switch" data-bulb="bulb2" disabled>
                            </div>
                            <i class="fa fa-chevron-down bulb-chevron text-muted" id="bulb2-chevron"></i>
                        </div>
                    </div>
        
                    <div class="collapse show" id="bulb2-body">
                        <div class="card-body pt-2 pb-3">
        
                            <div class="d-flex gap-2 mb-3" id="bulb2-mode-btns">
                                <button class="btn btn-sm bulb-mode-btn active" data-bulb="bulb2" data-mode="color">
                                    <i class="fa fa-palette me-1"></i>Kolor
                                </button>
                                <button class="btn btn-sm bulb-mode-btn" data-bulb="bulb2" data-mode="white">
                                    <i class="fa fa-sun me-1"></i>Biel
                                </button>
                            </div>
        
                            <div class="bulb-panel" id="bulb2-panel-color">
                                <label class="bulb-slider-label">
                                    <i class="fa fa-palette me-1 text-muted"></i>Kolor
                                </label>
                                <div class="d-flex align-items-center gap-2 mb-2">
                                    <input type="color" class="bulb-colorpicker" id="bulb2-color"
                                        value="#ffffff" data-bulb="bulb2">
                                    <span class="bulb-color-preview" id="bulb2-color-preview"
                                        style="background:#ffffff;"></span>
                                    <span class="text-muted" style="font-size:0.78rem;" id="bulb2-color-hex">#ffffff</span>
                                </div>
                                <label class="bulb-slider-label">
                                    <i class="fa fa-sun me-1 text-muted"></i>Jasność
                                    <span class="bulb-slider-val ms-1" id="bulb2-brightness-val">100%</span>
                                </label>
                                <input type="range" class="bulb-range" id="bulb2-brightness"
                                    min="10" max="100" value="100" data-bulb="bulb2" data-type="brightness">
                            </div>
        
                            <div class="bulb-panel d-none" id="bulb2-panel-white">
                                <label class="bulb-slider-label">
                                    <i class="fa fa-sun me-1 text-muted"></i>Jasność
                                    <span class="bulb-slider-val ms-1" id="bulb2-white-brightness-val">100%</span>
                                </label>
                                <input type="range" class="bulb-range" id="bulb2-white-brightness"
                                    min="10" max="100" value="100" data-bulb="bulb2" data-type="white-brightness">
        
                                <label class="bulb-slider-label mt-2">
                                    <i class="fa fa-temperature-half me-1 text-muted"></i>Temperatura
                                    <span class="bulb-slider-val ms-1" id="bulb2-temp-val">50</span>
                                    <span class="bulb-temp-hint ms-1">(ciepły ← → zimny)</span>
                                </label>
                                <input type="range" class="bulb-range bulb-range--temp" id="bulb2-temp"
                                    min="10" max="100" value="50" data-bulb="bulb2" data-type="white-temp">
                            </div>
        
                        </div>
                    </div>
                </div>
            </div>
        
        </div>

    </div>
</div>
@endsection

@section('scripts')
<script>
    // funkja aktualizująca zegar
    function updateClock() {
        const now    = new Date();
        const days   = ['Niedziela','Poniedziałek','Wtorek','Środa','Czwartek','Piątek','Sobota'];
        const months = ['stycznia','lutego','marca','kwietnia','maja','czerwca',
                        'lipca','sierpnia','września','października','listopada','grudnia'];
        const h  = String(now.getHours()).padStart(2,'0');
        const m  = String(now.getMinutes()).padStart(2,'0');
        const s  = String(now.getSeconds()).padStart(2,'0');
        document.getElementById('clock').textContent      = `${h}:${m}:${s}`;
        document.getElementById('clock-date').textContent =
            `${days[now.getDay()]}, ${now.getDate()} ${months[now.getMonth()]} ${now.getFullYear()}`;
    }
    updateClock();
    setInterval(updateClock, 1000);

    // Ajax pobierający pogode
    @if(!empty($location['lat']))
    (function fetchWeather() {
        const weatherUrl = '{{ secure_url(route("weather.current", [], false)) }}';

        $.ajax({
            url: weatherUrl,
            method: 'GET',
            data: {
                lat: '{{ $location["lat"] }}',
                lon: '{{ $location["lon"] }}'
            },
            success: function(res) {
                $('#weather-temp').text(res.temperature ?? '—');
                $('#weather-hum').text(res.humidity     ?? '—');
                $('#weather-wind').text(res.windspeed   ?? '—');
                $('#weather-desc').text(res.description ?? '—');
                $('#weather-icon').attr('class', 'fa ' + (res.icon ?? 'fa-question') + ' fa-2x');

                const iconColors = {
                    'fa-sun':        'text-warning',
                    'fa-cloud-sun':  'text-warning',
                    'fa-cloud':      'text-secondary',
                    'fa-cloud-rain': 'text-info',
                    'fa-snowflake':  'text-primary',
                    'fa-bolt':       'text-warning',
                    'fa-smog':       'text-secondary',
                };
                $('#weather-icon-box').attr('class',
                    'stat-icon ' + (iconColors[res.icon] ?? 'text-secondary'));

                const now = new Date();
                $('#weather-updated').text('Aktualizacja: ' +
                    String(now.getHours()).padStart(2,'0') + ':' +
                    String(now.getMinutes()).padStart(2,'0'));

                $('#weather-loading').hide();
                $('#weather-content').show();
            },
            error: function(xhr) {
                $('#weather-error-msg').text(xhr.responseJSON?.error ?? 'Błąd pobierania pogody.');
                $('#weather-loading').hide();
                $('#weather-error').show();
            }
        });
    })();
    @endif

    // żarówki 
    (function () {
        'use strict';
    
        const CSRF   = $('meta[name="csrf-token"]').attr('content');
        const DEBOUNCE_MS = 400; // opóźnienie suwaków przed wysłaniem

        $.ajaxSetup({
            headers: { 'X-CSRF-TOKEN': CSRF }
        });
        
        // konwersja hex na rgb
        function hexToRgb(hex) {
            const r = parseInt(hex.slice(1,3),16);
            const g = parseInt(hex.slice(3,5),16);
            const b = parseInt(hex.slice(5,7),16);
            return { r, g, b };
        }
    
        function debounce(fn, ms) {
            let t;
            return function(...args) { clearTimeout(t); t = setTimeout(() => fn(...args), ms); };
        }
    
        // ustawia wizualny stan żarówki
        function applyBulbState(bulb, state) {
            const $icon  = $(`#${bulb}-icon`);
            const $wrap  = $(`#${bulb}-icon-wrap`);
            const $badge = $(`#${bulb}-badge`);
            const $sw    = $(`#${bulb}-switch`);
    
            if (!state || state.error) {
                // Brak dostępu
                $wrap.removeClass('bulb-on bulb-off').addClass('bulb-unreachable');
                $icon.css('color', '');
                $badge.text('brak dostępu').removeClass('badge-on badge-off').addClass('badge-unreachable');
                $sw.prop('disabled', true).prop('checked', false);
                $(`#${bulb}-card`).addClass('bulb-card--unreachable');
                return;
            }
    
            $(`#${bulb}-card`).removeClass('bulb-card--unreachable');
            $sw.prop('disabled', false).prop('checked', state.on === true);
    
            if (state.on) {
                $wrap.removeClass('bulb-off bulb-unreachable').addClass('bulb-on');
                $badge.text('włączona').removeClass('badge-off badge-unreachable').addClass('badge-on');
    
                if (state.mode === 'color' && state.color) {
                    const { r, g, b } = state.color;
                    $icon.css('color', `rgb(${r},${g},${b})`);
                    const hex = '#' +
                        r.toString(16).padStart(2,'0') +
                        g.toString(16).padStart(2,'0') +
                        b.toString(16).padStart(2,'0');
                    $(`#${bulb}-color`).val(hex);
                    $(`#${bulb}-color-preview`).css('background', hex);
                    $(`#${bulb}-color-hex`).text(hex);
                } else {
                    $icon.css('color', '#FFD97D');
                }
            } else {
                $wrap.removeClass('bulb-on bulb-unreachable').addClass('bulb-off');
                $icon.css('color', '');
                $badge.text('wyłączona').removeClass('badge-on badge-unreachable').addClass('badge-off');
            }
    
            // ustaw suwaki wg stanu
            if (state.brightness != null) {
                $(`#${bulb}-brightness`).val(state.brightness);
                $(`#${bulb}-brightness-val`).text(state.brightness + '%');
                $(`#${bulb}-white-brightness`).val(state.brightness);
                $(`#${bulb}-white-brightness-val`).text(state.brightness + '%');
            }
            if (state.temperature != null) {
                $(`#${bulb}-temp`).val(state.temperature);
                $(`#${bulb}-temp-val`).text(state.temperature);
            }
    
            // tryb
            if (state.mode) {
                setMode(bulb, state.mode === 'white' ? 'white' : 'color', false);
            }
        }
    
        function setMode(bulb, mode, animate = true) {
            $(`#${bulb}-panel-color`).toggleClass('d-none', mode !== 'color');
            $(`#${bulb}-panel-white`).toggleClass('d-none', mode !== 'white');
            $(`[data-bulb="${bulb}"].bulb-mode-btn`).each(function () {
                $(this).toggleClass('active', $(this).data('mode') === mode);
            });
        }

        function bulbApi(endpoint, data, onSuccess) {
            const isGet = (endpoint === 'status');
            const opts  = {
                url    : isGet ? '/bulbs/status' : `/bulbs/${endpoint}`,
                method : isGet ? 'GET' : 'POST',
        
                contentType: 'application/json',
                success: onSuccess,
                error  : function (xhr) {
                    console.warn('[Bulbs] błąd:', xhr.responseJSON?.error ?? xhr.status);
                }
            };
            if (!isGet) opts.data = JSON.stringify(data);
            $.ajax(opts);
        }
    
        // pobierz statsus żarówek przy ładowaniu strony
        bulbApi('status', null, function (res) {
            const data = res.data ?? res;
            applyBulbState('bulb1', data.bulb1 ?? null);
            applyBulbState('bulb2', data.bulb2 ?? null);
        });
        
       
    
        // switch on/off
        $(document).on('change', '.bulb-toggle', function () {
            const bulb = $(this).data('bulb');
            const on   = $(this).is(':checked');
            bulbApi(on ? 'on' : 'off', { bulb }, function () {
                // odśwież ikonę lokalnie bez czekania na pełny status
                const fakeState = { on, mode: 'color' };
                applyBulbState(bulb, fakeState);
            });
        });
    
        // tryb kolor/biel
        $(document).on('click', '.bulb-mode-btn', function () {
            const bulb = $(this).data('bulb');
            const mode = $(this).data('mode');
            setMode(bulb, mode);
        });
    
        // color picker
        const sendColor = debounce(function (bulb, hex) {
            const { r, g, b } = hexToRgb(hex);
            bulbApi('color', { bulb, r, g, b }, function () {
                $(`#${bulb}-icon`).css('color', `rgb(${r},${g},${b})`);
            });
        }, DEBOUNCE_MS);
    
        $(document).on('input', '.bulb-colorpicker', function () {
            const bulb = $(this).data('bulb');
            const hex  = $(this).val();
            $(`#${bulb}-color-preview`).css('background', hex);
            $(`#${bulb}-color-hex`).text(hex);
            $(`#${bulb}-icon`).css('color', hex); // podgląd na żywo
            sendColor(bulb, hex);
        });
    
        // suwaki jasności i temperatury
        const sendSlider = debounce(function (bulb, type, value) {
            if (type === 'brightness') {
                bulbApi('brightness', { bulb, brightness: value });
            } else if (type === 'white-brightness') {
                // Pobierz aktualną temperaturę żeby wysłać razem
                const temp = parseInt($(`#${bulb}-temp`).val(), 10);
                bulbApi('white', { bulb, brightness: value, temperature: temp });
            } else if (type === 'white-temp') {
                const br = parseInt($(`#${bulb}-white-brightness`).val(), 10);
                bulbApi('white', { bulb, brightness: br, temperature: value });
            }
        }, DEBOUNCE_MS);
    
        $(document).on('input', '.bulb-range', function () {
            const bulb  = $(this).data('bulb');
            const type  = $(this).data('type');
            const value = parseInt($(this).val(), 10);
    
            // Etykieta wartości
            if (type === 'brightness')       $(`#${bulb}-brightness-val`).text(value + '%');
            if (type === 'white-brightness') $(`#${bulb}-white-brightness-val`).text(value + '%');
            if (type === 'white-temp')       $(`#${bulb}-temp-val`).text(value);
    
            sendSlider(bulb, type, value);
        });
    
        // chevron collapse
        ['bulb1', 'bulb2'].forEach(function (bulb) {
            $(`#${bulb}-body`)
                .on('hide.bs.collapse', function () {
                    $(`#${bulb}-chevron`).addClass('rotated');
                })
                .on('show.bs.collapse', function () {
                    $(`#${bulb}-chevron`).removeClass('rotated');
                });
        });
    
    })();
</script>

@endsection