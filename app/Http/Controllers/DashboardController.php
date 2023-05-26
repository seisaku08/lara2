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

            'message' => 'まいぺいじ',
            'orders' => Order::where('user_id',Auth::user()->id)->get(),

        ];
        return view('dashboard', $data);
    }
}
