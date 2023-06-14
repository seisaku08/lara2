<?php

namespace App\Http\Controllers;

use App\Models\MachineDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ConfirmController extends Controller
{
    //
    public function post(Request $request){

        $mid = $request->session()->get('Session.CartData');
        $data = [
            'machines' => MachineDetail::whereIn('machine_id', $mid)->get(),
            'user' => Auth::user(),
            'input' => $request

        ];

        $rules = [
                //
                'seminar_name' => 'required',
                'venue_zip' => 'exclude_if:seminar_venue_pending,true|required',
                'venue_addr1' => 'exclude_if:seminar_venue_pending,true|required',
                'venue_name' => 'exclude_if:seminar_venue_pending,true|required',
                'venue_tel' => 'exclude_if:seminar_venue_pending,true|required|digits_between:5,11',
                'shipping_arrive_day' => 'exclude_if:seminar_venue_pending,true|required|before:seminar_day|after:order_use_from',
                'shipping_return_day' => 'exclude_if:seminar_venue_pending,true|required|after_or_equal:seminar_day|before:order_use_to',
            ];

        $massages = [
                'seminar_name.required' => 'セミナー名は必ず入力してください。',
                'venue_zip.required' => '郵便番号は必ず入力してください。',
                'venue_addr1.required' => '住所は必ず入力してください。',
                'venue_name.required' => '配送先担当者は必ず入力してください。',
                'venue_tel.required' => '配送先電話番号は必ず入力してください。',
                'venue_tel.digits_between' => '配送先電話番号は市外局番から入力してください。',
                'shipping_arrive_day.required' => '到着希望日は必ず入力してください。',
                'shipping_arrive_day.before' => '到着希望日はセミナー開催日より前の日付を入力してください。',
                'shipping_arrive_day.after' => '到着希望日は使用開始日より後の日付を入力してください。',
                'shipping_return_day.required' => '返送機材発送予定日は必ず入力してください。',
                'shipping_return_day.after_or_equal' => '返送機材発送予定日はセミナー開催日以降（当日を含む）の日付を入力してください。',
                'shipping_return_day.before' => '返送機材発送予定日は使用終了日より前の日付を入力してください。',
               
            ];


        if($request->input('back') == 'back'){
            return redirect()->action('CartController@view');
        }
    
    
        $validator = Validator::make($request->all(), $rules, $massages);
        if($validator->fails()){
            return redirect()->route('sendto')->withErrors($validator)->withInput();
        }


        return view('confirm', $data);
    }
}
