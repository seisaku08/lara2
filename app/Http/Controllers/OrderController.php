<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Venue;
use App\Models\Shipping;
use App\Models\DayMachine;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    public function detail(Request $request){
        $id= $request->id;
        $data = [
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
        return view('order/detail', $data);
    }

    public function edit(Request $request){
        $id= $request->id;
        $data = [
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
        return view('order/edit', $data);
    }

    public function update(Request $request, $id){
        // $id= $request->id;

        $rules = [
                //
                'seminar_day' => 'required',
                'seminar_name' => 'required',
                'venue_zip' => 'required',
                'venue_addr1' => 'required',
                'venue_name' => 'required',
                'venue_tel' => 'required|digits_between:5,11',
                'shipping_arrive_day' => 'required|before:seminar_day|after:today',
                'shipping_return_day' => 'required|after:shipping_arrive_day',
            ];

        $massages = [
                'seminar_day.required' => 'セミナー開催日は必ず入力してください。',
                'seminar_name.required' => 'セミナー名は必ず入力してください。',
                'venue_zip.required' => '郵便番号は必ず入力してください。',
                'venue_addr1.required' => '住所は必ず入力してください。',
                'venue_name.required' => '配送先担当者は必ず入力してください。',
                'venue_tel.required' => '配達先電話番号は必ず入力してください。',
                'venue_tel.digits_between' => '配達先電話番号は市外局番から入力してください。',
                'shipping_arrive_day.required' => '到着希望日時は必ず入力してください。',
                'shipping_arrive_day.before' => '到着希望日時はセミナー開催日より前の日付を入力してください。',
                'shipping_arrive_day.after' => '到着希望日時は本日より後の日付を入力してください。',
                'shipping_return_day.required' => '返送予定日は必ず入力してください。',
                'shipping_return_day.after' => '返送予定日は到着希望日より後の日付を入力してください。',
               
            ];
   
        $validator = Validator::make($request->all(), $rules, $massages);
        if($validator->fails()){
            return back()->withErrors($validator)->withInput();
        }

        $order = Order::find($request->order_id);
        $order->seminar_day = $request->seminar_day;
        $order->seminar_name = $request->seminar_name;
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
}
