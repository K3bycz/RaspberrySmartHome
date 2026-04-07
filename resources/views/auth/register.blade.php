@extends('layouts.auth')

@section('content')
<div class="card shadow-sm">
    <div class="card-body p-4">
        <h4 class="mb-4 text-center"><i class="fa fa-user-plus me-2"></i>Rejestracja</h4>

        @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        <form method="POST" action="{{ route('register.post') }}">
            @csrf

            <div class="mb-3">
                <label for="name" class="form-label">Nazwa użytkownika</label>
                <input
                    type="text"
                    name="name"
                    id="name"
                    class="form-control @error('name') is-invalid @enderror"
                    value="{{ old('name') }}"
                    required
                >
                @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

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

            <div class="mb-3">
                <label for="password_confirmation" class="form-label">Powtórz hasło</label>
                <input
                    type="password"
                    name="password_confirmation"
                    id="password_confirmation"
                    class="form-control"
                    required
                >
            </div>

            <div class="d-grid mt-4">
                <button type="submit" class="btn btn-custom">Zarejestruj się</button>
            </div>
        </form>

        <hr>
        <p class="text-center mb-0">
            Masz już konto?<a href="{{ route('login') }}" class="custom-link">Zaloguj się</a>
        </p>
    </div>
</div>
@endsection