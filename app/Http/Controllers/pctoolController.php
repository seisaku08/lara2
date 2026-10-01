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
use Illuminate\Support\Facades\DB;
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
            'searchRoute' => $request->routeIs('admin.orders.create') ? 'admin.orders.create' : 'pctool',
        ];

        // 使用日関連の変数を作る
        $day1after = Common::dayafter(today(),1);
        $day4after = Common::dayafter(today(),4);
        $day5after = Common::dayafter(today(),5);
        $daysemi3before = Common::daybefore(Carbon::parse($request->seminar_day),3);
        $daysemi4before = Common::daybefore(Carbon::parse($request->seminar_day),4);
        $daysemi3after = Common::dayafter(Carbon::parse($request->seminar_day),3);
        $isAdminCreate = $request->routeIs('admin.orders.create');

        $rules = [
            'seminar_day' => ['date','required_with_all:from,to', "after_or_equal:{$day5after}"],
            'from' => ['nullable', 'date', 'required_with_all:seminar_day,to'],
            'to' => ['nullable', 'date', 'required_with_all:seminar_day,from'],
        ];
        if (!$isAdminCreate) {
            $rules['from'][] = "after_or_equal:{$day1after}";
            $rules['from'][] = "before_or_equal:{$daysemi4before}";
            $rules['to'][] = "after_or_equal:{$daysemi3after}";
        }

        $messages = [
            'seminar_day.required_with_all' => 'セミナー開催日は入力必須です。',
            'from.required_with_all' => '予約開始日は入力必須です。',
            'to.required_with_all' => '予約終了日は入力必須（セミナー開催日の3営業日後（'.$daysemi3after->format('Y/m/d').'）から入力可能）です。',
            'seminar_day.after_or_equal' => 'セミナー開催日は本日の5営業日後（'.$day5after->format('Y/m/d').'）から入力可能です。',
        ];
        if (!$isAdminCreate) {
            $messages['from.after_or_equal'] = '予約開始日は翌営業日以降（'.$day1after->format('Y/m/d').'）から入力可能です。';
            $messages['from.before_or_equal'] = '予約開始日はセミナー開催日の4営業日前（'.$daysemi4before->format('Y/m/d').'）まで入力可能です。';
            $messages['to.after_or_equal'] = '予約終了日はセミナー開催日の3営業日後（'.$daysemi3after->format('Y/m/d').'）から入力可能です。';
        }

        $validator = Validator::make($request->all(), $rules, $messages);

        if($validator->fails()){
            return back()->withErrors($validator)->withInput($request->except('to'));
        }

        //使用状況の確認（From:予約開始日からTo:予約終了日の間にday_machineテーブルに存在するmachine_idをピックアップする）
        if($request->from != "" && $request->to != ""){
            $from = new Carbon($request->from);
            $to = new Carbon($request->to);
            $u = [];
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

        $view = $request->routeIs('admin.orders.create') ? 'order.admin-pctool' : 'pctool';
        return view($view, $data);
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

    public function edit(Request $request){
        $id = $request->id;
        $machine = MachineDetail::find($id);
        if($machine == null){
            return view('pctool/error', ['id' => $id]);
        }
        $data = [
            'id' => $id,
            'machine_details' => $machine,
            'supplies' => Supply::where('machine_id', $id)->get(),
        ];
        return view('pctool/edit', $data);
    }

    public function update(Request $request, int $id){
        $request->validate([
            'machine_name' => 'required|string|max:255',
            'machine_spec' => 'nullable|string|max:255',
            'machine_status' => 'nullable|string|max:255',
            'machine_since' => 'nullable|date',
            'machine_os' => 'nullable|string|max:255',
            'machine_cpu' => 'nullable|string|max:255',
            'machine_memory' => 'nullable|string|max:255',
            'machine_monitor' => 'nullable|string|max:255',
            'machine_powerpoint' => 'nullable|string|max:255',
            'machine_connector' => 'nullable|string|max:255',
            'machine_canto11' => 'nullable|string|max:255',
            'machine_memo' => 'nullable|string',
        ]);

        $machine = MachineDetail::find($id);
        if(!$machine){
            return redirect()->back()->withErrors(['error'=>'対象の機材が見つかりません。']);
        }

        $machine->machine_name = $request->input('machine_name');
        $machine->machine_spec = $request->input('machine_spec');
        $machine->machine_status = $request->input('machine_status');
        $machine->machine_since = $request->input('machine_since');
        $machine->machine_os = $request->input('machine_os');
        $machine->machine_cpu = $request->input('machine_cpu');
        $machine->machine_memory = $request->input('machine_memory');
        $machine->machine_monitor = $request->input('machine_monitor');
        $machine->machine_powerpoint = $request->input('machine_powerpoint');
        $machine->machine_camera = $request->has('machine_camera') ? 1 : 0;
        $machine->machine_hasdrive = $request->has('machine_hasdrive') ? 1 : 0;
        $machine->machine_connector = $request->input('machine_connector');
        $machine->machine_canto11 = $request->input('machine_canto11');
        $machine->machine_memo = $request->input('machine_memo');

        try {
            DB::transaction(function() use ($machine, $request, $id) {
                $machine->save();

                // handle deletions
                $deleted = $request->input('supplies_deleted', []);
                if(!empty($deleted)){
                    Supply::whereIn('supply_id', $deleted)->delete();
                }

                $suppliesInput = $request->input('supplies', []);
                foreach($suppliesInput as $supplyData){
                    $supplyId = $supplyData['supply_id'] ?? null;
                    $name = isset($supplyData['supply_name']) ? trim($supplyData['supply_name']) : '';
                    $memo = isset($supplyData['supply_memo']) ? trim($supplyData['supply_memo']) : '';

                    if($supplyId){
                        $s = Supply::find($supplyId);
                        if($s){
                            // Skip updating to blank to avoid accidental deletion
                            if($name === '' && $memo === '') continue;
                            $s->supply_name = $name;
                            $s->supply_memo = $memo;
                            $s->save();
                        }
                    } else {
                        // new supply: only create when at least one field provided
                        if($name !== '' || $memo !== ''){
                            $s = new Supply();
                            $s->machine_id = $id;
                            $s->supply_name = $name;
                            $s->supply_memo = $memo;
                            $s->save();
                        }
                    }
                }
            });
        } catch (\Illuminate\Database\QueryException $e) {
            Log::error('pctool.update DB error', ['id' => $id, 'error' => $e->getMessage(), 'errorInfo' => $e->errorInfo ?? null]);
            $msg = 'データベースが読み取り専用モードのため、更新できませんでした。DB設定（read_only/super_read_only）や接続先を確認してください。';
            return redirect()->back()->withInput()->withErrors(['db' => $msg]);
        } catch (\Exception $e) {
            Log::error('pctool.update unexpected error', ['id' => $id, 'error' => $e->getMessage()]);
            return redirect()->back()->withInput()->withErrors(['error' => '更新中にエラーが発生しました。管理者に連絡してください。']);
        }

        return redirect()->route('pctool.detail', ['id' => $id])->with('status', '更新しました。');
    }
}
