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
        $data['page_title'] = "AntonX- Users List";
        $data['users'] = User::with('role')->get();
        return view('user.user_list', $data);
    }

    public function save(Request $request)
    {
        $valid_year = date("Y", strtotime("-15 year"));
        $birth_year = date('Y', strtotime($request->dob));
        if ($valid_year < $birth_year) {
            $response['status'] = 'failure';
            $response['result'] = 'User Age should not be less than 15 years';
        } else {
            $validator = Validator::make($request->all(), [
                'atn_number' => 'required|numeric|unique:users,atn_number,' . $request->id,
                'full_name' => 'required',
                'role_id' => 'required',
                'designation_id' => 'required',
                'joining_date' => 'required',
                'email' => 'required|regex:/^([a-z0-9\+_\-]+)(\.[a-z0-9\+_\-]+)*@([a-z0-9\-]+\.)+[a-z]{2,6}$/ix|unique:users,email,' . $request->id,
                'job_type' => 'required',
                'contact' => 'required|numeric|digits_between:11,14|unique:users,contact,' . $request->id,
                'color' => 'required',
                'user_image' => 'image|mimes:png,gif,jpeg,jpg,|max:2048',

            ]);

            if ($validator->passes()) {

                $user_data = [
                    'name' => $request->full_name,
                    'job_type' => $request->job_type,
                    'color' => $request->color,
                    'email' => $request->email,
                    'atn_number' => $request->atn_number,
                    'contact' => $request->contact,
                    'blood_group' => $request->blood_group,
                    'current_address' => $request->current_address,
                    'postal_address' => $request->postal_address,
                    'gender' => $request->gender,
                    'date_of_birth' => $request->dob,
                    'joining_date' => $request->joining_date,
                    'role_id' => $request->role_id,
                    'designation_id' => $request->designation_id,

                ];
                if ($request->file('user_image')) {
                    $image_name = "";
                    $file = $request->file('user_image');
                    $image_name = time() . rand(1, 100) . '.' . $file->extension();
                    $file->move(public_path('/images/users_images'), $image_name);
                    $user_data['image_url'] =  request()->getSchemeAndHttpHost() . "/public/images/users_images/$image_name";
                }



                if (isset($request->id)) {
                    $user_designation = User::find($request->id);
                    $designation = UserDesignation::find($user_designation->designation_id);
                    $usr_company = UserCompany::where('user_id', $request->id)
                        ->where('designation', $designation->title)->whereNull('endDate')->get();

                    if ($user_designation->designation_id != $request->designation_id) {
                        if (count($usr_company) > 0) {
                            $user_designation = [
                                'endDate' => get_date(),
                            ];
                            $user_designation['updated_by'] =  Auth::user()->id;
                            UserCompany::where('user_id', $request->id)->where('designation', $designation->title)
                                ->whereNull('endDate')->update($user_designation);
                        }
                        $get_designation = UserDesignation::find($request->designation_id);
                        $user_designation = [
                            'designation' => $get_designation->title,
                            'startDate' => get_date(),
                            'user_id' => $request->id,
                        ];
                        $user_designation['created_by'] =  Auth::user()->id;
                        UserCompany::create($user_designation);
                    }
                    $user_data['updated_by'] = Auth::user()->id;
                    User::where('id', $request->id)->update($user_data);
                } else {
                    $valid_pass = Validator::make($request->all(), [
                        'password' => 'required|min:6',
                    ]);
                    if ($valid_pass->passes()) {
                        $user_data['password'] = Hash::make($request->password);

                        $user_data['created_by'] = Auth::user()->id;
                        User::create($user_data);

                        $user_id = User::orderByDesc('id')->get()[0];
                        $streak = [
                            'streak' => 0,
                            'user_id' => $user_id->id,
                            'user_up' => 0,
                            'user_down' => 0,
                        ];
                        Streak::create($streak);

                        $designation = UserDesignation::find($request->designation_id);
                        $user_designation = [
                            'designation' => $designation->title,
                            'startDate' => get_date(),
                            'user_id' => $user_id->id,
                        ];
                        $user_designation['created_by'] =  Auth::user()->id;
                        UserCompany::create($user_designation);
                    } else {
                        $response['status'] = 'failure';
                        $response['result'] = $valid_pass->errors()->toJson();
                    }
                }
                $response['status'] = 'Success';
                $response['result'] = 'Added Successfully';
            } else {
                $response['status'] = 'failure';
                $response['result'] = $validator->errors()->toJson();
            }
        }
        return response()->json($response);
    }

   

    public function delete(Request $request)
    {
        $user = User::find($request->id);
        if ($user->role->title != 'Super Admin') {
            $user->delete();
            $response['status'] = "Success";
            $response['result'] = "Deleted Successfully";
        } else {
            $response['status'] = "failure";
            $response['result'] = "Deletion denied for this user!";
        }

        return response()->json($response);
    }
}
