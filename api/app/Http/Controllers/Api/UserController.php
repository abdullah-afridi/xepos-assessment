<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function __construct()
    {
        // $this->middleware('role');
    }

    public function logout(Request $request)
    {
        $accessToken = auth()->user()->token();
        $token = $request->user()->tokens->find($accessToken);
        $token->revoke();
        return response(['message' => 'You have been successfully logged out.'], 200);
    }
}
