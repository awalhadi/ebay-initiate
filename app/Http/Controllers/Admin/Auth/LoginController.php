<?php

namespace App\Http\Controllers\Admin\Auth;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function login()
    {
        return view('admin.auth.login');
    }

    public function loginSubmit(Request $request)
    {
        $this->validate($request, [
            'email' => 'required|email', 'password' => 'required',
        ]);

        $credentials  = array('email' => $request->email, 'password' => $request->password);
        if (auth()->attempt($credentials, $request->has('remember'))) {
            // Admin user
            if (auth()->user()->user_type == User::USER_TYPE_SYSTEM_ADMIN) {
                return  redirect()->route('admin.dashboard');
            }
            
            return redirect('/');
        }
        return redirect('/admin/login')
            ->withInput($request->only('email', 'remember'))
            ->withErrors([
                'email' => 'Incorrect email address or password',
            ]);
    }

    public function logout(Request $request)
    {
        Auth::logout();

        return redirect('/admin/login');
    }
}
