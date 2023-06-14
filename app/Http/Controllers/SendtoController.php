<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Models\MachineDetail;
use Illuminate\Http\Request;

class SendtoController extends Controller
{
    //
    public function view(Request $request){
        //セッション内のカートデータから機材情報をリストアップ
        $mid = $request->session()->get('Session.CartData');
        $data = [
            'records' => MachineDetail::whereIn('machine_id', $mid)->get(),
            'user' => Auth::user(),
            'input' => $request,

        ];

        //戻るボタンのアクション（入力保ったまま前のページに遷移）
        if($request->input('back') == 'back'){
            // dd($request);

            return redirect()->route('pctool.retry');
        }

        //セッションにトークンがない場合、作成
        if (!$request->session()->has('Session.Token')) {
            $request->session()->put('Session.Token', rand());
        }

        // dd($data);
        return view('sendto', $data);
    }
}
