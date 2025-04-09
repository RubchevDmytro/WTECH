<?php
namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showRegisterForm()
    {
        return view('auth.register');
    }
public function register(Request $request)
{

    $request->validate([
        'name' => 'required|string|max:255',
        'surname' => 'required|string|max:255',
        'email' => 'required|string|email|max:255|unique:users',
        'gender' => 'required|in:male,female', // Убедитесь, что это правило есть
        'password' => 'required|string|min:8|confirmed',
    ], [
        'email.unique' => 'This email is already registered.',
        'password.min' => 'The password must be at least 8 characters.',
        'password.confirmed' => 'The passwords do not match.',
        'gender.required' => 'Please select a gender.',
        'gender.in' => 'Gender must be either male or female.',
    ]);

    $user = User::create([
        'name' => $request->name,
        'surname' => $request->surname,
        'email' => $request->email,
        'gender' => $request->gender,
        'password' => Hash::make($request->password),
    ]);

    Auth::login($user);

    return redirect('/login')->with('success', 'User successfully registered!');
}

    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            if ($request->email === 'admin@example.com' && Auth::user()->is_admin) {
                return redirect('/admin/admin_menu');
            }
            return redirect('/');
        }

        return back()->withErrors(['email' => 'Invalid credentials']);
    }
}
