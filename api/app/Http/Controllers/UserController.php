<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Models\UserRole;
use App\Models\Attendance;
use App\Models\Streak;
use App\Models\User;
use App\Models\UserCompany;
use App\Models\UserDesignation;
use Carbon\Carbon;

class UserController extends Controller
{
    public function __construct()
    {
        // 
    }

    public function index()
    {
        return view('user.index');
    }

}
