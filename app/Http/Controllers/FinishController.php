<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Facades\Sequence;
use App\Models\DayMachine;
use App\Models\MachineDetail;
use App\Models\MachineDetailOrder;
use App\Models\Order;
use App\Models\Temporary;
use App\Models\Venue;
use App\Models\Shipping;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use DateTime;
use Exception;

class FinishController extends Controller
{
    public function finish(Request $request)
    {
        $data = [
            'machines' => MachineDetail::wherein('machine_id', $request->id)->get(),
            'user' => Auth::user(),
            'input' => $request,

        ];
        if($request->input('back') == 'back'){
            return redirect('/sendto')->withInput();
        }
        try{
            $order_no = DB::transaction(function ()use($request) {

            //識別トークンが存在しない（以前適切にセッションが通っている＝ブラウザバック等で重複データが送られてきている）
            if(!$request->session()->has('Session.Token')){
                throw new Exception("エラー：セッショントークンが読み込めません。ブラウザを開いたまま長時間経過したか、セミナー名「".$request->seminar_name.'」が登録済みである可能性があります。');
            }
            //識別トークンが既に登録済み
            elseif(Order::where('token', $request->session()->get('Session.Token'))->exists() == true){
                $err = Order::where('token', $request->session()->get('Session.Token'))->first();
                throw new Exception('エラー：オーダーNo.'.$err->order_no.'「'.$err->seminar_name.'」は登録済みです。');
            }

            //セミナー開催日から連番を取得
            $sday = new DateTime($request->seminar_day);
            $sno = Sequence::getNewOrderNo($sday->format('ymd'));
            $order_no = sprintf('%s%04d', $sday->format('ymd'), $sno);

            //ordersテーブル
            $order = new Order;
                //order_noは日付（西暦下2桁＋月日の6桁）＋連番
                $order->order_no = $order_no;
                $order->user_id = Auth::user()->id;
                $order->seminar_day = $request->seminar_day;
                $order->seminar_name = $request->seminar_name;
                $order->order_use_from = $request->order_use_from;
                $order->order_use_to = $request->order_use_to;
                $order->token = $request->session()->get('Session.Token');
                $order->save();

            //order_idをmachine_detail_orderテーブルのために取得
            $last_order_id = DB::getPdo()->lastInsertId();

            //machine_idを取得
            $id = $request->id;

            foreach($id as $i){

                //day_machineテーブルに機材占有状況を展開
                $start = new DateTime($request->order_use_from);
                $end = new DateTime($request->order_use_to);
                while($start <= $end){
                    $day_machine = new DayMachine;
                        $day_machine->day = date($start->format('Y-m-d'));
                        $day_machine->machine_id = $i;
                        $day_machine->save();
                    $start->modify('1 day');
                    }
                //machine_detailテーブルの使用状況
                $param = [
                    'id' => $i
                    ];

                DB::update('UPDATE machine_details SET `machine_status` = "予約中" where `machine_id` = :id', $param);

                //machine_detail_orderテーブルにオーダーと機材IDの対応を1組ずつ展開
                $mdo = new MachineDetailOrder;
                    $mdo->machine_id = $i;
                    $mdo->order_id = $last_order_id;
                    $mdo->save();

                }

            //venuesテーブルに配送先情報を追加
            $venue = new Venue;
                $venue->venue_place = "kari";
                $venue->venue_zip = $request->venue_zip;
                $venue->venue_tel = $request->venue_tel;
                $venue->venue_addr1 = $request->venue_addr1;
                $venue->venue_addr2 = $request->venue_addr2;
                $venue->venue_addr3 = $request->venue_addr3;
                $venue->venue_addr4 = $request->venue_addr4;
                $venue->venue_name = $request->venue_name;
                $venue->save();
                
            //venue_idをshippingsテーブルのために取得
            $last_venue_id = DB::getPdo()->lastInsertId();

            //shippingsテーブルに発送期日情報を追加
            $ship = new Shipping;
                $ship->order_id = $last_order_id;
                $ship->venue_id = $last_venue_id;
                $ship->shipping_arrive_day = $request->shipping_arrive_day;
                $ship->shipping_arrive_time = $request->shipping_arrive_time;
                $ship->shipping_return_day = $request->shipping_return_day;
                $ship->save();
                
            return $order_no;

            },5);

        //テンポラリデータをもろもろ削除、オーダーNoを抽出
        Temporary::where('user_id', Auth::user())->delete();  
        $request->session()->forget('Session');
        $data['order_no']=$order_no;

        return view('finish', $data);

        }
        catch(\Exception $e){
            // echo($e->getMessage());
            return view('confirm', $data)->withErrors($e->getmessage());
        }
        finally{

        }

    }
    //
}
