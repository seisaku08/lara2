<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
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

    public function addprocess(Request $request, $id){
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

    public function delprocess(Request $request, $id){
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



    public function update(Request $request, $id){
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

    public function destroy($id){

        $order = Order::where('order_id', $id)->first();
        $machine = Order::join('machine_detail_order','orders.order_id','=','machine_detail_order.order_id')
        ->where('machine_detail_order.order_id',$id)
        ->pluck('machine_id')
        ->toarray();

        DayMachine::wherein('machine_id', $machine)->wherebetween('day', [$order->order_use_from,$order->order_use_to])->delete();
        $order->delete();

        return redirect()->route('dashboard');
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

    public function changetome($id){
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
    public function changetojohn($id){
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
