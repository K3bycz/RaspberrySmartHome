@extends('layouts.auth')

@section('content')
<div class="card shadow-sm">
    <div class="card-body p-4">
        <h4 class="mb-4 text-center"><i class="fa fa-lock me-2"></i>Logowanie</h4>

        @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <form method="POST" action="{{ route('login.post') }}">
            @csrf

            <div class="mb-3">
                <label for="email" class="form-label">E-mail</label>
                <input
                    type="email"
                    name="email"
                    id="email"
                    class="form-control @error('email') is-invalid @enderror"
                    value="{{ old('email') }}"
                    required
                >
                @error('email')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="password" class="form-label">Hasło</label>
                <input
                    type="password"
                    name="password"
                    id="password"
                    class="form-control @error('password') is-invalid @enderror"
                    required
                >
                @error('password')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="d-grid mt-4">
                <button type="submit" class="btn btn-custom">Zaloguj się</button>
            </div>
        </form>

        <hr>
        <p class="text-center mb-0">
            Nie masz konta?<a href="{{ route('register') }}" class="custom-link">Zarejestruj się</a>
        </p>
    </div>
</div>
@endsection