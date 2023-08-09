<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Request;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */
    public $successStatus = 200;

    use AuthenticatesUsers;
    public function login_by_api(Request $request)
    {
        $email = request('email');
        $pass = request('password');

        $user = User::where('email', '=', $email)->first();
        if ($user) {
            if ($email != $user->email) {
                return response()->json(['status' => false, 'error' => 'Email dose match', 'body' => null], 401);
            } elseif (!Hash::check($pass, $user->password)) {
                return response()->json(['status' => false, 'error' => 'Password dose not match', 'body' => null], 401);
            } elseif (Auth::attempt(['email' => $email, 'password' => $pass])) {
                $user = Auth::user();
                $token =  $user->createToken('MyAPI')->accessToken;
                return response()->json(['status' => true, 'error' => null, 'body' => ['access_token' => $token, 'user_info' => $user]], $this->successStatus);
            }
        } else {
            return response()->json(['status' => false, 'error' => 'User not found', 'body' => null], 404);
        }
    }    

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }


    public function username()
    {
        return 'email';
    }
}
