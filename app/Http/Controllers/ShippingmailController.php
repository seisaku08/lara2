<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Shipping;
use App\Models\User;
use App\Models\Venue;
use App\Models\MachineDetail;

class ShippingmailController extends Controller
{
    public function view(Request $request){

    $data = [
        'shippingdata' => [
            'machines' => MachineDetail::join('machine_detail_order', 'machine_details.machine_id', '=', 'machine_detail_order.machine_id')->where('machine_detail_order.order_id', '=', 1)->orderBy('machine_details.machine_id','asc')->get(),
            'orders' => Order::join('users', 'orders.user_id', '=', 'users.id')->join('shippings','orders.order_id', '=', 'shippings.order_id')->join('venues', 'shippings.venue_id', '=', 'venues.venue_id')->where('orders.order_id', '=', 1)->first(),
            'invoice' => Invoice::where('shipping_id', '=',1)->get(),
    ]
];
// dd($data);
        return view('shippingmail', $data);
    }
    //
}
