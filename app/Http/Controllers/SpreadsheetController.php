<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\PhpSpreadsheetService;
use Illuminate\View\View;

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
}

