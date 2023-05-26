<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MachineDetail;
use Illuminate\Support\Facades\Auth;

class HeloController extends Controller
{
    public function view(Request $request){
    $data = [
        'records' => MachineDetail::all(),
        'user' => Auth::user(),
        'input' => $request,
        'inUse' => $request->session()->get('inUse')

    ];
    // dd($data);
    return view('helo', $data);
    }
}