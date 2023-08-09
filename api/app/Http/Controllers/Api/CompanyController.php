<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\AddCompanyRequest;
use App\Http\Resources\CompanyResource;
use App\Mail\WelcomeEmail;
use App\Models\Company;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Mail;

class CompanyController extends Controller
{
    protected $perPage = 10;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $companies = Company::orderByDesc('created_at')->paginate($this->perPage);

        if ($companies) {
            return response()->json(
                [
                    'status' => true,
                    'message' => 'Companies loaded successfully',
                    'body' => $companies,// CompanyResource::collection($companies)
                ],
                200
            );
        } else {
            return response()->json(['status' => false, 'message' => 'NO Company found', 'body' => null], 404);
        }
    }

    public function getCompanyList(){
        $companies = Company::orderBy('name', 'asc')->get();
        return response()->json(
            [
                'status' => true,
                'message' => 'Companies loaded successfully',
                'body' => $companies,
            ],
            200
        );

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(AddCompanyRequest $request, Company $company)
    {
        $data = $request->validated();
        DB::beginTransaction();

        try {
       
            if ($request->has('image')) {
                $data['logo'] = saveFileToStorage($request->file('image'));
            }

            $company->create($data);
            DB::commit();
            
            Mail::to($data['email'])->send(new WelcomeEmail());

            return response()->json(['status' => true, 'message' => 'Company added successfully', 'body' => $company], 200);
        } catch (Exception $e) {       
            Log::info("ADD NEW COMPANY ERROR:");
            Log::info($e->getMessage());
            DB::rollBack();

            return response()->json(['status' => false, 'message' => $e->getMessage(), 'body' => null], 403);
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
        $company = Company::find($id);
        
        if ($company) {
            return response()->json(
                [
                    'status' => true,
                    'message' => 'Company loaded successfully',
                    'body' => new CompanyResource($company)
                ],
                200
            );
        } else {
            return response()->json(['status' => false, 'message' => 'NO Company found', 'body' => null], 404);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(AddCompanyRequest $request, Company $company)
    {
        $data = $request->validated();
        DB::beginTransaction();
        
        try {

            if ($request->file('image')) {
                $data['logo'] = saveFileToStorage($request->file('image'));
            }

            $company->update($data);
            DB::commit();
            return response()->json(['status' => true, 'message' => 'Company updated successfully', 'body' => $company], 200);
        } catch (Exception $e) {
            Log::info("Update COMPANY ERROR:");
            Log::info($e->getMessage());
            DB::rollBack();

            return response()->json(['status' => false, 'message' => 'Company updating failed.', 'body' => null], 403);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Company $company)
    {
        DB::beginTransaction();
        
        try {
           
            $company->delete();
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
