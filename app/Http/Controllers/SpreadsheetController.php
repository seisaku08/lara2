<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\PhpSpreadsheetService;
// use DragonCode\Contracts\Cashier\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\Request;

class SpreadsheetController extends Controller
{
    protected $spreadsheet;

    /**
     * 
     * @param PhpSpreadsheetService $spreadsheet
     * 
     * @return void
     */
    public function __construct(PhpSpreadsheetService $spreadsheet)
    {
        $this->spreadsheet = $spreadsheet;
    }
    
    /**
     * Excelダウンロードページを表示.
     *
     * @return View
     */
    public function index(): View
    {
        return view('xlsdl');
    }

    /**
     * Excelファイルをダウンロード.
     *
     * @return View
     */
    public function download(): View
    {
        $this->spreadsheet->export();
        return view('xlsdl');
    }

    /**
     * 送り状発行.
     *
     * @return View
     */
    public function invoice(Request $request): View
    {
        $this->spreadsheet->invoice($request);
        return back();
    }
}

