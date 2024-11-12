<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index(){
        $data = null;
        $data = [

            'user' => Auth::user(),
            'message' => 'まいぺいじ',
            'orders' => Order::where('user_id',Auth::user()->id)->get(),
            // 'orders' => Order::join('users', 'orders.user_id', '=', 'users.id')->orderBy('order_no', 'asc')->get(),
            // 'orders' => Order::join('users', 'orders.user_id', '=', 'users.id')->orderBy($orderby, $sort)->get(),
            'accept' => Order::where('user_id',Auth::user()->id)->where('orders.order_status', '=', '受付済')->orderBy('order_no', 'asc')->get(),
            'sent' => Order::where('user_id',Auth::user()->id)->where('orders.order_status', '=', '発送済')->orderBy('order_no', 'asc')->get(),
            'end' => Order::where('user_id',Auth::user()->id)->where('orders.order_status', '=', '返却完了')->orderBy('order_no', 'asc')->get(),

        ];
        return view('dashboard', $data);
    }
}
