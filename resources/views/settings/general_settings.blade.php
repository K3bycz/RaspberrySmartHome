@extends('layouts.app')

@section('content')
<div style="min-height: 85vh;">

    <div class="alert alert-dark mb-4">
        <i class="fa fa-info-circle me-2"></i>
        Współrzędne znajdziesz np. na
        <a href="https://www.google.com/maps" target="_blank">Google Maps</a>
    </div>

    @if(session('success'))
        <div class="notification mb-3" id="success-notification">
            {{ session('success') }}
        </div>
    @endif

    <div class="card shadow-sm mb-4">
        <div class="card-header">
            <h6 class="mb-0"><i class="fa fa-location-dot me-2"></i>Lokalizacja miejsca zamieszkania</h6>
        </div>
        <div class="card-body">
           
            <form action="{{ route('settings.saveLocation') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label class="form-label">Nazwa miejscowości</label>
                    <input type="text" name="location_name" class="form-control form-control-sm"
                        placeholder="np. Warszawa" value="{{ old('location_name', $user->location_name ?? '') }}">
                </div>
                <div class="row g-2 mb-3">
                    <div class="col-6">
                        <label class="form-label">Szerokość geograficzna</label>
                        <input type="number" step="0.0001" name="location_lat"
                            class="form-control form-control-sm"
                            placeholder="np. 52.2297"
                            value="{{ old('location_lat', $user->location_lat ?? '') }}">
                    </div>
                    <div class="col-6">
                        <label class="form-label">Długość geograficzna</label>
                        <input type="number" step="0.0001" name="location_lon"
                            class="form-control form-control-sm"
                            placeholder="np. 21.0122"
                            value="{{ old('location_lon', $user->location_lon ?? '') }}">
                    </div>
                </div>
                
                @error('location_lat') <div class="text-danger small mb-1">{{ $message }}</div> @enderror
                @error('location_lon') <div class="text-danger small mb-1">{{ $message }}</div> @enderror
                @error('location_name') <div class="text-danger small mb-1">{{ $message }}</div> @enderror

                <button type="submit" class="btn btn-custom btn-sm mt-2">
                    <i class="fa fa-floppy-disk me-1"></i>Zapisz lokalizację
                </button>
            </form>
        </div>
    </div>

</div>

@endsection

@section('scripts')
<script>
    setTimeout(function() {
        $('#success-notification').slideUp(400);
    }, 3000);
</script>
@endsection