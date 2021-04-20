<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Activitylog\Models\Activity;
use Spatie\Permission\Models\Role;
use Yajra\DataTables\Facades\DataTables;
use App\Mail\EmailForQueuing;
use Route;
use Validator;
use App\User;
use App\Branch;
use App\Item;
use App\Loan;
use App\Initial;
use App\Warehouse;
use App\StockRequest;
use App\PreparedItem;
use App\Category;
use App\Stock;
use App\Defective;
use App\Customer;
use App\CustomerBranch;
use DB;
use App\UserLog;
use Carbon\Carbon;
use Mail;
use Auth;
use Config;

class HomeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    public function report()
    {
        $title = 'Report a problem';
        return view('report', compact('title'));
    }
    public function reportAproblem(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'module' => ['required', 'string', 'min:5', 'max:255'],
            'remarks' => ['required', 'string', 'min:10'],
        ]);
        if ($validator->passes()) {
            $user = auth()->user()->name.' '.auth()->user()->lastname;
            $branch = auth()->user()->branch->branch;
            $email = auth()->user()->email;
            $config = array(
                'driver'     => 'smtp',
                'host'       => 'mail.ideaserv.com.ph',
                'port'       => '465',
                'from'       => array('address' => 'bsms.support@ideaserv.com.ph', 'name' => 'support'),
                'encryption' => 'ssl',
                'username'   => 'bsms.support@ideaserv.com.ph',
                'password'   => 'q1w2e3r4t5y6'
            );
            Config::set('mail', $config);
            $allemails = array();
            $allemails[] = 'jerome.lopez.ge2018@gmail.com';
            $send = Mail::send('report-a-problem', 
                [
                'branch'=>auth()->user()->branch->branch,
                'module'=>$request->input('module'),
                'email'=>auth()->user()->email,
                'remarks'=>$request->input('remarks')
                ],
                function( $message) use($user, $branch, $email){ 
                $message->to('bsms.support@ideaserv.com.ph', 'bsms.support@ideaserv.com.ph')->subject('Report A Problem'); 
                $message->from($email, 'Report A Problem - '.$user. ' - '.$branch);
            });

            $email = auth()->user()->email;
            $name = auth()->user()->name. ' '. auth()->user()->lastname;
            Mail::to($email, $name)->later(15, new EmailForQueuing);
            /*Mail::later(15, 'responder', 
                [
                'fullname'=>auth()->user()->name.' '.auth()->user()->lastname,
                ],
                function( $message) use($email, $name){ 
                $message->to($email, $name)->subject('Report A Problem'); 
                $message->from('bsms.support@ideaserv.com.ph', 'BSMS Support Team');
            });*/
        
            return redirect()->back()->with('success', 'Thank you '.$user.'! Your report has been successfully sent. Thank you for contacting us.');
        }
    }

    public function index()
    {
        if (auth()->user()->status == '0') {
            return redirect('logout');
        }
        $route = Route::current()->getname();
        if ($route == 'disposed') {
            $title = 'Disposed';
            return view('pages.disposed', compact('title'));
        }
        
        $title = 'Dashboard';
        $mail = StockRequest::wherein('status', ['4', 'INCOMPLETE'])->where( 'updated_at', '<', Carbon::now()->subDays(5))->first();
        //dd($mail);
        if ($mail) {
            $branch = Branch::where('id', $mail->branch_id)->first();
            $count = PreparedItem::where('request_no', $mail->request_no)->count();
            $items = PreparedItem::where('request_no', $mail->request_no)->get();
            //return $count;
            if ($count) {
                //return $items;
                foreach ($items as $item) {
                    $missing = Item::where('id', $item->items_id)->first();
                    $email = 'jerome.lopez.ge2018@gmail.com';
                    $gomail = Mail::send('unresolved', ['item'=>$missing->item, 'RDate'=>$mail->created_at, 'intransit'=>$mail->intransit, 'branch'=>$branch->branch],function( $message){ 
                        $message->to('jerome.lopez.ge2018@gmail.com', 'Jerome Lopez')->subject 
                            ('Unresolved Issue Notification'); 
                        $message->from('noreply@ideaserv.com.ph', 'Unresolved - NO-REPLY'); 
                    });
                if ($gomail){
                    return 'tama';
                }

                }
            }
        }
        StockRequest::wherein('status', ['4', 'INCOMPLETE'])->where( 'updated_at', '<', Carbon::now()->subDays(5))->update(['status' => 'UNRESOLVED']);

        if (auth()->user()->hasrole('Viewer')) {
            return view('pages.pending', compact('title'));
        }

        if (auth()->user()->branch->branch != "Warehouse" && auth()->user()->branch->branch != 'Main-Office' && !auth()->user()->hasanyrole('Repair', 'Returns Manager')) {
            $units = Stock::where('status', 'in')->where('branch_id', auth()->user()->branch->id)->count();
            $returns = Defective::wherein('status', ['For return', 'For receiving'])->where('branch_id', auth()->user()->branch->id)->count();
            $stockreq = StockRequest::where('branch_id', auth()->user()->branch->id)
                ->wherein('status', ['PENDING', 'SCHEDULED'])
                ->where('stat', '=', 'ACTIVE')
                ->count();
            $sunits = Stock::where('status', 'service unit')->where('branch_id', auth()->user()->branch->id)->count();
            $loans = Loan::where('status', 'pending')->where('to_branch_id', auth()->user()->branch->id)->count();
            return view('pages.home', compact('stockreq', 'units', 'returns', 'sunits', 'title', 'loans'));
        }else if (auth()->user()->hasrole('Repair')){
            return view('pages.warehouse.return', compact('title'));
        }else if (auth()->user()->hasrole('Returns Manager')){
            return view('pages.unrepair', compact('title'));
        }else{
            $stockreq = StockRequest::where('status', 'PENDING')
                ->where('stat', '=', 'ACTIVE')
                ->count();
            $units = Warehouse::where('status', 'in')->count();
            $returns = Defective::where('status', 'Repaired')->count();
            $unresolved = StockRequest::where('status', 'UNRESOLVED')->where('stat', 'ACTIVE')->count();
            $resolved = StockRequest::where('status', 'UNRESOLVED')->where('stat', 'RESOLVED')->count();
            return view('pages.home', compact('stockreq', 'units', 'returns', 'title', 'unresolved', 'resolved'));
        }
    }
    public function log()
    {
        $title = 'Activities';
        return view('pages.home', compact('title'));
    }
    public function myapi(Request $request)
    {
        $stockreq = StockRequest::where('id', $request->id)
                ->wherein('status', ['PENDING', 'SCHEDULED'])
                ->where('stat', '=', 'ACTIVE')
                ->update(['stat' => 'mytest']);
        $mystock = StockRequest::where('requests.id', $request->id)
        ->wherein('requests.status', ['PENDING', 'SCHEDULED'])
        ->join('branches', 'branches.id', 'branch_id')
        ->first();
        return $mystock->branch;
    }
    public function unrepair()
    {
        $title = 'Unrepairable items';
        return view('pages.unrepair', compact('title'));
    }
    public function print($id)
    {
        sleep(2);
        $request = StockRequest::where('request_no', $id)->first();
        $title = 'Print Preview';
        return view('pages.warehouse.print', compact('request', 'title'));
    }
    public function printDefective()
    {
        sleep(2);
        $request = Defective::where('branch_id', auth()->user()->branch->id)->where('status', 'For receiving')->get();
        $title = 'Print Preview';
        return view('pages.branch.print', compact('title'));
    }
    public function initial($id)
    {
        if ($id == 'shadow046') {
            $items = Item::all();
            $branches = Branch::all();
            foreach ($branches as $branchs) {
                foreach ($items as $item) {
                    $initial = new Initial;
                    $initial->items_id = $item->id;
                    $initial->branch_id = $branchs->id;
                    $initial->qty = 0;
                    $initial->save();
                }
            }
        }
        if ($id == 'ini') {
            $items = Item::all();
            $branches = Branch::all();
            foreach ($branches as $branchs) {
                foreach ($items as $item) {
                    if ($branchs->id != 1) {
                        $stock = new Stock;
                        $stock->category_id = $item->category_id;
                        $stock->branch_id = $branchs->id;
                        $stock->items_id = $item->id;
                        $stock->serial = 'N/A';
                        $stock->status = 'in';
                        $stock->save();
                    }
                }
            }
        }
        if ($id == 'initial') {
            $items = Item::all();
            $branches = Branch::all();
            foreach ($branches as $branchs) {
                foreach ($items as $item) {
                    if ($branchs->id != 1) {
                        $ini = Initial::where('items_id', $item->id)
                            ->where('branch_id', $branchs->id)
                            ->first();
                        $ini->qty = '5';
                        $ini->save();
                    }
                }
            }
        }
        if ($id == 'ware') {
            $items = Item::all();
                foreach ($items as $item) {
                    $ware = new Warehouse;
                    $ware->user_id = auth()->user()->id;
                    $ware->category_id = $item->category_id;
                    $ware->items_id = $item->id;
                    $ware->serial = '-';
                    $ware->status = 'in';
                    $ware->save();
                }
        }
        dd(Stock::all());
    }
    public function activity()
    {
        if (auth()->user()->hasAnyRole('Warehouse Manager', 'Editor',  'Manager')) {
            $act = UserLog::select('user_logs.*', 'users.email')
                ->join('users', 'users.id', '=', 'user_logs.user_id')
                ->orderBy('id', 'desc')
                ->take(200)
                ->get();
        }
        if (auth()->user()->roles->first()->name == 'Head') {
            $myuser = [];
            $user = User::where('branch_id', auth()->user()->branch->id)->get();
            foreach ($user as $user) {
                $myuser[] = $user->id;
            }
            $act = UserLog::wherein('user_id', $myuser)->orderBy('id', 'desc')->take(200)->get();
        }
        if (auth()->user()->hasAnyRole('Tech', 'Repair', 'Encoder')) {
            $act = UserLog::where('user_id', auth()->user()->id)->orderBy('id', 'desc')->take(200)->get();
        }
        return DataTables::of($act)
        ->addColumn('date', function (UserLog $request){
            return $request->updated_at->toFormattedDateString(). ' '.$request->updated_at->toTimeString();
        })
        ->addColumn('time', function (UserLog $request){
            return $request->updated_at->toTimeString();
        })
        ->addColumn('username', function (UserLog $request){
            $username = User::where('id', $request->user_id)->first();
            return $username->email;
        })
        ->addColumn('fullname', function (UserLog $request){
            $username = User::where('id', $request->user_id)->first();
            return $username->name.' '.$username->middlename.' '. $username->lastname;
        })
        ->addColumn('userlevel', function (UserLog $request){
            $username = User::where('id', $request->user_id)->first();
            return $username->roles->first()->name;
        })
        ->make(true);
    }
    public function service_units()
    {
        $users = User::all();
        return view('pages.service-units', compact('users'));
    }

    public function imp()
    {
        $sources = DB::table('customer_branch')->get();
            foreach ($sources as $source) {
                $customer = Customer::where('code', $source->custcode)->first();
                if ($customer) {
                    $branch = CustomerBranch::where('customer_id', $customer->id)->where('code', $source->brchcode)->first();
                    if (!$branch) {
                        $add = new CustomerBranch;
                        $add->customer_branch = $source->brchname;
                        $add->address = $source->addr1;
                        $add->code = $source->brchcode;
                        $add->customer_id = $customer->id;
                        if ($source->contact_number) {
                            $add->contact = $source->contact_number;
                        }
                        if ($source->contact_person) {
                            $add->cperson = $source->contact_person;
                        }
                        $add->status = '1';
                        $add->save();
                    }
                }
            }
        return dd(CustomerBranch::all());
    }
    public function preventive()
    {
        $title = "Preventive Maintenance";
        $categories = Category::orderBy('category')->get();
        if (!auth()->user()->hasanyrole('Head', 'Tech')) {
            return redirect('/');
        }
        return view('pages.pm', compact('title', 'categories'));
    }
    public function getprint(Request $request, $id)
    {
        $stock = StockRequest::where('request_no', $id)->first();
        $consumable = PreparedItem::select('item', 'uom', 'prepared_items.id as id', 'items_id', 'request_no', 'serial', 'schedule')
            ->where('branch_id', $stock->branch_id)
            ->where('request_no', $id)
            ->where('schedule', $request->schedule)
            ->whereNotin('uom', ['Unit'])
            ->join('items', 'items.id', '=', 'items_id')
            ->selectRaw('count(items_id) as quantity')
            ->groupBy('items_id')
            ->get();
        $unit = PreparedItem::select('item', 'uom', 'prepared_items.id as id', 'items_id', 'request_no', 'serial', 'schedule')
            ->where('branch_id', $stock->branch_id)
            ->where('request_no', $id)
            ->where('schedule', $request->schedule)
            ->whereNotin('uom', ['Pc', 'Meter'])
            ->join('items', 'items.id', '=', 'items_id')
            ->selectRaw('count(prepared_items.id) as quantity')
            ->groupBy('prepared_items.id')
            ->get();
        $result = $unit->merge($consumable);
        return DataTables::of($result)
        ->addColumn('quantity', function (PreparedItem $PreparedItem){
            if ($PreparedItem->quantity != 1) {
                return $PreparedItem->quantity.' - '.$PreparedItem->items->UOM.'s';
            }else{
                return $PreparedItem->quantity.' - '.$PreparedItem->items->UOM;
            }
        })
        ->make(true);
    }
    public function save(Request $request){
        Storage::disk('open')->put('company_info/description.txt', $request->description);
    }
    public function convert(){
        return view('pages.test');
    }
}