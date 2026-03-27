<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\User;
use Carbon\Carbon;

class AlertMailController extends Controller
{
    public function view(Request $request){
    
    //標準出力&ログに出力するメッセージのフォーマット

    $data['alertdata'] =[];
    $dupe = [];

    $duplicateTokens = Order::select('token')
        ->whereNotNull('token')
        ->groupBy('token')
        ->havingRaw('COUNT(*) > 1')
        ->pluck('token');

    $duplicates = Order::whereIn('token', $duplicateTokens)
        ->orderBy('token')
        ->get(['order_id','order_no','seminar_name','token','user_id'])
        ->groupBy('token')
        ->map(function($orders, $token){
            return [
                'token' => $token,
                'orders' => $orders->map(function($o){
                    return [
                        'order_id' => $o->order_id,
                        'order_no' => $o->order_no,
                        'seminar_name' => $o->seminar_name,
                    ];
                })->values()->toArray(),
            ];
        })->values()->toArray();


    $orders = Order::whereNull('seminar_day')
        ->orWhereNull('order_use_from')
        ->orWhereNull('order_use_to')
        ->get(['order_id','order_no','seminar_name','seminar_day','order_use_from','order_use_to','user_id']);

    // user_id の一括取得（null を除去）
    $userIds = $orders->pluck('user_id')->filter()->unique()->toArray();

    // id => name の連想配列を取得（単一クエリ）
    $users = User::whereIn('id', $userIds)->pluck('name','id')->toArray();

    $incomplete = $orders->map(function($o) use ($users) {
    $seminarDay = null;
    $from = null;
    $to = null;
    if (!empty($o->seminar_day)) {
        try { $seminarDay = Carbon::parse($o->seminar_day)->format('Y-m-d'); } catch(\Exception $e) { $seminarDay = null; }
    }
    if (!empty($o->order_use_from)) {
        try { $from = Carbon::parse($o->order_use_from)->format('Y-m-d'); } catch(\Exception $e) { $from = $o->order_use_from; }
    }
    if (!empty($o->order_use_to)) {
        try { $to = Carbon::parse($o->order_use_to)->format('Y-m-d'); } catch(\Exception $e) { $to = $o->order_use_to; }
    }
    return [
        'order_id' => $o->order_id,
        'order_no' => $o->order_no,
        'seminar_name' => $o->seminar_name,
        'user_name' => $users[$o->user_id] ?? '不明',
        'seminar_day' => $seminarDay,
        'order_use_from' => $from,
        'order_use_to' => $to,
    ];
})->toArray();


    $alertData = [];

    if (!empty($duplicates)) $alertData['duplicates'] = $duplicates;
    if (!empty($incomplete)) $alertData['incomplete_orders'] = $incomplete;
    $data['alertdata'] = $alertData;
// dd($data);
        return view('alertmail', $data);
    }
    //
}
