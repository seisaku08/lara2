<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;


class AjaxController extends Controller
{
    //
    public function view(Request $request){
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
    
                'orders' => Order::join('users', 'orders.user_id', '=', 'users.id')->orderBy('order_no', 'desc')->get(),
                // 'orders' => Order::join('users', 'orders.user_id', '=', 'users.id')->orderBy($orderby, $sort)->get(),    
            ];
            return view('ajax', $data);
    
    }

public function showall(Request $request)
{
        $sort = 'order_no';
        $sortby = 'desc';
    if(isset($request->sort)){
        $sort = $request->sort;
    }
    if(isset($request->sortby)){
        $sortby = $request->sortby;
    }

    $orders = Order::join('users', 'orders.user_id', '=', 'users.id')->orderBy($sort, $sortby)->get();

    $orderlist = array();

    foreach($orders as $order){
        $orderlist[] = array(
          'user_id'    => $order->user_id,
          'order_use_from'  => $order->order_use_from,
          'order_use_to'  => $order->order_use_to,
          'order_id' => $order->order_id,
          'order_no' => $order->order_no,
          'seminar_name' => $order->seminar_name,
          'name' => $order->name,
          'order_status' => $order->order_status,
          'temporary_name' => $order->temporary_name,
        );
    }

    // ヘッダーを指定することによりjsonの動作を安定させる
    header('Content-type: application/json');

    // htmlへ渡す配列$productListをjsonに変換する
    echo json_encode($orderlist);
}

}
