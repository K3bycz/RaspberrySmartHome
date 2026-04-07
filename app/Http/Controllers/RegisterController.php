<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class RegisterController extends Controller
{
    // Metoda wyświetlająca formularz rejestracji
    public function showRegisterForm()
    {
        // Jeśli już zalogowany, przekieruj na dashboard
        if (session('user_id')) {
            return redirect()->route('dashboard');
        }

        return view('auth.register');
    }

    // Metoda obsługująca rejestrację
    public function register(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|string|min:6|confirmed',
        ]);

        User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
        ]);

        return redirect()->route('login')->with('success', 'Konto zostało utworzone. Możesz się teraz zalogować.');
    }
}