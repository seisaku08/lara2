<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;

class OrderlistController extends Controller
{
    public function index(){
        $data = null;
        $data = [

            'orders' => Order::join('users', 'orders.user_id', '=', 'users.id')->orderBy('seminar_day', 'asc')->get(),

        ];
        return view('orderlist', $data);
    }

    //
}
