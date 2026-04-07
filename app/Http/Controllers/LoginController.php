<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{
    // Metoda wyświetlająca formularz logowania
    public function showLoginForm()
    {
        // Jeśli już zalogowany, przekieruj na dashboard
        if (session('user_id')) {
            return redirect()->route('dashboard');
        }

        return view('auth.login');
    }

    // Metoda obsługująca logowanie
    public function login(Request $request)
    {
        // Walidacja
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required|string|min:6',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return back()
                ->withErrors(['email' => 'Nieprawidłowy e-mail lub hasło.'])
                ->withInput($request->only('email'));
        }

        // Zapisz dane użytkownika w sesji
        session([
            'user_id'   => (string) $user->_id,
            'user_name' => $user->name,
            'user_email' => $user->email,
        ]);

        return redirect()->route('dashboard');
    }

    // Wylogowanie
    public function logout(Request $request)
    {
        $request->session()->flush();

        return redirect()->route('login')->with('success', 'Zostałeś wylogowany.');
    }
}