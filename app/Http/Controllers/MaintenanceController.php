<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MachineDetail;
use App\Models\Order;
use Carbon\Carbon;

class MaintenanceController extends Controller
{
    //
    public function index(Request $request){

        $data = [
            'records' => MachineDetail::all(),
            'orders' => Order::orderBy('seminar_day','asc')->get(),
            'input' => $request,
        ];
        return view('maintenance.index', $data);

    }

    public function selorder(Request $request){

        $data = [
            'records' => MachineDetail::all(),
            'orders' => Order::orderBy('seminar_day','asc')->get(),
            'input' => $request,
        ];
        return view('maintenance.work', $data);

    }

    public function selpc(Request $request){
        // dd($request);
        $data = [
            'records' => MachineDetail::all(),
            'orders' => Order::orderBy('seminar_day','asc')->get(),
            'input' => $request,
        ];
        return view('maintenance.work', $data);

    }

}
