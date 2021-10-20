<?php

namespace App\Http\Controllers;

use DB;
use Auth;
use Session;
use Carbon\Carbon;
use App\Company;
use App\Employe;

use Yajra\Datatables\Datatables;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class EmployeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function __construct() {
        $this->middleware('admin');
    }
    public function index(Request $request)
    {
        return view('employe.index');
    }

    public function list()
    {
        $query = Employe::where('id','!=',0);
        return DataTables::of($query) 
        ->addColumn('full_name',function($query){
            
            return $query->full_name;
        })
        ->addColumn('phone',function($query){
            return $query->phone;
        })
        ->addColumn('email',function($query){
            return $query->email;
        })
        ->addColumn('company',function($query){
            return $query->company->name;
        })
        ->addColumn('action',function($query){
            $str = '<div class="btn-group dropdown">
            <a href="javascript: void(0);" class="table-action-btn dropdown-toggle arrow-none btn btn-light btn-sm" data-toggle="dropdown" aria-expanded="false"><i class="mdi mdi-dots-horizontal"></i></a>
            <div class="dropdown-menu dropdown-menu-right">
            <a data-toggle="tooltip" data-placement="top" title="Employe Update" class="dropdown-item" href="'.route('emploies.edit',$query->id).'"><i class="fas fa-landmark mr-1 text-muted font-18 vertical-middle"></i>Update</a>
            <a data-toggle="tooltip" data-placement="top" title="Delete" class="dropdown-item" href="javascript::void(0)" onclick="employeDelete(this,'.$query->id.')"><i class="fas fa-trash-alt mr-1 text-muted font-18 vertical-middle"></i>Delete</a>
            </div></div>';
            return $str;
          })
        ->rawColumns(['full_name','phone','email','company','action'])
        ->make(true);
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $company_list = Company::get()->pluck('name','id');
        return view('employe.insert',\compact('company_list'));
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
                'firstname' => 'required|string',
                'lastname' => 'required|string',
                'phone' => 'required|string',
                'company_id' => 'required|integer',
                'email' => 'required|string|email:rfc,dns',
            ];
            $validator = Validator::make($attributes, $validateArray);
            if ($validator->fails())
            {
                return response()->json(["success" => false, 'type' => 'validation-error', 'error' => $validator->errors()]);
            }
                    
            Employe::create(
            [
                'firstname' => $attributes['firstname'],
                'lastname' => $attributes['lastname'],
                'company_id' => $attributes['company_id'],
                'email' => $attributes['email'],
                'phone' => $attributes['phone'],
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
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $employe = Employe::find($id);
        $company_list = Company::get()->pluck('name','id');
        if(!$employe)
        {
            Session::flash('message', "Employe not found");
            Session::flash('type', 'error');
            return redirect()->route('emploies.index');
        }
        return view('employe.edit',\compact('employe','company_list'));
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
                'employe_id'=>'required',
                'firstname' => 'required|string',
                'lastname' => 'required|string',
                'phone' => 'required|string',
                'company_id' => 'required|integer',
                'email' => 'required|string|email:rfc,dns',
            ];
            $validator = Validator::make($attributes, $validateArray);
            if ($validator->fails())
            {
                return response()->json(["success" => false, 'type' => 'validation-error', 'error' => $validator->errors()]);
            }
            $employe = Employe::find($id);
            if(!$employe)
            {
                return response()->json(["success" => false,'error'=>'Employe not found' ]);
            }
            $employe->firstname = $attributes['firstname'];
            $employe->lastname = $attributes['lastname'];
            $employe->company_id = $attributes['company_id'];
            $employe->email = $attributes['email'];
            $employe->phone = $attributes['phone'];
            $employe->save();
            return response()->json(["success" => true,'message'=>'Successfully inserted' ]);
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
            $employe = Employe::find($request->id);
            if(!empty($employe))
            {
                $employe->delete();
                return response()->json(['success'=>true,'message'=>'Record has been deleted successfully!']);
            }
        }
        return response()->json(['success'=>false,'error'=>'record does not find']);
    }
}
