<?php

namespace App\Http\Controllers;

use DB;
use Auth;
use Session;
use Carbon\Carbon;
use App\Company;

use Yajra\Datatables\Datatables;

use App\Rules\ValidateIsimage; 

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class CompanyController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function __construct() {
        $this->middleware('admin');
    }
    public function index()
    {
        return view('company.index');
    }

    public function list(Request $request)
    {
        $query = Company::where('id','!=',0);
        return DataTables::of($query) 
        ->addColumn('logo',function($query){
            if(!empty($query->logo))
            {
                $url= asset('storage/'.$query->logo);
            }
            else{
                $url= asset('storage/defailt.png');
            }
            $str = '<img src="'.$url.'" alt="image" class="img-fluid img-thumbnail" width="100px" height="100px">';
            return $str;
        })
        ->addColumn('name',function($query){
            return $query->name;
        })
        ->addColumn('email',function($query){
            return $query->email;
        })
        ->addColumn('action',function($query){
            $str = '<div class="btn-group dropdown">
            <a href="javascript: void(0);" class="table-action-btn dropdown-toggle arrow-none btn btn-light btn-sm" data-toggle="dropdown" aria-expanded="false"><i class="mdi mdi-dots-horizontal"></i></a>
            <div class="dropdown-menu dropdown-menu-right">
            <a data-toggle="tooltip" data-placement="top" title="Logo Update" class="dropdown-item" href="'.route('companies.edit',$query->id).'"><i class="fas fa-landmark mr-1 text-muted font-18 vertical-middle"></i>Update</a>
            <a data-toggle="tooltip" data-placement="top" title="Delete Logo" class="dropdown-item" href="javascript::void(0)" onclick="companyDelete(this,'.$query->id.')"><i class="fas fa-trash-alt mr-1 text-muted font-18 vertical-middle"></i>Delete</a>
            </div></div>';
            return $str;
          })
        ->rawColumns(['logo','name','email','action'])
        ->make(true);
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('company.insert');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if($request->ajax())
        {
            $attributes = $request->all();
            $validateArray = [
                'name' => 'required|string',
                'email' => 'required|string|email:rfc,dns',
                'image' => ['required',new ValidateIsimage()]
            ];
            $validator = Validator::make($attributes, $validateArray);
            if ($validator->fails())
            {
                return response()->json(["success" => false, 'type' => 'validation-error', 'error' => $validator->errors()]);
            }
            
            if(isset($attributes['image']) && !empty($attributes['image']))
            {
                $file = $attributes['image'];
                @list($type, $file) = explode(';', $file);
                @list(, $file) = explode(',', $file);
                @list(, $extension) = explode('/', $type);
                $unique_name = rand().'.'. getExtension($extension);
                $filePath = 'logo/'. $unique_name;
                Storage::disk('public')->put($filePath, base64_decode($file));
                
                $image_data = [
                    'logo' =>  $filePath,
                ];
                $attributes = array_merge($attributes,$image_data);
            }
            
                
            
            // $banner->update($banner_data);            
            $logo = Company::create(
            [
                'name' => $attributes['name'],
                'email' => $attributes['email'],
                'logo' => $attributes['logo'],
            ]);
            
            return response()->json(["success" => true,'message'=>'Successfully inserted' ]);
        }
        return response()->json(['success' => false, "error" => 'Un authorised request']); 
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $company = Company::find($id);
        if(!$company)
        {
            Session::flash('message', "Company not found");
            Session::flash('type', 'error');
            return redirect()->route('companies.index');
        }
        return view('company.edit',\compact('company'));
        
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
    
        if($request->ajax())
        {
            $attributes = $request->all();
            $validateArray = [
                'company_id'=>'required',
                'name' => 'required|string',
                'email' => 'required|string|email:rfc,dns',
                'image' => 'required_if:company_id,==,0',new ValidateIsimage(),
            ];
            $validator = Validator::make($attributes, $validateArray);
            if ($validator->fails())
            {
                return response()->json(["success" => false, 'type' => 'validation-error', 'error' => $validator->errors()]);
            }
            $company = Company::find($id);
            if(isset($attributes['image']) && !empty($attributes['image']))
            {
                $file = $attributes['image'];
                @list($type, $file) = explode(';', $file);
                @list(, $file) = explode(',', $file);
                @list(, $extension) = explode('/', $type);
                $unique_name = rand().'.'. getExtension($extension);
                $filePath = 'logo/'. $unique_name;
                Storage::disk('public')->put($filePath, base64_decode($file));
                if($company && $company->logo)
                {
                    Storage::disk('public')->delete($company->logo);
                }
                $image_data = [
                    'logo' =>  $filePath,
                ];
                $attributes = array_merge($attributes,$image_data);
            }
            else if($company!=null){
                $image_data = [
                    'logo' =>  $company->logo,
                ];
                $attributes = array_merge($attributes,$image_data);
            }
             
            $company->name = $attributes['name'];
            $company->email = $attributes['email'];
            $company->logo = $attributes['logo'];
            $company->save();
            return response()->json(["success" => true,'message'=>'Successfully updated' ]);
        }
        return response()->json(['success' => false, "error" => 'Un authorised request']); 
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request,$id)
    {
        if($request->has('id'))
        {
            $company = Company::find($request->id);
            if(!empty($company))
            {
                if($company->logo)
                {
                    Storage::disk('public')->delete($company->logo);
                }
                $company->delete();
                return response()->json(['success'=>true,'message'=>'Record has been deleted successfully!']);
            }
        }
        return response()->json(['success'=>false,'error'=>'record does not find']);
    }
}
