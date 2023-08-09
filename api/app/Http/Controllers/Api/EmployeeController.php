<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\EmployeeRequest;
use App\Http\Resources\EmployeeResource;
use App\Mail\WelcomeEmail;
use App\Models\Employee;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class EmployeeController extends Controller
{
    protected $perPage = 10;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $employees = Employee::with('company')->orderByDesc('created_at')->paginate($this->perPage);

        if ($employees) {
            return response()->json(
                [
                    'status' => true,
                    'message' => 'Employees loaded successfully',
                    'body' => $employees,//EmployeeResource::collection($employees)
                ],
                200
            );
        } else {
            return response()->json(['status' => false, 'message' => 'NO Employee found', 'body' => null], 404);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(EmployeeRequest $request, Employee $employee)
    {
        $data = $request->validated();
        DB::beginTransaction();

        try {
            $employee->create($data);
            DB::commit();

            Mail::to($data['email'])->send(new WelcomeEmail());
            
            return response()->json(['status' => true, 'message' => 'Employee added successfully', 'body' => $employee], 200);
        } catch (Exception $e) {
            Log::info("ADD NEW EMPLOYEE ERROR:");
            Log::info($e->getMessage());
            DB::rollBack();

            return response()->json(['status' => false, 'message' => 'Adding employee failed.', 'body' => null], 403);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $employee = Employee::find($id);

        if ($employee) {
            return response()->json([
                'status' => true,
                'message' => 'Employee loaded successfully',
                'body' => new EmployeeResource($employee)
            ], 200);
        } else {
            return response()->json(['status' => false, 'message' => 'NO employee found', 'body' => null], 404);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(EmployeeRequest $request, Employee $employee)
    {

        $data = $request->validated();
        DB::beginTransaction();

        try {

            $employee->update($data);
            DB::commit();

            return response()->json(['status' => true, 'message' => 'Employee updated successfully', 'body' => $employee], 200);
        
        } catch (Exception $e) {
        
            Log::info("Update EMPLOYEE ERROR:");
            Log::info($e->getMessage());
            DB::rollBack();

            return response()->json(['status' => false, 'message' => 'Employee updating failed.', 'body' => null], 403);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Employee $employee)
    {
        DB::beginTransaction();
        
        try {
        
            $employee->delete();
            DB::commit();
            return response()->json(['status' => true, 'message' => 'Company deleted successfully', 'body' => null], 200);
        
        } catch (Exception $e) {
        
            Log::info("Delete COMPANY ERROR:");
            Log::info($e->getMessage());
            DB::rollBack();
            return response()->json(['status' => false, 'message' => 'Company deletion failed.', 'body' => null], 403);
        }
    }
}
