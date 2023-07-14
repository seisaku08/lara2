<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MachineDetail;
use App\Models\Order;

class ShippingController extends Controller
{
    public function index(Request $request){
        // dd($request);
        $data = [
            'records' => MachineDetail::all(),
            'orders' => Order::orderBy('seminar_day','asc')->get(),
            'input' => $request,
        ];
        return view('shipping.index', $data);

    }


    //
}
