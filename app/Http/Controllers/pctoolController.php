<?php

namespace App\Http\Controllers;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Illuminate\Http\Request;
use App\Models\MachineDetail;
use App\Models\DayMachine;
use App\Models\User;
use App\Models\Order;
use App\Models\Maintenance;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use DateTime;
use Illuminate\Auth\Events\Validated;

class pctoolController extends Controller
{
    public function view(Request $request){

        $data = [
            'records' => MachineDetail::all(),
            'user' => Auth::user(),
            'input' => $request,

        ];


        if($request->from != "" && $request->to != ""){
            $from = new DateTime($request->from);
            $to = new DateTime($request->to);
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
        if($request->from != "" && $request->to != ""){
            $from = new DateTime($request->from);
            $to = new DateTime($request->to);
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
