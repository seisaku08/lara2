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
                'seminar_day' => 'required',
                'seminar_name' => 'required',
                'venue_zip' => 'required',
                'venue_addr1' => 'required',
                'venue_name' => 'required',
                'venue_tel' => 'required|digits_between:5,11',
                'shipping_arrive_day' => 'required|before:seminar_day|after:today',
                'shipping_return_day' => 'required|after:shipping_arrive_day',
            ];

        $massages = [
                'seminar_day.required' => 'セミナー開催日は必ず入力してください。',
                'seminar_name.required' => 'セミナー名は必ず入力してください。',
                'venue_zip.required' => '郵便番号は必ず入力してください。',
                'venue_addr1.required' => '住所は必ず入力してください。',
                'venue_name.required' => '配送先担当者は必ず入力してください。',
                'venue_tel.required' => '配達先電話番号は必ず入力してください。',
                'venue_tel.digits_between' => '配達先電話番号は市外局番から入力してください。',
                'shipping_arrive_day.required' => '到着希望日時は必ず入力してください。',
                'shipping_arrive_day.before' => '到着希望日時はセミナー開催日より前の日付を入力してください。',
                'shipping_arrive_day.after' => '到着希望日時は本日より後の日付を入力してください。',
                'shipping_return_day.required' => '返送予定日は必ず入力してください。',
                'shipping_return_day.after' => '返送予定日は到着希望日より後の日付を入力してください。',
               
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
