<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MachineDetail;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use App\Libs\Common;
use Illuminate\Support\Facades\Validator;
use App\Http\Requests\AjaxDaysRequest;

class AjaxpctoolController extends Controller
{
    //

    public function view(Request $request){
        $data = [
            'records' => MachineDetail::all(),
            'user' => Auth::user(),
            'input' => $request,
        ];

        return view('ajaxpctool', $data);

    }

    public function show_all(){
    $records = MachineDetail::orderBy('machine_id', 'desc')->get();

    $productList = array();

    foreach($records as $record){
        $productList[] = array(
          'machine_id' => $record->machine_id,
          'machine_name' => $record->machine_name,
          'machine_spec' => $record->machine_spec,
          'machine_since' => Carbon::parse($record->machine_since)->format('Y-m'),
          'machine_os' => $record->machine_os,
          'machine_cpu' => $record->machine_cpu,
          'machine_memory' => $record->machine_memory,
          'machine_monitor' => $record->machine_monitor,
          'machine_powerpoint' => $record->machine_powerpoint,
          'machine_camera' => $record->machine_camera == true ? '有' : '無',
          'machine_hasdrive' => $record->machine_hasdrive == true ? '有' : '無',
          'machine_connector' => $record->machine_connector,
          'machine_canto11' => $record->machine_canto11,
          'machine_memo' => $record->machine_memo,
        );
    }

    // ヘッダーを指定することによりjsonの動作を安定させる
    header('Content-type: application/json');

    // htmlへ渡す配列$productListをjsonに変換する
    echo json_encode($productList);
}

    public function checkday(Request $request){

        // $validate = $request->validated;
        // $day = $request->input('seminar_day');
        $data = [
            'records' => MachineDetail::all(),
            'user' => Auth::user(),
            'input' => $request,
        ];
        // dd($request);
        //使用日関連の変数を作る
        $day1after = Common::dayafter(today(),1);
        $day4after = Common::dayafter(today(),4);
        $day5after = Common::dayafter(today(),5);
        $daysemi3before = Common::daybefore(Carbon::parse($request->seminar_day),3);
        $daysemi4before = Common::daybefore(Carbon::parse($request->seminar_day),4);
        $daysemi3after = Common::dayafter(Carbon::parse($request->seminar_day),3);
        // dd($request,$day1after,$daysemi3after);
        $validator = Validator::make($request->all(),
        [
            'seminar_day' => ['date','required_with_all:from,to', "after_or_equal:{$day5after}"],
            'from' => ['required_with_all:seminar_day,to', "after_or_equal:{$day1after}", "before_or_equal:{$daysemi4before}"],
            'to' => ['required_with_all:seminar_day,from', "after_or_equal:{$daysemi3after}"],
        ],
        [
            'seminar_day.required_with_all' => 'セミナー開催日は入力必須です。',
            'from.required_with_all' => '予約開始日は入力必須です。',
            'to.required_with_all' => '予約終了日は入力必須（セミナー開催日の3営業日後（'.$daysemi3after->format('Y/m/d').'）から入力可能）です。',
            'seminar_day.after_or_equal' => 'セミナー開催日は本日の5営業日後（'.$day5after->format('Y/m/d').'）から入力可能です。',
            'from.after_or_equal' => '予約開始日は翌営業日以降（'.$day1after->format('Y/m/d').'）から入力可能です。',
            'from.before_or_equal' => '予約開始日はセミナー開催日の4営業日前（'.$daysemi4before->format('Y/m/d').'）まで入力可能です。',
            'to.after_or_equal' => '予約終了日はセミナー開催日の3営業日後（'.$daysemi3after->format('Y/m/d').'）から入力可能です。',
        ]);
                if($request->from >= $request->to){
        }
        if($validator->fails()){
            return json_encode($validator);
            // return back()->withErrors($validator)->withInput($request->except('to'));
            // return back()->withErrors($validator)->withInput($request->except(null));
        }

    header('Content-type: application/json');
    echo json_encode($validator);
    // echo json_encode($validator);

}
}
