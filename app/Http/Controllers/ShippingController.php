<?php

namespace App\Http\Controllers;

use App\Models\DayMachine;
use Illuminate\Http\Request;
use App\Models\MachineDetail;
use App\Models\Order;
use App\Models\Invoice;
use App\Mail\ShippingMail;      //Mailableクラス
use Illuminate\Support\Facades\Validator;
use App\Models\MachineDetailOrder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Exception;


class ShippingController extends Controller
{
    public function index(Request $request){
        // dd($request);
        $data = [
            'records' => MachineDetail::all(),
            'orders' => Order::join('shippings','orders.order_id', '=', 'shippings.order_id')->wherein('orders.order_status', ['受付済','仮登録'])->orderBy('seminar_day','asc')->get(),
            'input' => $request,
            'back_order' => Order::join('shippings','orders.order_id', '=', 'shippings.order_id')->wherein('orders.order_status', ['発送済'])->orderBy('seminar_day','asc')->get(),
        ];
        return view('shipping.index', $data);

    }

    public function order(Request $request){

        $data = [
            'machines' => MachineDetail::join('machine_detail_order', 'machine_details.machine_id', '=', 'machine_detail_order.machine_id')->where('machine_detail_order.order_id', '=', $request->id)->orderBy('machine_details.machine_id','asc')->get(),
            'orders' => Order::join('users', 'orders.user_id', '=', 'users.id')->join('shippings','orders.order_id', '=', 'shippings.order_id')->join('venues', 'shippings.venue_id', '=', 'venues.venue_id')->where('orders.order_id', '=', $request->id)->first(),
            'input' => $request,
        ];
        return view('shipping.work', $data);

    }

    public function invoice(Request $request){
        $data = [
            'machines' => MachineDetail::join('machine_detail_order', 'machine_details.machine_id', '=', 'machine_detail_order.machine_id')->where('machine_detail_order.order_id', '=', $request->id)->orderBy('machine_details.machine_id','asc')->get(),
            'orders' => Order::join('users', 'orders.user_id', '=', 'users.id')->join('shippings','orders.order_id', '=', 'shippings.order_id')->join('venues', 'shippings.venue_id', '=', 'venues.venue_id')->where('orders.order_id', '=', $request->id)->first(),
            'input' => $request,
        ];
        // dd($data,$request);
        try{
        $validator = Validator::make($request->all(),
        [
            'id' => ['nullable']
        ],
        [
            'id.nullable' => '伝票番号は',
            'id.regex' => '伝票番号は「4桁-4桁-4桁」もしくは「12桁」の形式で入力してください。'
        ]);

        if($validator->fails()){
            // dd($validator);
            return view('shipping.work', $data)->withErrors($validator);
            // throw new Exception('null',499);
        }
            $order_no = DB::transaction(function ()use($request) {

            foreach($request->no as $i){
                if ($i == null){continue;}
                $invoice = new Invoice;
                $invoice->shipping_id = $request->shipping_id;
                $invoice->invoice_no = $i;

                $invoice->save();
                }

            Order::where('order_id', $request->id)->update(['order_status' => '発送済']);
            MachineDetailOrder::where('order_id', $request->id)->update(['order_status' => '発送済']);
            DayMachine::where('order_id', $request->id)->update(['order_status' => '発送済']);
            MachineDetail::join('machine_detail_order', 'machine_details.machine_id', '=', 'machine_detail_order.machine_id')->where('machine_detail_order.order_id', '=', $request->id)->update(['machine_details.machine_status' => '貸出中']);

            });
            
            $shippingdata = [
                'machines' => MachineDetail::join('machine_detail_order', 'machine_details.machine_id', '=', 'machine_detail_order.machine_id')->where('machine_detail_order.order_id', '=', $request->id)->orderBy('machine_details.machine_id','asc')->get(),
                'orders' => Order::join('users', 'orders.user_id', '=', 'users.id')->join('shippings','orders.order_id', '=', 'shippings.order_id')->join('venues', 'shippings.venue_id', '=', 'venues.venue_id')->where('orders.order_id', '=', $request->id)->first(),
                'invoice' => Invoice::where('shipping_id', '=',$request->shipping_id)->get(),
            ];
            Mail::to($data['orders']->email)
            ->send(new ShippingMail($shippingdata));


        }
        catch(\Exception $e){
            // echo($e->getMessage());
            if ($e->getcode() == 499){
            // dd($data);

                return view('shipping.work', $data)->withErrors($e->getMessage());

            }
            return view('shipping.work', $data)->withErrors($e->getmessage());
        }
        finally{

        }


        return redirect()->route('shipping');


    //
    }
}