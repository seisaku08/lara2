<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
//あとでログを出力する処理もいれるので
use Illuminate\Support\Facades\Log;
//操作するテーブルを読み込む
use App\Mail\NineDaysBeforeMail;      //Mailableクラス
use Illuminate\Support\Facades\Mail;
use App\Models\User;
use App\Models\Order;
use App\Models\Venue;
use App\Models\Shipping;
use App\Models\MachineDetail;
use Carbon\Carbon;

class Reminder extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'NineDayRemindMail';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
     //標準出力&ログに出力するメッセージのフォーマット

    //9営業日前に達したorderの抽出
    // $to_send_order_no = Order::where('nine_day_before', '<=', Carbon::today())->get();
    $to_send_order_no = (object)null;

    //各予約の予約情報をDBより収集）
    foreach($to_send_order_no as $part_of_order){
        //「送信済み」かつ「住所未記入“ではない”」予約はスキップする
        if($part_of_order->reminder_sent == 1 && $part_of_order->seminar_venue_pending == 0){
            continue;
        }else{
            //order_idに紐づく機材IDを抽出（あとでwhereInするので配列に）
            $machine = Order::join('machine_detail_order','orders.order_id','=','machine_detail_order.order_id')
            ->where('machine_detail_order.order_id',$part_of_order->order_id)
            ->pluck('machine_id')
            ->toarray();
            //担当者情報を取得
            $user = User::find(Order::where('order_id', $part_of_order->order_id)->get('user_id'))->first()->toarray();

            $orderdata = [
                //機材情報を取得
                'machines' => MachineDetail::wherein('machine_id', $machine)->get(),
                //担当者情報を取得
                'user' => $user,
                //予約情報を取得
                'order' => Order::where('order_id', $part_of_order->order_id)->first(),
                //配送スケジュールを取得
                'shipping' => Shipping::where('order_id',$part_of_order->order_id)->first(),
                //配送先を取得
                'venue' => Venue::where('venue_id', Shipping::where('order_id',$part_of_order->order_id)->pluck('venue_id'))->first(),
            ];
            // dd($user);
            //メールを担当者に送付
            Mail::to(User::find(Order::where('order_id', $part_of_order->order_id)->get('user_id'))->first())
            ->send(new NineDaysBeforeMail($orderdata)); 

            //送信済みフラグを立てる
            $order = Order::find($part_of_order->order_id);
                $order->reminder_sent = 1;
                $order->save();
        }
    }
     $message = '[' . date('Y-m-d h:i:s') . ']UserCounsst:' . User::count();
echo 'reee';
     //INFOレベルでメッセージを出力
     $this->info( $message );
     //ログを書き出す処理はこちら
    //  Log::setDefaultDriver('batch');
     Log::info( $message );        //
    }
}
