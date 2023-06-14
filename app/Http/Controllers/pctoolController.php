<?php

namespace App\Http\Controllers;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Illuminate\Http\Request;
use App\Libs\Common;
use App\Models\MachineDetail;
use App\Models\DayMachine;
use App\Models\User;
use App\Models\Order;
use App\Models\Maintenance;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Yasumi\Yasumi;
use Carbon\Carbon;
use Illuminate\Auth\Events\Validated;

class pctoolController extends Controller
{
    public function view(Request $request){

        $data = [
            'records' => MachineDetail::all(),
            'user' => Auth::user(),
            'input' => $request,
        ];

        //使用日関連の変数を作る
        $day1after = Common::dayafter(today(),1);
        $day4after = Common::dayafter(today(),4);
        $daysemi3after = Common::dayafter(Carbon::parse($request->seminar_day),3);
        // dd($request,$day1after,$daysemi3after);
        $validator = Validator::make($request->all(),
        [
            'seminar_day' => ['required_with_all:from,to', "after_or_equal:{$day4after}"],
            'from' => ['required_with_all:seminar_day,to', "after_or_equal:{$day1after}"],
            'to' => ['required_with_all:seminar_day,from', "after_or_equal:{$daysemi3after}"],
        ],
        [
            'seminar_day.required_with_all' => 'セミナー開催日は入力必須です。',
            'from.required_with_all' => '使用開始日は入力必須です。',
            'to.required_with_all' => '使用終了日は入力必須です。',
            'seminar_day.after_or_equal' => 'セミナー開催日は本日の4営業日後（'.$day4after->format('Y/m/d').'）から入力可能です。',
            'from.after_or_equal' => '使用開始日は翌営業日以降（'.$day1after->format('Y/m/d').'）から入力可能です。',
            'to.after_or_equal' => '使用終了日はセミナー開催日の3営業日後（'.$daysemi3after->format('Y/m/d').'）から入力可能です。',
        ]);

        if($validator->fails()){
            return back()->withErrors($validator)->withInput($request->except('to'));
        }

        //使用状況の確認（From:使用開始日からTo:使用終了日の間にday_machineテーブルに存在するmachine_idをピックアップする）
        if($request->from != "" && $request->to != ""){
            $from = new Carbon($request->from);
            $to = new Carbon($request->to);
            while($from <= $to){
                $u[] = $from->format('Y-m-d');
                $from->modify('1 day');
            }
            $data['usage'] = array_keys(array_count_values(DayMachine::whereIn('day', $u)->pluck('machine_id')->toarray()));
        }else{
            $data['usage'] = [];
        }
        
        // dd($data);
        return view('pctool', $data);
    }
    public function retry(Request $request){
        $data = [
            'records' => MachineDetail::all(),
            'user' => Auth::user(),
            'input' => $request,
            
        ];
        if($request->session()->has('Session.SeminarDay')){
            $merge['seminar_day'] = $request->session()->get('Session.SeminarDay');
        }
        if($request->session()->has('Session.CartData')){
            $merge['id'] = $request->session()->get('Session.CartData');
        }
        if($request->session()->has('Session.UseFrom')){
            $merge['from'] = $request->session()->get('Session.UseFrom');
        }
        if($request->session()->has('Session.UseTo')){
            $merge['to'] = $request->session()->get('Session.UseTo');
        }
        
        if(isset($merge)){
            $request->merge($merge);
        }

        //使用状況の確認（From:使用開始日からTo:使用終了日の間にday_machineテーブルに存在するmachine_idをピックアップする）
        if($request->from != "" && $request->to != ""){
            $from = new Carbon($request->from);
            $to = new Carbon($request->to);
            while($from <= $to){
                $u[] = $from->format('Y-m-d');
                $from->modify('1 day');
            }
            $data['usage'] = array_keys(array_count_values(DayMachine::whereIn('day', $u)->pluck('machine_id')->toarray()));
        }else{
            $data['usage'] = [];
        }
        
        // dd($data);
        return view('pctool', $data,);
    }
    //
    public function detail(Request $request){
        $id= $request->id;
        $data = [
            'machine_details' => MachineDetail::find($id),
            'orders' => Order::join('machine_detail_order','orders.order_id','=','machine_detail_order.order_id')
                ->join('machine_details','machine_detail_order.machine_id','=','machine_details.machine_id')
                ->where('machine_details.machine_id',$id)
                ->get(),
            'maintenances' => Maintenance::where('machine_id',$id)->get()
        ];
        return view('pctool/detail', $data);
    }

}
