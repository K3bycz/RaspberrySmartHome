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
</script>
@endsection