<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

use App\CustomerBranch;
use App\Customer;
use Mail;
class CustomerController extends Controller
{

    public function __construct()
    {
        
        $this->middleware('auth');
    }
    public function index()
    {
        if (auth()->user()->hasanyrole('Repair', 'Returns Manager', 'Viewer', 'Viewer PLSI', 'Viewer IDSI')) {
            return redirect('/');
        }
        $title = 'Customers';
        return view('pages.customer', compact('title'));
    }
    public function customertable()
    {
        $customer = Customer::all();
        return DataTables::of($customer)
        ->addColumn('code', function (Customer $customer){
            return mb_strtoupper($customer->code);
        })
        ->addColumn('customer', function (Customer $customer){
            return mb_strtoupper($customer->customer);
        })
        ->make(true);
    }

    public function branchindex(Request $request, $id)
    {
        $customer = Customer::find($id);
        $title = mb_strtoupper($customer->customer).' Branches';
        $customer = mb_strtoupper($customer->customer);
        return view('pages.customerbranch', compact('customer', 'title'));
    }
    public function getid(Request $request)
    {
        $data = CustomerBranch::query()->where('customer_branch', $request->customer)->first();
        return response()->json($data);
    }
    public function customerbranchtable($id)
    {
        $customer = CustomerBranch::where('customer_id', $id)->get();
        return DataTables::of($customer)
        ->addColumn('status', function (CustomerBranch $customer){
            if ($customer->status == 1) {
                return 'Active';
            }else{
                return 'Inactive';
            }
        })
        ->addColumn('customer_branch', function (CustomerBranch $customer){
            return mb_strtoupper($customer->customer_branch);
        })
        ->addColumn('code', function (CustomerBranch $customer){
            return mb_strtoupper($customer->code);
        })
        ->make(true);
    }
    public function hint(Request $request)
    {
        if ($request->client == 'yes') {
            $data = CustomerBranch::query()->select('customer')->where('customer_branch', $request->branch)
                ->join('customers', 'customers.id', 'customer_id')
                ->first();
            return response()->json($data->customer);
        }

        $data = CustomerBranch::query()->where('customer_branch', 'LIKE', '%'.$request->hint.'%')->take(5)->get();
        
        return response()->json($data);
        
    }
    public function branchtable()
    {
        $customer = CustomerBranch::query()->select('customer_branches.*', 'customer')
            ->join('customers', 'customers.id', 'customer_id')
            ->get();
        return DataTables::of($customer)
        ->addColumn('status', function (CustomerBranch $customer){
            if ($customer->status == 1) {
                return 'Active';
            }else{
                return 'Inactive';
            }
        })
        ->addColumn('customer_branch', function (CustomerBranch $customer){
            return mb_strtoupper($customer->customer_branch);
        })
        ->addColumn('code', function (CustomerBranch $customer){
            return mb_strtoupper($customer->code);
        })
        ->addColumn('customer', function (CustomerBranch $customer){
            return mb_strtoupper($customer->customer);
        })
        
        ->make(true);
    }
    public function store(Request $request)
    {
        if (Customer::where('code', mb_strtolower($request->input('customer_code')))->exists() || Customer::where('customer', mb_strtolower($request->input('customer_name')))->exists()) {
            $data = '0';
        }else{
            $customer = new Customer;
            $customer->code = $request->input('customer_code');
            $customer->customer = ucwords(mb_strtolower($request->input('customer_name')));
            $customer->save();
            $data = '1';
            /*$oldbranch = Branch::where('id', $olduser->branch_id)->first();
            $branch = Branch::where('id', $request->input('branch'))->first();*/
            Mail::send('create-customer', ['customer'=> ucwords(mb_strtolower($request->input('customer_name'))), 'code'=> $request->input('customer_code')],function( $message){ 
                $message->to('kdgonzales@ideaserv.com.ph', 'Kenneth Gonzales')->subject 
                    (auth()->user()->name.' '.auth()->user()->lastname.' has updated a user to Service center stock monitoring system.'); 
                $message->from('noreply@ideaserv.com.ph', 'NO REPLY - Create Customer'); 
            });
        }
        return response()->json($data);
    }

    public function branchadd(Request $request)
    {
        if (CustomerBranch::where('code', mb_strtolower($request->bcode))->where('customer_id', $request->bid)->exists() || CustomerBranch::where('customer_branch', mb_strtolower($request->bname))->where('customer_id', $request->bid)->exists()) {
            $data = '0';
        }else{
            $customerbranch = new CustomerBranch;
            $customerbranch->code = $request->bcode;
            $customerbranch->customer_branch = $request->bname;
            $customerbranch->customer_id = $request->bid;
            $customerbranch->address = $request->address;
            $customerbranch->contact = $request->number;
            $customerbranch->status = "1";
            $customerbranch->save();
            $sbu = Customer::where('id', $request->bid)->first();
            $data = '1';
            Mail::send('create-customerbranch', ['sbu'=>$sbu->code,'address'=> $request->address,'phone'=>$request->number, 'customer'=> ucwords(mb_strtolower($request->bname)), 'code'=> $request->bcode],function( $message){ 
                $message->to('kdgonzales@ideaserv.com.ph', 'Kenneth Gonzales')->subject 
                    (auth()->user()->name.' '.auth()->user()->lastname.' has updated a user to Service center stock monitoring system.'); 
                $message->from('noreply@ideaserv.com.ph', 'NO REPLY - Create Customer Branch'); 
            });
        }
        return response()->json($data);
    }
    public function branchupdate(Request $request)
    {
        if (CustomerBranch::where('code', mb_strtolower($request->bcode))->where('customer_id', $request->bid)->where('id', '!=', $request->id)->exists() || CustomerBranch::where('customer_branch', mb_strtolower($request->bname))->where('customer_id', $request->bid)->where('id', '!=', $request->id)->exists()) {
            $data = '0';
        }else{
            $customerbranch = CustomerBranch::where('id', $request->id)->first();
            $customerbranch->code = mb_strtolower($request->bcode);
            $customerbranch->customer_branch = mb_strtolower($request->bname);
            $customerbranch->address = $request->address;
            $customerbranch->contact = $request->number;
            $customerbranch->status = $request->status;
            $customerbranch->save();
            $data = '1';
            $sbu = Customer::where('id', $request->bid)->first();
            $data = '1';
            /*Mail::send('create-customerbranch', ['sbu'=>$sbu->code,'address'=> $request->address,'phone'=>$request->number, 'customer'=> ucwords(mb_strtolower($request->bname)), 'code'=> $request->bcode],function( $message){ 
                $message->to('kdgonzales@ideaserv.com.ph', 'Kenneth Gonzales')->subject 
                    (auth()->user()->name.' '.auth()->user()->lastname.' has updated a user to Service center stock monitoring system.'); 
                $message->from('noreply@ideaserv.com.ph', 'NO REPLY - Create Customer Branch'); 
            });*/
        }
        return response()->json($data);
    }
    public function update(Request $request)
    {
        if (Customer::where('code', mb_strtolower($request->input('customer_code')))->where('id', '!=', $request->input('myid'))->exists() || Customer::where('customer', mb_strtolower($request->input('customer_name')))->where('id', '!=', $request->input('myid'))->exists()) {
            $data = '0';
        }else{
            $customer = Customer::where('id', $request->input('myid'))->first();
            $customer->code = mb_strtolower($request->input('customer_code'));
            $customer->customer = mb_strtolower($request->input('customer_name'));
            $customer->save();
            $data = '1';
        }
        return response()->json($data);
    }
}