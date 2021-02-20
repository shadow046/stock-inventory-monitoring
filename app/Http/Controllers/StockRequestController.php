<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Carbon\Carbon;
use App\StockRequest;
use App\RequestedItem;
use App\PreparedItem;
use App\Warehouse;
use App\Category;
use App\Item;
use App\Stock;
use App\Branch;
use App\User;
use App\Initial;
use App\UserLog;
use Mail;
use Auth;
class StockRequestController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    public function index()
    {
        if (auth()->user()->hasanyrole('Repair')) {
            return redirect('/');
        }
        $title = 'Stock Request';
        $stocks = Warehouse::select('items_id', 'serial', \DB::raw('SUM(CASE WHEN status = \'in\' THEN 1 ELSE 0 END) as stock'))
            ->where('status', 'in')
            ->groupBy('items_id')->get();
        $categories = Category::all();
        return view('pages.stock-request', compact('stocks', 'categories', 'title'));
    }
    public function getItemCode(Request $request){
        $data = Item::select('id', 'item')->where('category_id', $request->id)->get();
        return response()->json($data);
    }
    public function getCode(Request $request){
        $initials = Initial::where('branch_id', auth()->user()->branch->id)
            ->join('items', 'items_id', '=', 'items.id')
            ->where('category_id', $request->id)
            ->get();
        $icode = [];
        $itm =[];
        foreach ($initials as $initial) {
            $count = Stock::where('stocks.status', 'in')
                ->where('branch_id', auth()->user()->branch->id)
                ->where('items_id', $initial->items_id)
                ->count();
            if ($count < $initial->qty) {
                $itemcode = Item::select('id', 'item')->where('id', $initial->items_id)->first();
                if(!in_array($itemcode, $icode)){
                    array_push($icode, $itemcode);
                }
            }
        }
        return response()->json(array_filter($icode));
    }
    public function getCatReq(Request $request){
        $catreqs = RequestedItem::select('categories.category', 'categories.id')
            ->where('request_no', $request->reqno)
            ->join('items', 'items.id', '=', 'requested_items.items_id')
            ->join('categories', 'categories.id', '=', 'items.category_id')
            ->get();
        return response()->json($catreqs);
    }
    public function getStock(Request $request){
        if (auth()->user()->branch->branch == 'Warehouse') {
            $data = Warehouse::select(\DB::raw('SUM(CASE WHEN status = \'in\' THEN 1 ELSE 0 END) as stock'))
                ->where('status', 'in')
                ->where('items_id', $request->id)
                ->groupBy('items_id')
                ->get();
        }else{
            $data = Stock::select(\DB::raw('SUM(CASE WHEN status = \'in\' THEN 1 ELSE 0 END) as stock'))
                ->where('status', 'in')
                ->where('items_id', $request->id)
                ->where('branch_id', auth()->user()->branch->id)
                ->groupBy('items_id')
                ->get();
        }
        return response()->json($data);
    }
    public function getSerials(Request $request){
        if (auth()->user()->branch->branch == 'Warehouse') {
            $data = Warehouse::select('items_id', 'serial')
                ->where('status', 'in')
                ->where('items_id', $request->id)
                ->get();
        }else{
            $data = Stock::select('id', 'items_id', 'serial')
                ->where('status', 'in')
                ->where('items_id', $request->id)
                ->where('branch_id', auth()->user()->branch->id)
                ->get();
        }
        return response()->json($data);
    }
    public function getcon(Request $request){
        $data = PreparedItem::select('id')->where('request_no', $request->reqno)->where('items_id', $request->itemsid)->get();
        return response()->json($data);
    }
    public function getsendDetails(Request $request, $id){
        $consumable = PreparedItem::select('uom', 'prepared_items.id as id', 'items_id', 'request_no', 'serial', 'schedule')
            ->where('request_no', $id)
            ->whereNotin('uom', ['Unit'])
            ->join('items', 'items.id', '=', 'items_id')
            ->selectRaw('count(items_id) as quantity')
            ->groupBy('items_id')
            ->get();
        $unit = PreparedItem::select('uom', 'prepared_items.id as id', 'items_id', 'request_no', 'serial', 'schedule')
            ->where('request_no', $id)
            ->whereNotin('uom', ['Pc', 'Meter'])
            ->join('items', 'items.id', '=', 'items_id')
            ->selectRaw('count(prepared_items.id) as quantity')
            ->groupBy('prepared_items.id')
            ->get();
        $result = $unit->merge($consumable);
        return DataTables::of($result)
        ->addColumn('item_name', function (PreparedItem $PreparedItem){
            return strtoupper($PreparedItem->items->item);
        })
        ->addColumn('serial', function (PreparedItem $PreparedItem){
            return strtoupper($PreparedItem->serial);
        })
        ->addColumn('quantity', function (PreparedItem $PreparedItem){
            if ($PreparedItem->quantity != 1) {
                return $PreparedItem->quantity.' - '.$PreparedItem->items->UOM.'s';
            }else{
                return $PreparedItem->quantity.' - '.$PreparedItem->items->UOM;
            }
        })
        ->make(true);
    }
    public function generateRandomNumber() {
        $random = mt_rand(1, 999); 
        $today = Carbon::now()->format('d-m-Y');
        $number = $today.'-'.$random;
        if ($this->barcodeNumberExists($number)) {
            return generateBarcodeNumber();
        }
        return response()->json($number);
    }
    public function barcodeNumberExists($number) {
        return StockRequest::where('request_no', $number)->exists();
    }
    public function prepitemdetails(Request $request, $id)
    {
        return DataTables::of(PreparedItem::where('request_no', $id)->get())
        ->addColumn('item_name', function ($PreparedItem){

            return strtoupper($PreparedItem->items->item);
        })
        ->make(true);
    }   
    public function getReqDetails(Request $request)
    {
        return response()->json(RequestedItem::whereNot('status', 'delivered')->where('request_no', $request->reqno)->get());
    }
    public function getuomq(Request $request)
    {
        return response()->json(RequestedItem::select('quantity', 'id')->where('request_no', $request->reqno)->where('items_id', $request->itemid)->first());
    }
    public function updateRequestDetails(Request $request, $id)
    {
        RequestedItem::where('request_no', $id)->where('items_id', $request->item)->decrement('quantity', 1);
        $updated = RequestedItem::where('request_no', $id)->where('items_id', $request->item)->first();
        if ($updated->quantity == 0) {
            $data = $updated->delete();
            return response()->json($data);
        }else{
            return response()->json(true);
        }
    }
    public function getRequestDetails(Request $request, $id)
    {
        return DataTables::of(RequestedItem::where('request_no', $id)->get())
        ->addColumn('item_name', function (RequestedItem $RequestedItem){
            return strtoupper($RequestedItem->items->item);
        })
        ->addColumn('uom', function (RequestedItem $RequestedItem){
            $uom = Item::select('UOM as uom')->where('id', $RequestedItem->items->id)->first();
            return $uom->uom;
        })
        ->addColumn('qty', function (RequestedItem $RequestedItem){
            if ($RequestedItem->quantity != 1) {
                return $RequestedItem->quantity. ' ' .$RequestedItem->items->UOM.'s';
            }else{
                return $RequestedItem->quantity. ' ' .$RequestedItem->items->UOM;
            }
        })
        ->addColumn('stock', function (RequestedItem $RequestedItem){
            $data = Warehouse::select(\DB::raw('SUM(CASE WHEN status = \'in\' THEN 1 ELSE 0 END) as stock'))
                ->where('status', 'in')
                ->where('items_id',$RequestedItem->items->id)
                ->groupBy('items_id')
                ->first();
            if (!$data) {
                $stock = 0;
            }else{
                $stock = $data->stock;
            }
            return $stock;
        })
        ->addColumn('stockuom', function (RequestedItem $RequestedItem){
            $uom = Item::select('UOM as uom')->where('id', $RequestedItem->items->id)->first();
            $data = Warehouse::select(\DB::raw('SUM(CASE WHEN status = \'in\' THEN 1 ELSE 0 END) as stock'))
                ->where('status', 'in')
                ->where('items_id',$RequestedItem->items->id)
                ->groupBy('items_id')
                ->first();
            if (!$data) {
                $stock = 0;
            }else{
                $stock = $data->stock;
            }
            if ($stock != 1) {
                return $stock. ' ' . $uom->uom.'s';
            }else{
                return $stock. ' ' . $uom->uom;
            }
        })
        ->make(true);
    }   
    public function pcount(Request $request)
    {
        $stock = StockRequest::where('request_no', $request->reqno)
                ->first();
        return response()->json($stock);
    }
    public function getRequests()
    {
        $user = auth()->user()->branch->id;
        if (auth()->user()->branch->branch != 'Warehouse'){
            $stock = StockRequest::wherein('status',  ['0', '1', '4', '5', '8'])
                ->where('branch_id', $user)
                ->get();
        }else if(auth()->user()->hasRole('Viewer')){
            $stock = StockRequest::wherein('status',  ['0', '1', '4', '5', '6', '8'])
                ->get();
        }else{
            $stock = StockRequest::wherein('status',  ['0', '1', '4', '5', '6', '8'])
                ->get();
        }
        return DataTables::of($stock)
        ->setRowData([
            'data-id' => '{{ $request_no }}',
            'data-status' => '{{ $status }}',
            'data-user' => '{{ $user_id }}',
        ])
        ->addColumn('status', function (StockRequest $request){
            if ($request->status == 0) {
                return 'PENDING';
            }else if ($request->status == 1){
                return 'SCHEDULED';
            }else if ($request->status == 4){
                return 'INCOMPLETE';
            }else if ($request->status == 5){
                return 'RESCHEDULED';
            }else if ($request->status == 6){
                return 'UNRESOLVED';
            }else if ($request->status == 8){
                return 'PARTIAL';
            }
        })
        ->addColumn('sched', function (StockRequest $request){
            return $request->schedule;
        })
        ->addColumn('created_at', function (StockRequest $request){
            return $request->created_at->toFormattedDateString().' '.$request->created_at->toTimeString();
        })
        ->addColumn('reqBy', function (StockRequest $request){
            return strtoupper($request->user->name);
        })
        ->addColumn('branch', function (StockRequest $request){
            return strtoupper($request->branch->branch);
        })
        ->addColumn('area', function (StockRequest $request){
            return strtoupper($request->area->area);
        })
        ->addColumn('pending', function (StockRequest $request){
            return strtoupper($request->pending);
        })
        ->make(true);
    }
    public function store(Request $request)
    {
        if ($request->stat == 'ok') {
            $reqno = new StockRequest;
            $reqno->request_no = $request->reqno;
            $reqno->user_id = auth()->user()->id;
            $reqno->branch_id = auth()->user()->branch->id;
            $reqno->area_id = auth()->user()->area->id;
            $reqno->status = 0;
            $log = new UserLog;
            $log->activity = "Create Stock Request no. $request->reqno";
            $log->user_id = auth()->user()->id;
            $reqno->save();
            sleep(1);
            $reqitem = RequestedItem::select('items.item', 'quantity')
                ->where('request_no', $request->reqno)
                ->join('items', 'items.id', '=', 'requested_items.items_id')
                ->get();
            $cc = User::select('email')
                ->where('branch_id', '1')
                ->join('model_has_roles', 'model_id', '=', 'users.id')
                ->where('role_id', '6')
                ->get();
            $allemails = array();
            $allemails[] = 'jerome.lopez.ge2018@gmail.com';
            foreach ($cc as $email) {
                $allemails[]=$email->email;
            }
            /*Mail::send('mail', ['reqitem' => $reqitem, 'reqno' => $request->reqno, 'branch'=>auth()->user()->branch->branch],function( $message) use ($allemails){ 
                $message->to('gerard.mallari@gmail.com', 'Gerald Mallari')->subject 
                    (auth()->user()->branch->branch); 
                $message->from('no-reply@ideaserv.com.ph', 'NO REPLY'); 
                $message->cc($allemails); 
            });*/
            $data = $log->save();
        }
        if ($request->stat == 'notok') {
            $reqitem = new RequestedItem;
            $reqitem->request_no = $request->reqno;
            $reqitem->items_id = $request->item;
            $reqitem->quantity = $request->qty;
            $data = $reqitem->save();
        }
        return response()->json($data);
    }

    public function prepitem(Request $request)
    {
        $preparedItem = PreparedItem::where('branch_id', $request->branchid)
            ->where('request_no', $request->reqno)
            ->first();

        if ($preparedItem) {
            $data = '1';
        }else{
            $data = '0';
        }
        return response()->json($data);
    }
    public function received(Request $request)
    {
        $data = '0';
        foreach ($request->id as $del) {
            $preparedItems = PreparedItem::select('prepared_items.items_id as itemid', 'prepared_items.serial as serial')
                ->join('items', 'prepared_items.items_id', '=', 'items.id')
                ->where('branch_id', auth()->user()->branch->id)
                ->where('request_no', $request->reqno)
                ->where('prepared_items.id', $del)
                ->first();
            $prepared = PreparedItem::where('branch_id', auth()->user()->branch->id)
                ->where('request_no', $request->reqno)
                ->where('prepared_items.id', $del)
                ->first();
            $items = Item::where('id', $preparedItems->itemid)->first();
            $stock = new Stock;
            $stock->category_id = $items->category_id;
            $stock->branch_id = auth()->user()->branch->id;
            $stock->items_id = $preparedItems->itemid;
            $stock->user_id = auth()->user()->id;
            $stock->serial = $preparedItems->serial;
            $stock->status = 'in';
            $stock->save();
            $log = new UserLog;
            $log->activity = "Received $items->item(S/N: $preparedItems->serial) with Request no. $request->reqno ";
            $log->user_id = auth()->user()->id;
            $log->save();
            $prepared->delete();
        }
        $preparedItem = PreparedItem::where('branch_id', auth()->user()->branch->id)
            ->where('request_no', $request->reqno)
            ->first();
        if ($preparedItem) {
            $reqno = StockRequest::where('request_no', $request->reqno)->first();
            if ($reqno->status == 8) {
                $reqno->status = $request->status;
            }else{
                $reqno->status = 4;
            }
            $reqno->save();
            $data = '1';
        }else{
            $reqno = StockRequest::where('request_no', $request->reqno)->first();
            $reqno->status = $request->status;
            $reqno->save();
        }
        return response()->json($data);
    }
    public function update(Request $request)
    {
        if ($request->stat == 'ok') {
            $reqno = StockRequest::where('request_no', $request->reqno)->first();
            $reqno->status = $request->status;
            $reqno->schedule = $request->datesched;
            $reqno->save();
            $prepitem = PreparedItem::select('items.item', 'serial', 'branch_id')
                ->where('request_no', $request->reqno)
                ->join('items', 'items.id', '=', 'prepared_items.items_id')
                ->get();
            $reqbranch = PreparedItem::select('branch_id')
                ->where('request_no', $request->reqno)
                ->first();
            $branch = Branch::where('id', $request->branchid)->first();
            $email = $branch->email;
            /*Mail::send('sched', ['prepitem' => $prepitem, 'sched'=>$request->datesched,'reqno' => $request->reqno,'branch' =>$branch],function( $message) use ($branch, $email){ 
                $message->to($email, $branch->head)->subject 
                    (auth()->user()->branch->branch); 
                $message->from('no-reply@ideaserv.com.ph', 'NO REPLY - Warehouse'); 
                $message->cc(['emorej046@gmail.com', 'gerard.mallari@gmail.com']); 
            });*/
            $data = true;
        }else if($request->stat == 'resched'){
            if ($request->status == '5') {
                $reqno = StockRequest::where('request_no', $request->reqno)->first();
                $reqno->status = $request->status;
                $reqno->schedule = $request->datesched;
                $data = $reqno->save();
            }else if($request->status == '6') {
                $reqno = StockRequest::where('request_no', $request->reqno)->first();
                $reqno->status = $request->status;
                $data = $reqno->save();
            }
        }else{
            $item = Warehouse::where('status', 'in')
                ->where('items_id', $request->item)
                ->first();
            $item->status = 'sent';
            $item->request_no = $request->reqno;
            $item->branch_id = $request->branchid;
            $item->schedule = $request->datesched;
            $item->user_id = auth()->user()->id;
            $item->save();
            $scheditem = Item::where('id', $request->item)->first();
            $sched = StockRequest::where('request_no', $request->reqno)->first();
            $prep = new PreparedItem;
            $prep->items_id = $request->item;
            $prep->request_no = $request->reqno;
            $prep->serial = $request->serial;
            $prep->branch_id = $request->branchid;
            $prep->schedule = $request->datesched;
            $prep->user_id = auth()->user->id;
            $prep->save();
            $log = new UserLog;
            $log->activity = "Schedule $scheditem->item(S/N: $request->serial) with Request no. $request->reqno ";
            $log->user_id = auth()->user()->id;
            $data = $log->save();
        }
        return response()->json($data);
    }
    public function dest(Request $request)
    {
        $delete = StockRequest::where('request_no', $request->reqno)->where('status', '0')->first();
        $delete->status = 3;
        $log = new UserLog;
        $log->activity = "Delete request no. $request->reqno" ;
        $log->user_id = auth()->user()->id;
        $log->save();
        $data = $delete->save();
        return response()->json($data);
    }
}