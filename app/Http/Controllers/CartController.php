<?php

namespace App\Http\Controllers;

use Illuminate\View\Middleware\ShareErrorsFromSession;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MachineDetail;
use App\Models\User;
use App\Models\DayMachine;
use App\Models\Temporary;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use DateTime;
use Illuminate\Auth\Events\Validated;

class CartController extends Controller
{

    // public function view(Request $request){
    //     $id = $request->input('id');
    //     $data = [

    //         'cartData' => array_column($request->session()->get('cartData'), 'session_machine_id', 'session_machine_id')
 
    //         // 'records' => MachineDetail::whereIn('machine_id', $id)->get()
    //     ];
    //     // dd($data);
    //     return view('cart', $data);
    // }
    public function index(Request $request){
        $mid = $request->session()->get('Session.CartData');
        $data = [

            'user' => Auth::user()->id,
            'input' => $request,
            'CartData' => MachineDetail::wherein('machine_id', $mid)->get(),
            'from' => $request->session()->get('Session.UseFrom'),
            'to' => $request->session()->get('Session.UseTo'),
            
        ];
        // dd($data);
        return view('cart', $data);
    }
    /*
    |--------------------------------------------------------------------------
    | 商品詳細 → カート画面へのSession情報保存
    |--------------------------------------------------------------------------
    */
    public function addCart(Request $request,)
    {
        //使用機材のIDを取得
        $id = $request->input('id');


        $validator = Validator::make($request->all(),
        [
            'from' => 'required',
            'to' => 'required',
            'id' => 'required',
        ],
        [
            'from' => '使用開始日は必ず入力してください。',
            'to' => '使用終了日は必ず入力してください。',
            'id' => '機材は必ず一つ以上選択してください。',
        ]);

        if($validator->fails()){
            //セッションに機材ID、日程を登録
            $request->session()->put('Session.CartData', $id);
            $request->session()->put('Session.UseFrom', $request->from);
            $request->session()->put('Session.UseTo', $request->to);

            return redirect()->route('pctool.retry')->withErrors($validator);
        }

        //使用状況を確認
        //$uに検索日程を1日ずつ格納
        $from = new DateTime($request->from);
        $to = new DateTime($request->to);
        while($from <= $to){
            $u[] = $from->format('Y-m-d');
            $from->modify('1 day');
        }
        $user = Auth::user()->id;
        //検索日程における既登録分を取得
        $usage = DayMachine::whereIn('day', $u)->pluck('machine_id')->toarray();
        //temporariesテーブルから自分「以外」の仮登録状況を取得する
        $tempUse = Temporary::where('user_id', '<>', $user)->whereIn('day', $u)->pluck('machine_id')->toarray();
        //無限増殖防止のためtemporariesテーブルからユーザー自身の仮登録分を削除する
        Temporary::where('user_id',$user)->delete();  
        $inUse = array_keys(array_count_values(array_merge($usage,$tempUse)));

        if(in_array($id, $inUse)){
            // return back()->withInput();
        }
        else{
            //temporariesテーブルに選択した機材ID、日程を仮登録
            foreach($id as $i){
                $from = new DateTime($request->from);
                while($from <= $to){
                    $temp = new Temporary;
                        $temp->user_id = $user;
                        $temp->machine_id = $i;
                        $temp->day = date($from->format('Y-m-d'));
                        $temp->save();
                    $from->modify('1 day');
                }
            }
            //セッションに機材ID、日程を登録
            $request->session()->put('Session.CartData', $id);
            $request->session()->put('Session.UseFrom', $request->from);
            $request->session()->put('Session.UseTo', $request->to);

        }
        // dd($id,$u,$usage,$tempUse,array_merge($usage,$tempUse),$inUse,in_array($id, $inUse),$request->session());
        
        return redirect()->route('cart.index');
        

        //商品詳細画面のhidden属性で送信（Request）された商品IDと注文個数を取得し配列として変数に格納
        //inputタグのname属性を指定し$requestからPOST送信された内容を取得する。
        // foreach($request->id as $sid){
        //     $cartData = 
        // [
        //     'session_machine_id' => $sid, 
            // 'session_quantity' => $request->product_quantity, 
        // ];

        // //sessionにcartData配列が「無い」場合、商品情報の配列をcartData(key)という名で$cartDataをSessionに追加
        // if (!$request->session()->has('cartData')) {
        //     $request->session()->put('cartData', $cartData);
        // } else {
        //     //sessionにcartData配列が「有る」場合、情報取得
        //     $sessionCartData = $request->session()->get('cartData');

        //     //isSameMachineId定義 product_id同一確認フラグ false = 同一ではない状態を指定
        //     $isSameMachineId = false;
        //     // foreach ($sessionCartData as $index => $sessionData) {
        //     //     //product_idが同一であれば、フラグをtrueにする → 個数の合算処理、及びセッション情報更新。更新は一度のみ
        //     //     if ($sessionData['session_machine_id'] === $cartData['session_machine_id'] ) {
        //     //         $isSameMachineId = true;
        //     //         // $quantity = $sessionData['session_quantity'] + $cartData['session_quantity'];
        //     //         //cartDataをrootとしたツリー状の多次元連想配列の特定のValueにアクセスし、指定の変数でValueの上書き処理
        //     //         // $request->session()->put('cartData.' . $index . '.session_quantity', $quantity);
        //     //         break;
        //     //     }
        //     // }

        //     //product_idが同一ではない状態を指定 その場合であればpushする
        //     if ($isSameMachineId === false) {
        //         $request->session()->push('cartData', $cartData);
        //     }
        // }
        // }
        // //POST送信された情報をsessionに保存 'users_id'(key)に$request内の'users_id'をセット
        // $request->session()->put('users_id', ($request->users_id));
        // return redirect()->route('cart.index');
    }

    public function delCart(Request $request,)
    {
        dd($request);
    }


    /*
    |--------------------------------------------------------------------------
    | カート内商品の削除
    |--------------------------------------------------------------------------
    */

    public function remove(Request $request){
        //session情報の取得（product_idと個数の2次元配列）
        $sessionCartData = $request->session()->get('cartData');

        //削除ボタンから受け取ったproduct_idと個数を2次元配列に
        $removeCartItem = [
            ['session_machine_id' => $request->product_id, 
            'session_quantity' => $request->product_quantity]
        ];

        //sessionデータと削除対象データを比較、重複部分を削除し残りの配列を抽出
        $removeCompletedCartData = array_udiff($sessionCartData, $removeCartItem, function ($sessionCartData, $removeCartItem) {
            $result1 = $sessionCartData['session_machine_id'] - $removeCartItem['session_machine_id'];
            $result2 = $sessionCartData['session_quantity'] - $removeCartItem['session_quantity'];
            return $result1 + $result2;
        });

        //上記の抽出情報でcartDataを上書き処理
        $request->session()->put('cartData', $removeCompletedCartData);
        //上書き後のsession再取得
        $cartData = $request->session()->get('cartData');

        //session情報があればtrue
        if ($request->session()->has('cartData')) {
            return redirect()->route('cartlist.index');
        }

        return view('products.no_cart_list', ['user' => Auth::user()]);

    }

}
