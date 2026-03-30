<?php

namespace App\Http\Controllers;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Illuminate\Http\Request;
use App\Libs\Common;
use App\Models\MachineDetail;
use App\Models\DayMachine;
use App\Models\Temporary;
use App\Models\User;
use App\Models\Order;
use App\Models\Maintenance;
use App\Models\Supply;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Yasumi\Yasumi;
use Carbon\Carbon;
use Illuminate\Auth\Events\Validated;
use Illuminate\Support\Facades\Log;

class pctoolController extends Controller
{
    public function view(Request $request){
        // PRG: retry() からの遷移で保存された使用情報を取得して一時的に利用する（取得後はセッションから削除）
        $usageFromSession = $request->session()->pull('Session.Usage', null);

        // selected IDs はセッションの ids またはリクエストの id を利用
        if (!empty($usageFromSession) && !empty($usageFromSession['ids'])) {
            $selectedIds = array_map('strval', (array)$usageFromSession['ids']);
        } else {
            $selectedIds = array_map('strval', (array)$request->input('id', []));
        }

        // セッションの日時情報があり、かつリクエストに値が無ければマージしておく
        if (!empty($usageFromSession)) {
            $merge = [];
            if (empty($request->input('seminar_day')) && !empty($usageFromSession['seminar_day'] ?? null)) $merge['seminar_day'] = $usageFromSession['seminar_day'];
            if (empty($request->input('from')) && !empty($usageFromSession['from'] ?? null)) $merge['from'] = $usageFromSession['from'];
            if (empty($request->input('to')) && !empty($usageFromSession['to'] ?? null)) $merge['to'] = $usageFromSession['to'];
            if (!empty($merge)) $request->merge($merge);
        }

        $data = [
            'records' => MachineDetail::where('machine_is_expired','!=',1)->get(),
            'user' => Auth::user(),
            'input' => $request,
            'selectedIds' => $selectedIds,
        ];

        // 使用日関連の変数を作る
        $day1after = Common::dayafter(today(),1);
        $day4after = Common::dayafter(today(),4);
        $day5after = Common::dayafter(today(),5);
        $daysemi3before = Common::daybefore(Carbon::parse($request->seminar_day),3);
        $daysemi4before = Common::daybefore(Carbon::parse($request->seminar_day),4);
        $daysemi3after = Common::dayafter(Carbon::parse($request->seminar_day),3);

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

        if($validator->fails()){
            return back()->withErrors($validator)->withInput($request->except('to'));
        }

        //使用状況の確認（From:予約開始日からTo:予約終了日の間にday_machineテーブルに存在するmachine_idをピックアップする）
        if($request->from != "" && $request->to != ""){
            $from = new Carbon($request->from);
            $to = new Carbon($request->to);
            while($from <= $to){
                $u[] = $from->format('Y-m-d');
                $from->modify('1 day');
            }
            $dm = array_keys(array_count_values(DayMachine::whereIn('day', $u)->pluck('machine_id')->toarray()));
            $tm = array_keys(array_count_values(Temporary::whereIn('day', $u)->where('user_id', '<>', Auth::id())->pluck('machine_id')->toarray()));
            $data['usage'] = array_merge($dm, $tm);
        }else{
            $data['usage'] = [];
        }
        
        // デバッグログ：request/session/selectedIds/records/usage の状態
        try {
            Log::debug('pctool.view debug', [
                'request' => $request->all(),
                'session_Usage_before_pull' => $usageFromSession,
                'selectedIds' => $selectedIds,
                'records_count' => is_object($data['records']) ? $data['records']->count() : null,
                'usage' => $data['usage'] ?? null,
            ]);
        } catch (\Exception $e) {
            // ログ失敗は無視
        }

        return view('pctool', $data);
    }
    public function retry(Request $request){
        // merge any saved cart/session values so we capture intended selection
        $merge = [];
        if($request->session()->has('Session.SeminarDay')) $merge['seminar_day'] = $request->session()->get('Session.SeminarDay');
        if($request->session()->has('Session.CartData'))   $merge['id'] = $request->session()->get('Session.CartData');
        if($request->session()->has('Session.UseFrom'))    $merge['from'] = $request->session()->get('Session.UseFrom');
        if($request->session()->has('Session.UseTo'))      $merge['to'] = $request->session()->get('Session.UseTo');
        if (!empty($merge)) $request->merge($merge);

        // selected ids from request (may come from session.CartData merged above)
        $selectedIds = (array)$request->input('id', []);

        // persist selection and dates to Session.Usage, then redirect to GET /pctool (PRG)
        $usage = [
            'seminar_day' => $request->input('seminar_day', null),
            'from' => $request->input('from', null),
            'to'   => $request->input('to', null),
            'ids'  => $selectedIds,
        ];

        $request->session()->put('Session.Usage', $usage);

        return redirect()->route('pctool', [
            'seminar_day' => $usage['seminar_day'],
            'from' => $usage['from'],
            'to' => $usage['to'],
        ]);
    }
    //
    public function detail(Request $request){
        $id= $request->id;
        $data = [
            'id'=> $id,
            'machine_details' => MachineDetail::find($id),
            'supplies' => Supply::where('machine_id', '=', $id)->get(),
            'orders' => Order::join('machine_detail_order','orders.order_id','=','machine_detail_order.order_id')
                ->join('machine_details','machine_detail_order.machine_id','=','machine_details.machine_id')
                ->where('machine_details.machine_id',$id)
                ->orderBy('seminar_day', 'asc')
                ->get(),
            'maintenances' => Maintenance::where('machine_id',$id)->get()
        ];
        // dd($data);
        if($data['machine_details'] == null){
            return view('pctool/error', $data);
        }
        return view('pctool/detail', $data);
    }
}
