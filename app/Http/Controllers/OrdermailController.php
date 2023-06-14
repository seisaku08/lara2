<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\MachineDetail;

class OrdermailController extends Controller
{
    public function view(Request $request){

    $data = [
        'orderdata' => [
        'order_no' => '2301010001',
        'user' => User::find(1)->toarray(),
        'input' => [
            'seminar_day' => '2023-06-01',
            'seminar_name' => 'ダミー',
            'seminar_venue_pending' => 0,
            'venue_zip' => 1000001,
            'venue_addr1' => '東京都千代田区千代田',
            'venue_addr2' => '',
            'venue_addr3' => '',
            'venue_addr4' => '',
            'venue_name' => 'ダミー太郎',
            'venue_tel' => 0300000000,
            'shipping_arrive_day' => '2023-06-01',
            'shipping_arrive_time' => '2023-06-01',
            'shipping_return_day' => '2023-06-01',
            'order_use_from' => '2023-06-01',
            'order_use_to' => '2023-06-01'
        ],
        'machines'=> MachineDetail::wherein('machine_id', [1,2,3])->get(),
    ]
];

        return view('ordermail', $data);
    }
    //
}
