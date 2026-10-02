<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\User;
use App\Models\Venue;
use App\Models\Shipping;
use App\Models\DayMachine;
use App\Models\MachineDetail;
use App\Models\Temporary;
use App\Models\MachineDetailOrder;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use App\Mail\OrderMail;      //Mailableクラス
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Exception;

class OrderController extends Controller
{
    public function detail(Request $request){
        $id= $request->id;
        $data = [
            'id'=> $id,
            'user' => Auth::user(),
            'machines' => Order::join('machine_detail_order','orders.order_id','=','machine_detail_order.order_id')
                ->join('machine_details','machine_detail_order.machine_id','=','machine_details.machine_id')
                ->where('orders.order_id',$id)
                ->orderBy('machine_detail_order.machine_id','asc')
                ->get(),
            'orders' => Order::join('shippings','orders.order_id','=','shippings.order_id')
                ->join('venues','shippings.venue_id','=','venues.venue_id')
                ->join('users', 'orders.user_id', '=', 'users.id')
                ->where('orders.order_id', $id)
                ->first(),
        ];
        if($data['orders'] == null){
            return view('order/error', $data);
        }

        return view('order/detail', $data);
    }

    public function edit(Request $request){
        $id= $request->id;
        $data = [
            'id'=> $id,
            'input' => $request,
            'user' => Auth::user(),
            'machines' => Order::join('machine_detail_order','orders.order_id','=','machine_detail_order.order_id')
            ->join('machine_details','machine_detail_order.machine_id','=','machine_details.machine_id')
            ->where('orders.order_id',$id)
            ->get(),
            'orders' => Order::join('shippings','orders.order_id','=','shippings.order_id')
                ->join('venues','shippings.venue_id','=','venues.venue_id')
                ->where('orders.order_id', $id)
                ->first(),
        ];
        if($data['orders'] == null){
            return view('order/error', $data);
        }

        return view('order/edit', $data);
    }
    public function addpc(Request $request){
        $id= $request->id;
        $order = Order::where('order_id', $id)->first();
        $data = [
            'id'=> $id,
            // 'records' => MachineDetail::all(),
            'records' => MachineDetail::where('machine_is_expired','!=',1)->get(),
            'input' => $request,
            'order' => $order,
            'user' => Auth::user(),
            'machines' => Order::join('machine_detail_order','orders.order_id','=','machine_detail_order.order_id')
            ->join('machine_details','machine_detail_order.machine_id','=','machine_details.machine_id')
            ->where('orders.order_id',$id)
            ->get(),
        ];
        if($data['order'] == null){
            return view('order/error', $data);
        }

        //使用状況の確認（From:予約開始日からTo:予約終了日の間にday_machineテーブルに存在するmachine_idをピックアップする）
        // if($request->from != "" && $request->to != ""){
            $from = new Carbon($order->order_use_from);
            $to = new Carbon($order->order_use_to);
            $u = [];
            while($from <= $to){
                $u[] = $from->format('Y-m-d');
                $from->modify('1 day');
            }
        // dd($from, $to, $u);
            $dm = array_keys(array_count_values(DayMachine::whereIn('day', $u)->pluck('machine_id')->toarray()));
            $tm = array_keys(array_count_values(Temporary::whereIn('day', $u)->where('user_id', '<>', Auth::user()->id)->pluck('machine_id')->toarray()));
            $data['usage'] = array_merge($dm, $tm);
        // }else{
        //     $data['usage'] = [];
        // }

        
        return view('order/addpc', $data);
    }

    public function addprocess(Request $request, int $id){
        $order = Order::find($id);
        if($request->input('back') == '前の画面に戻る'){
            return redirect()->action('OrderController@detail', $id);
        }        
        try{
            DB::transaction(function ()use($request,$order,$id) {
                if(!is_array($request->id)){
                    throw new Exception("機材が選択されていません。", 499);
                }else{
                    $addid = $request->id;

                    foreach($addid as $i){
                        //machine_detail_orderテーブルに既登録がある場合は処理を中断
                        if(MachineDetailOrder::where('order_id', '=', $id)->where('machine_id', '=', $i)->first() != null){
                            throw new Exception("選択した機材は既に登録されています。", 499);
                        }
                        //day_machineテーブルに機材占有状況を展開
                        $start = new Carbon($order->order_use_from);
                        $end = new Carbon($order->order_use_to);
                        while($start <= $end){
                            // dump($start->format('Y-m-d'), $i, Daymachine::where('day', '=', $start)->where('machine_id', '=', $i)->first());
                            //day_machineテーブルに既登録がある場合は処理を中断
                            if(Daymachine::where('day', '=', $start)->where('machine_id', '=', $i)->first() != null){
                                throw new Exception("選択した機材は既に登録されています。", 499);
                            }
                        $day_machine = new DayMachine;
                        $day_machine->day = date($start->format('Y-m-d'));
                        $day_machine->machine_id = $i;
                        $day_machine->order_id = $id;
                        $day_machine->order_status = $order->order_status;
                        $day_machine->save();
                        $start->modify('1 day');
                        }

                        //machine_detail_orderテーブルに予約と機材IDの対応を1組ずつ展開
                        $mdo = new MachineDetailOrder;
                        $mdo->machine_id = $i;
                        $mdo->order_id = $id;
                        $mdo->order_status = $order->order_status;
                        $mdo->save();

                    }
                    // dd($request, $id, $request->id, $order);
                    
                    // throw new Exception("トランザクション阻止");

                }
            });
            
            return redirect()->route('order.detail', $id);

        }
        catch(\Exception $e){
            // echo($e->getMessage());
            if ($e->getcode() == 499){

            return redirect()->action('OrderController@addpc', $id)->withErrors($e->getMessage());

            }
            return back()->withErrors($e->getmessage());
        }
        finally{

        }
    }

    public function delpc(Request $request){
        $id= $request->id;
        $data = [
            'id'=> $id,
            'user' => Auth::user(),
            'machines' => Order::join('machine_detail_order','orders.order_id','=','machine_detail_order.order_id')
                ->join('machine_details','machine_detail_order.machine_id','=','machine_details.machine_id')
                ->where('orders.order_id',$id)
                ->orderBy('machine_detail_order.machine_id','asc')
                ->get(),
            'orders' => Order::join('shippings','orders.order_id','=','shippings.order_id')
                ->join('venues','shippings.venue_id','=','venues.venue_id')
                ->join('users', 'orders.user_id', '=', 'users.id')
                ->where('orders.order_id', $id)
                ->first(),
        ];
        if($data['orders'] == null){
            return view('order/error', $data);
        }

        return view('order/delpc', $data);
    }

    public function delprocess(Request $request, int $id){
        $order = Order::find($id);
        if($request->input('back') == '前の画面に戻る'){
            return redirect()->action('OrderController@detail', $id);
        }        
        try{
            DB::transaction(function ()use($request,$order,$id) {
                if(!is_array($request->id)){
                    throw new Exception("機材が選択されていません。", 499);
                }else{
                    $delid = $request->id;

                    foreach($delid as $i){
                        //machine_detail_orderテーブルに登録がない場合は処理を中断
                        if(MachineDetailOrder::where('order_id', '=', $id)->where('machine_id', '=', $i)->first() == null){
                            throw new Exception("選択した機材は登録されていません。既に削除されている可能性があります。", 499);
                        }
                        //day_machineテーブルの機材占有状況を展開
                        $start = new Carbon($order->order_use_from);
                        $end = new Carbon($order->order_use_to);
                        while($start <= $end){
                            // dump($start->format('Y-m-d'), $i, Daymachine::where('day', '=', $start)->where('machine_id', '=', $i)->first());
                            //day_machineテーブルに既に登録がない場合は処理を中断
                            if(Daymachine::where('day', '=', $start)->where('machine_id', '=', $i)->first() == null){
                                throw new Exception("選択した機材は登録されていません。既に削除されている可能性があります。", 499);
                            }
                        $day_machine = new DayMachine;
                        $day_machine->where('day', '=', $start)->where('machine_id', '=', $i)->delete();
                        $start->modify('1 day');
                        }

                        //machine_detail_orderテーブルにある予約と機材IDの対応を1組ずつ削除
                        $mdo = new MachineDetailOrder;
                        // dd($mdo->where('order_id', '=', $id)->where('machine_id', '=', $i)->get());

                        $mdo->where('order_id', '=', $id)->where('machine_id', '=', $i)->delete();

                    }
                    // dd($request, $id, $request->id, $order);
                    
                    // throw new Exception("トランザクション阻止");

                }
            });
            
            return redirect()->route('order.detail', $id);

        }
        catch(\Exception $e){
            // echo($e->getMessage());
            if ($e->getcode() == 499){

            return redirect()->action('OrderController@delpc', $id)->withErrors($e->getMessage());

            }
            return back()->withErrors($e->getmessage());
        }
        finally{

        }
    }



    public function update(Request $request, int $id){
        $id= $request->id;
        if($request->input('back') == '前の画面に戻る'){
            return redirect()->action('OrderController@detail', $id);
        }        

        $rules = [
                //
                'seminar_day' => 'required',
                'seminar_name' => 'required',
                'venue_zip' => 'exclude_if:seminar_venue_pending,true|required',
                'venue_addr1' => ['exclude_if:seminar_venue_pending,true','required','max:200'],
                'venue_addr2' => 'max:200',
                'venue_addr3' => 'max:200',
                'venue_addr4' => 'max:200',
                'venue_name' => ['exclude_if:seminar_venue_pending,true','required',],
                'venue_tel' => 'exclude_if:seminar_venue_pending,true|required|digits_between:5,11',
                'shipping_arrive_day' => 'exclude_if:seminar_venue_pending,true|required|before:seminar_day|after:order_use_from',
                'shipping_return_day' => 'exclude_if:seminar_venue_pending,true|required|after_or_equal:seminar_day|before:order_use_to',
                'shipping_note' => 'max:200',
            ];

        $massages = [
                'venue_tel.digits_between' => '配送先電話番号は市外局番から入力してください。',
                'shipping_arrive_day.before' => '到着希望日はセミナー開催日より前の日付を入力してください。',
                'shipping_arrive_day.after' => '到着希望日は予約開始日より後の日付を入力してください。',
                'shipping_return_day.after_or_equal' => '返送機材発送予定日はセミナー開催日以降（当日を含む）の日付を入力してください。',
                'shipping_return_day.before' => '返送機材発送予定日は予約終了日より前の日付を入力してください。',
              
            ];

            $attributes = [

                'seminar_day' => 'セミナー開催日',
                'seminar_name' => 'セミナー名',
                'venue_zip' => '郵便番号',
                'venue_name' => '配送先担当者',
                'venue_tel' => '配送先電話番号',
                'venue_addr1' => '「住所」',
                'venue_addr2' => '「施設・ビル名」',
                'venue_addr3' => '「会社・部門名１」',
                'venue_addr4' => '「会社・部門名２」',
                'shipping_arrive_day' => '到着希望日',
                'shipping_return_day' => '返送機材発送予定日',
                'shipping_note' => '備考',

            ];
   
        $validator = Validator::make($request->all(), $rules, $massages, $attributes);
        if($validator->fails()){
            return back()->withErrors($validator)->withInput();
        }

        $order = Order::find($request->order_id);
        $order->seminar_day = $request->seminar_day;
        $order->seminar_name = $request->seminar_name;
        $order->seminar_venue_pending = 0;
        $order->save();

        $venue = Venue::find($request->venue_id);
        $venue->venue_zip = $request->venue_zip;
        $venue->venue_tel = $request->venue_tel;
        $venue->venue_addr1 = $request->venue_addr1;
        $venue->venue_addr2 = $request->venue_addr2;
        $venue->venue_addr3 = $request->venue_addr3;
        $venue->venue_addr4 = $request->venue_addr4;
        $venue->venue_name = $request->venue_name;
        $venue->save();

        $ship = Shipping::find($request->shipping_id);
        $ship->shipping_arrive_day = $request->shipping_arrive_day;
        $ship->shipping_arrive_time = $request->shipping_arrive_time;
        $ship->shipping_return_day = $request->shipping_return_day;
        $ship->shipping_special = $request->shipping_special == true ? 1 : 0;
        $ship->shipping_note = $request->shipping_note;

        $ship->save();



        return redirect()->route('order.detail', ['id' =>$id]);
    }

    public function destroy(int $id){

        $order = Order::where('order_id', $id)->first();
        // $machine = Order::join('machine_detail_order','orders.order_id','=','machine_detail_order.order_id')
        // ->where('machine_detail_order.order_id',$id)
        // ->pluck('machine_id')
        // ->toarray();
        // DayMachine::wherein('machine_id', $machine)->wherebetween('day', [$order->order_use_from,$order->order_use_to])->delete();
        // dd(DayMachine::where('order_id', $id)->pluck('id'),MachineDetailOrder::where('order_id', $id)->pluck('id'));

        DayMachine::where('order_id', $id)->delete();
        MachineDetailOrder::where('order_id', $id)->delete();
        $order->delete();

        return redirect()->route('dashboard');
    }

    public function adminMachineList(){
        $orders = Order::join('shippings', 'orders.order_id', '=', 'shippings.order_id')
            ->join('users', 'orders.user_id', '=', 'users.id')
            ->whereIn('orders.order_status', ['受付済', '仮登録'])
            ->select('orders.*', 'users.name')
            ->orderBy('orders.seminar_day', 'asc')
            ->orderBy('orders.order_no', 'asc')
            ->get();

        return view('order.admin-machines', compact('orders'));
    }

    public function adminMachineEdit(int $id){
        $data = [
            'id' => $id,
            'user' => Auth::user(),
            'users' => User::orderBy('id')->get(['id', 'name']),
            'machines' => Order::join('machine_detail_order', 'orders.order_id', '=', 'machine_detail_order.order_id')
            ->join('machine_details', 'machine_detail_order.machine_id', '=', 'machine_details.machine_id')
            ->where('orders.order_id', $id)
            ->orderBy('machine_detail_order.machine_id', 'asc')
            ->get(),
            'orders' => Order::join('shippings', 'orders.order_id', '=', 'shippings.order_id')
                ->join('venues', 'shippings.venue_id', '=', 'venues.venue_id')
                ->join('users', 'orders.user_id', '=', 'users.id')
                ->where('orders.order_id', $id)
                ->first(),
        ];

        if ($data['orders'] == null) {
            return view('order/error', $data);
        }

        return view('order.admin-machine-edit', $data);
    }

    public function adminMachineManage(int $id){
        $order = Order::where('order_id', $id)->firstOrFail();
        $machineIds = MachineDetailOrder::where('order_id', $id)->pluck('machine_id');
        $days = [];
        $day = Carbon::parse($order->order_use_from);
        $end = Carbon::parse($order->order_use_to);
        while ($day->lte($end)) {
            $days[] = $day->toDateString();
            $day->addDay();
        }

        $occupiedMachineIds = DayMachine::whereIn('day', $days)
            ->pluck('machine_id')
            ->merge(Temporary::whereIn('day', $days)
                ->where('user_id', '<>', Auth::id())
                ->pluck('machine_id'))
            ->unique()
            ->all();

        $availableMachines = MachineDetail::where('machine_is_expired', '!=', 1)
            ->whereNotIn('machine_id', $machineIds)
            ->whereNotIn('machine_id', $occupiedMachineIds)
            ->orderBy('machine_id')
            ->get();

        $selectedMachines = MachineDetail::join('machine_detail_order', 'machine_details.machine_id', '=', 'machine_detail_order.machine_id')
            ->where('machine_detail_order.order_id', $id)
            ->orderBy('machine_details.machine_id')
            ->get(['machine_details.machine_id', 'machine_details.machine_name', 'machine_details.machine_spec']);

        return view('order.admin-machine-manage', compact('order', 'availableMachines', 'selectedMachines'));
    }

    public function updateAdminMachineManage(Request $request, int $id){
        $operation = $request->validate([
            'operation' => ['required', 'in:add,delete'],
        ])['operation'];
        $machineIdsField = $operation === 'add' ? 'add_machine_ids' : 'delete_machine_ids';
        $validated = $request->validate([
            $machineIdsField => ['required', 'array', 'min:1'],
            $machineIdsField.'.*' => ['required', 'integer', 'distinct', 'exists:machine_details,machine_id'],
        ]);
        $selectedIds = $validated[$machineIdsField];

        DB::transaction(function () use ($id, $operation, $selectedIds) {
            $order = Order::where('order_id', $id)->lockForUpdate()->firstOrFail();

            if ($operation === 'delete') {
                $linkedIds = MachineDetailOrder::where('order_id', $id)
                    ->whereIn('machine_id', $selectedIds)
                    ->pluck('machine_id');

                if ($linkedIds->count() !== count($selectedIds)) {
                    throw ValidationException::withMessages([
                        'delete_machine_ids' => '削除対象に、この予約へ登録されていない機材が含まれています。',
                    ]);
                }

                DayMachine::where('order_id', $id)->whereIn('machine_id', $selectedIds)->delete();
                MachineDetailOrder::where('order_id', $id)->whereIn('machine_id', $selectedIds)->delete();
                return;
            }

            $alreadyLinked = MachineDetailOrder::where('order_id', $id)
                ->whereIn('machine_id', $selectedIds)
                ->exists();

            if ($alreadyLinked) {
                throw ValidationException::withMessages([
                    'add_machine_ids' => '選択した機材の一部は、すでにこの予約に登録されています。',
                ]);
            }

            $days = [];
            $day = Carbon::parse($order->order_use_from);
            $end = Carbon::parse($order->order_use_to);
            while ($day->lte($end)) {
                $days[] = $day->toDateString();
                $day->addDay();
            }

            $occupied = DayMachine::whereIn('machine_id', $selectedIds)
                ->whereIn('day', $days)
                ->where('order_id', '<>', $id)
                ->lockForUpdate()
                ->exists();
            $temporarilyOccupied = Temporary::whereIn('machine_id', $selectedIds)
                ->whereIn('day', $days)
                ->where('user_id', '<>', Auth::id())
                ->exists();

            if ($occupied || $temporarilyOccupied) {
                throw ValidationException::withMessages([
                    'add_machine_ids' => '選択した機材の一部は予約期間中に使用されています。',
                ]);
            }

            $now = Carbon::now()->toDateTimeString();
            $links = [];
            $occupancies = [];
            foreach ($selectedIds as $machineId) {
                $links[] = [
                    'machine_id' => $machineId,
                    'order_id' => $id,
                    'order_status' => $order->order_status,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];

                foreach ($days as $date) {
                    $occupancies[] = [
                        'day' => $date,
                        'machine_id' => $machineId,
                        'order_id' => $id,
                        'order_status' => $order->order_status,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];
                }
            }

            DB::table('machine_detail_order')->insert($links);
            foreach (array_chunk($occupancies, 1000) as $chunk) {
                DB::table('day_machine_detail')->insert($chunk);
            }
        });

        $message = $operation === 'add' ? '機材を追加しました。' : '機材を削除しました。';
        return redirect()->route('admin.orders.machines.manage', $id)->with('status', $message);
    }

    public function adminEdit(Request $request, int $id){
        $order = Order::where('order_id', $id)->firstOrFail();
        $machineIds = MachineDetailOrder::where('order_id', $id)->pluck('machine_id');
        $previousOccupiedDay = null;
        $nextOccupiedDay = null;
        $periodHasConflict = false;

        if ($machineIds->isNotEmpty()) {
            $occupiedByOtherOrder = DayMachine::whereIn('machine_id', $machineIds)
                ->where('order_id', '<>', $id);

            if ($order->order_use_from && $order->order_use_to) {
                $periodHasConflict = (clone $occupiedByOtherOrder)
                    ->whereBetween('day', [$order->order_use_from, $order->order_use_to])
                    ->exists();

                $previousOccupiedDay = (clone $occupiedByOtherOrder)
                    ->where('day', '<', $order->order_use_from)
                    ->max('day');
                $nextOccupiedDay = (clone $occupiedByOtherOrder)
                    ->where('day', '>', $order->order_use_to)
                    ->min('day');
            }
        }

        $data = [
            'id' => $id,
            'orders' => $order,
            'machineCount' => $machineIds->count(),
            'availablePeriodStart' => $previousOccupiedDay
                ? Carbon::parse($previousOccupiedDay)->addDay()->toDateString()
                : null,
            'availablePeriodEnd' => $nextOccupiedDay
                ? Carbon::parse($nextOccupiedDay)->subDay()->toDateString()
                : null,
            'periodHasConflict' => $periodHasConflict,
        ];

        return view('order.admin-edit', $data);
    }

    public function updateAdminSeminar(Request $request, int $id){
        if ($request->input('back') == '前の画面に戻る') {
            return redirect()->route('admin.orders.machines.edit', $id);
        }

        $shipping = Shipping::where('order_id', $id)->first();
        $rules = [
            'seminar_name' => ['required', 'string'],
            'seminar_day' => ['required', 'date', 'after_or_equal:'.today()->toDateString()],
            'order_use_from' => ['required', 'date', 'before:seminar_day'],
            'order_use_to' => ['required', 'date', 'after_or_equal:seminar_day'],
        ];

        if ($shipping && $shipping->shipping_arrive_day) {
            $rules['order_use_from'][] = 'before:'.$shipping->shipping_arrive_day;
        }

        if ($shipping && $shipping->shipping_return_day) {
            $rules['order_use_to'][] = 'after_or_equal:'.$shipping->shipping_return_day;
        }

        $validated = $request->validate($rules, [
            'seminar_day.after_or_equal' => 'セミナー開催日は本日以降の日付にしてください。',
            'order_use_from.before' => '予約開始日はセミナー開催日より前の日付にしてください。',
            'order_use_to.after_or_equal' => '予約終了日はセミナー開催日以降の日付にしてください。',
        ], [
            'seminar_name' => 'セミナー名',
            'seminar_day' => 'セミナー開催日',
            'order_use_from' => '予約開始日',
            'order_use_to' => '予約終了日',
        ]);

        DB::transaction(function () use ($id, $validated) {
            $order = Order::where('order_id', $id)->lockForUpdate()->firstOrFail();
            $machineIds = MachineDetailOrder::where('order_id', $id)->pluck('machine_id');

            if ($machineIds->isNotEmpty()) {
                $hasConflict = DayMachine::whereIn('machine_id', $machineIds)
                    ->where('order_id', '<>', $id)
                    ->whereBetween('day', [$validated['order_use_from'], $validated['order_use_to']])
                    ->exists();

                if ($hasConflict) {
                    throw ValidationException::withMessages([
                        'order_use_from' => '選択機材は指定期間内に別の予約で使用されています。日程を変更してください。',
                    ]);
                }
            }

            $order->seminar_name = $validated['seminar_name'];
            $order->seminar_day = $validated['seminar_day'];
            $order->order_use_from = $validated['order_use_from'];
            $order->order_use_to = $validated['order_use_to'];
            $order->save();

            DayMachine::where('order_id', $id)->delete();

            $dayMachines = [];
            $now = Carbon::now()->toDateTimeString();
            foreach ($machineIds as $machineId) {
                $day = Carbon::parse($validated['order_use_from']);
                $end = Carbon::parse($validated['order_use_to']);

                while ($day->lte($end)) {
                    $dayMachines[] = [
                        'day' => $day->toDateString(),
                        'machine_id' => $machineId,
                        'order_id' => $id,
                        'order_status' => $order->order_status,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];
                    $day->addDay();

                    if (count($dayMachines) >= 1000) {
                        DB::table('day_machine_detail')->insert($dayMachines);
                        $dayMachines = [];
                    }
                }
            }

            if ($dayMachines) {
                DB::table('day_machine_detail')->insert($dayMachines);
            }
        });

        return redirect()->route('admin.orders.machines.edit', $id)
            ->with('status', 'セミナー情報と予約期間を更新しました。');
    }

    public function adminShippingEdit(int $id){
        $orders = Order::join('shippings', 'orders.order_id', '=', 'shippings.order_id')
            ->join('venues', 'shippings.venue_id', '=', 'venues.venue_id')
            ->where('orders.order_id', $id)
            ->firstOrFail();

        return view('order.admin-shipping-edit', compact('orders'));
    }

    public function updateAdminShipping(Request $request, int $id){
        if ($request->input('back') == '前の画面に戻る') {
            return redirect()->route('admin.orders.machines.edit', $id);
        }

        $order = Order::where('order_id', $id)->firstOrFail();
        $validated = $request->validate([
            'venue_zip' => ['required', 'string'],
            'venue_addr1' => ['required', 'string', 'max:200'],
            'venue_addr2' => ['nullable', 'string', 'max:200'],
            'venue_addr3' => ['nullable', 'string', 'max:200'],
            'venue_addr4' => ['nullable', 'string', 'max:200'],
            'venue_name' => ['required', 'string'],
            'venue_tel' => ['required', 'digits_between:5,11'],
            'shipping_arrive_day' => [
                'required', 'date',
                'before:'.$order->seminar_day,
                'after:'.$order->order_use_from,
            ],
            'shipping_arrive_time' => ['required', 'string'],
            'shipping_return_day' => [
                'required', 'date',
                'after_or_equal:'.$order->seminar_day,
                'before:'.$order->order_use_to,
            ],
            'shipping_special' => ['nullable', 'boolean'],
            'shipping_note' => ['nullable', 'string', 'max:200'],
        ], [
            'venue_tel.digits_between' => '配送先電話番号は市外局番から入力してください。',
            'shipping_arrive_day.before' => '到着希望日はセミナー開催日より前の日付を入力してください。',
            'shipping_arrive_day.after' => '到着希望日は予約開始日より後の日付を入力してください。',
            'shipping_return_day.after_or_equal' => '返送機材発送予定日はセミナー開催日以降（当日を含む）の日付を入力してください。',
            'shipping_return_day.before' => '返送機材発送予定日は予約終了日より前の日付を入力してください。',
        ], [
            'venue_zip' => '郵便番号',
            'venue_addr1' => '住所',
            'venue_name' => '配送先担当者',
            'venue_tel' => '配送先電話番号',
            'shipping_arrive_day' => '到着希望日',
            'shipping_arrive_time' => '到着希望時間',
            'shipping_return_day' => '返送機材発送予定日',
            'shipping_note' => '備考',
        ]);

        $shippingSpecial = $request->boolean('shipping_special');

        DB::transaction(function () use ($id, $validated, $shippingSpecial) {
            $shipping = Shipping::where('order_id', $id)->lockForUpdate()->firstOrFail();
            $venue = Venue::where('venue_id', $shipping->venue_id)->lockForUpdate()->firstOrFail();

            $venue->venue_zip = $validated['venue_zip'];
            $venue->venue_addr1 = $validated['venue_addr1'];
            $venue->venue_addr2 = $validated['venue_addr2'] ?? null;
            $venue->venue_addr3 = $validated['venue_addr3'] ?? null;
            $venue->venue_addr4 = $validated['venue_addr4'] ?? null;
            $venue->venue_name = $validated['venue_name'];
            $venue->venue_tel = $validated['venue_tel'];
            $venue->save();

            $shipping->shipping_arrive_day = $validated['shipping_arrive_day'];
            $shipping->shipping_arrive_time = $validated['shipping_arrive_time'];
            $shipping->shipping_return_day = $validated['shipping_return_day'];
            $shipping->shipping_special = $shippingSpecial;
            $shipping->shipping_note = $validated['shipping_note'] ?? null;
            $shipping->save();
        });

        return redirect()->route('admin.orders.machines.edit', $id)
            ->with('status', '配送先情報を更新しました。');
    }

    public function updateAdminReservationUser(Request $request, int $id){
        $validated = $request->validate([
            'user_id' => ['required', 'integer', 'exists:users,id'],
        ]);

        Order::where('order_id', $id)->update(['user_id' => $validated['user_id']]);

        return redirect()->route('admin.orders.machines.edit', $id)
            ->with('status', '予約者を変更しました。');
    }

    public function list(){
        // public function list(request $request){
        $data = null;
        // if(empty($request->orderby)){
        //     $orderby = 'order_no';
        // }else{
        // $orderby = $request->orderby;
        // }
        // if(empty($request->sort)){
        //     $sort = 'asc';
        // }else{
        //     $sort = $request->sort;
        // }
        $data = [

            'orders' => Order::join('users', 'orders.user_id', '=', 'users.id')->orderBy('order_no', 'asc')->get(),
            // 'orders' => Order::join('users', 'orders.user_id', '=', 'users.id')->orderBy($orderby, $sort)->get(),
            'accept' => Order::join('users', 'orders.user_id', '=', 'users.id')->where('orders.order_status', '=', '受付済')->orderBy('order_no', 'asc')->get(),
            'sent' => Order::join('users', 'orders.user_id', '=', 'users.id')->where('orders.order_status', '=', '発送済')->orderBy('order_no', 'asc')->get(),
            'end' => Order::join('users', 'orders.user_id', '=', 'users.id')->where('orders.order_status', '=', '返却完了')->orderBy('order_no', 'asc')->get(),

        ];
        return view('order.index', $data);
    }

    public function changetome(int $id){
        $user = Auth::user();
        try{

            DB::transaction(function ()use($user, $id) {

            Order::where('order_id', '=', $id)
            ->update([
                'user_id' => $user->id,
                'order_status' => '受付済'
            ]);

            MachineDetailOrder::where('order_id', '=', $id)
            ->update([
                'order_status' => '受付済'
            ]);
            
            DayMachine::where('order_id', '=', $id)
            ->update([
                'order_status' => '受付済'
            ]);

            //メール送信
            $orderdata = [
                'machines' => MachineDetail::join('machine_detail_order', 'machine_details.machine_id', '=', 'machine_detail_order.machine_id')->join('orders', 'machine_detail_order.order_id','=', 'orders.order_id')->where('orders.order_id', '=', $id)->get(),
                'user' => Auth::user(),
            //     'input' => $request,
                'order' => Order::where('order_id', '=', $id)->first(),
                'venue' => Venue::join('shippings', 'venues.venue_id', '=', 'shippings.venue_id')->where('shippings.order_id', '=', $id)->first(),
                'shipping' => Shipping::where('order_id', '=', $id)->first(),
            ];
            // dump($order,  $venue, $ship);
            // dd($orderdata);
            Mail::to(Auth::user())
               ->send(new OrderMail($orderdata)); 


            // dd($user);
            // throw new Exception("トランザクション阻止");

            });
        }
        catch(\Exception $e){
            return redirect()->action('OrderController@detail', $id)->withErrors($e->getMessage());

        }
        $data = null;
        $data = [

            'orders' => Order::join('users', 'orders.user_id', '=', 'users.id')->orderBy('order_no', 'asc')->get(),
        ];
        
        return redirect()->route('order.list', $data);
    }
    public function changetojohn(int $id){
        $user = Auth::user();
        try{

            DB::transaction(function ()use($user, $id) {

            Order::where('order_id', '=', $id)
            ->update([
                'user_id' => 2,
                'order_status' => '仮登録'
            ]);

            MachineDetailOrder::where('order_id', '=', $id)
            ->update([
                'order_status' => '仮登録'
            ]);

            DayMachine::where('order_id', '=', $id)
            ->update([
                'order_status' => '仮登録'
            ]);

            // dd($user);
            // throw new Exception("トランザクション阻止");

            });
        }
        catch(\Exception $e){
            return redirect()->action('OrderController@detail', $id)->withErrors($e->getMessage());

        }
        $data = null;
        $data = [

            'orders' => Order::join('users', 'orders.user_id', '=', 'users.id')->orderBy('order_no', 'asc')->get(),
        ];
        
        return redirect()->route('order.list', $data);
    }

}
