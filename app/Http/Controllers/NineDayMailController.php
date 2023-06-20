<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Shipping;
use App\Models\User;
use App\Models\Venue;
use App\Models\MachineDetail;

class NineDayMailController extends Controller
{
    public function view(Request $request){

    $data = [
        'orderdata' => [
        'machines'=> MachineDetail::wherein('machine_id', [1,2,3])->get(),
        'user' => User::find(1),
        'order' => Order::find(1),
        'venue' => Venue::find(1),
        'shipping' => Shipping::find(1),

    ]
];

        return view('ninedaymail', $data);
    }
    //
}
