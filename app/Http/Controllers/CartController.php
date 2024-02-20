<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MachineDetail;
use App\Models\DayMachine;
use App\Models\Temporary;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Exception;
use Carbon\Carbon;
use App\Libs\Common;



class CartController extends Controller
{

    public function index(Request $request){
        if(session()->has('Session.CartData') == false){
            $data = [
                'user' => Auth::user()->id,
                'input' => $request,
                'seminar_day' =>$request->session()->get('Session.SeminarDay'),
                'from' => $request->session()->get('Session.UseFrom'),
                'to' => $request->session()->get('Session.UseTo'),
                
            ];
            return view('cart.empty', $data);
        }

        $mid = $request->session()->get('Session.CartData');
        $data = [
            'user' => Auth::user()->id,
            'input' => $request,
            'CartData' => MachineDetail::wherein('machine_id', $mid)->get(),
            'seminar_day' =>$request->session()->get('Session.SeminarDay'),
            'from' => $request->session()->get('Session.UseFrom'),
            'to' => $request->session()->get('Session.UseTo'),
            
        ];
        return view('cart', $data);
    }

    public function addCart(Request $request,)
    {
        //使用機材のIDを取得
        $id = $request->input('id');

        $day1after = Common::dayafter(today(),1);
        $day4after = Common::dayafter(today(),4);
        $daysemi3before = Common::daybefore(Carbon::parse($request->seminar_day),3);
        $daysemi3after = Common::dayafter(Carbon::parse($request->seminar_day),3);

        $validator = Validator::make($request->all(),
        [
            'seminar_day' => ['required_with_all:from,to', "after_or_equal:{$day4after}"],
            'from' => ['required_with_all:seminar_day,to', "after_or_equal:{$day1after}", "before_or_equal:{$daysemi3before}"],
            'to' => ['required_with_all:seminar_day,from', "after_or_equal:{$daysemi3after}"],
            'id' => 'required',
        ],
        [
            'seminar_day.required_with_all' => 'セミナー開催日は入力必須です。',
            'from.required_with_all' => '予約開始日は入力必須です。',
            'to.required_with_all' => '予約終了日は入力必須（セミナー開催日の3営業日前（'.$daysemi3before->format('Y/m/d').'）まで入力可能）です。',
            'seminar_day.after_or_equal' => 'セミナー開催日は本日の4営業日後（'.$day4after->format('Y/m/d').'）から入力可能です。',
            'from.after_or_equal' => '予約開始日は翌営業日以降（'.$day1after->format('Y/m/d').'）から入力可能です。',
            'from.before_or_equal' => '予約開始日はセミナー開催日の3営業日前（'.$daysemi3before->format('Y/m/d').'）まで入力可能です。',
            'to.after_or_equal' => '予約終了日はセミナー開催日の3営業日後（'.$daysemi3after->format('Y/m/d').'）から入力可能です。',
            'id' => '機材は必ず一つ以上選択してください。',
        ]);

        if($validator->fails()){
            //セッションに機材ID、日程を登録
            $request->session()->put('Session.CartData', $id);
            $request->session()->put('Session.SeminarDay', $request->seminar_day);
            $request->session()->put('Session.UseFrom', $request->from);
            $request->session()->put('Session.UseTo', $request->to);

            return redirect()->route('pctool.retry')->withErrors($validator);
        }

        //使用状況を確認
        //$uに検索日程を1日ずつ格納
        $from = new Carbon($request->from);
        $to = new Carbon($request->to);
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
                $from = new Carbon($request->from);
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
            $request->session()->put('Session.SeminarDay', $request->seminar_day);
            $request->session()->put('Session.UseFrom', $request->from);
            $request->session()->put('Session.UseTo', $request->to);

        }
        // dd($id,$u,$usage,$tempUse,array_merge($usage,$tempUse),$inUse,in_array($id, $inUse),$request->session());
        
        return redirect()->route('cart.index');
    }

    public function delCart(Request $request)
    {
        //セッションからカートデータを取り出し、消去
        $sessionCartData = $request->session()->get('Session.CartData');
        $request->session()->forget('Session.Cartdata');

        //削除対象を取り出したカートデータから削除する
        $removed = array_diff($sessionCartData, [$request->machine_id]);

        //temporariesテーブルから削除対象の仮登録分を削除する
        Temporary::where('machine_id',$request->machine_id)->delete(); 

        //削除済みの新カートデータをセッションに保存
        if(!empty($removed)){
            $request->session()->put('Session.CartData', $removed);
        }else{
            session()->forget('Session.CartData');
            return view('cart_empty');
        }
        
        // dd($request, $sessionCartData, $removed, $request->session()->get('Session.CartData'));
        return redirect()->route('cart.index');
    }

}
