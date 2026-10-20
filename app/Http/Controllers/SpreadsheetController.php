<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\PhpSpreadsheetService;
// use DragonCode\Contracts\Cashier\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;
use Carbon\Carbon;

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
        $startDate = Carbon::today()->subMonth()->addDays(1);

        return view('xlsdl', [
            'defaultStartDate' => $startDate->toDateString(),
            'defaultEndDate' => (clone $startDate)->addDays(399)->toDateString(),
        ]);
    }

    /**
     * Excelファイルをダウンロード.
     *
     * @return View
     */
    public function download(Request $request): View
    {
        if (auth()->check() && Gate::allows('sys-ad')) {
            $dates = $request->validate([
                'start_date' => ['required', 'date_format:Y-m-d'],
                'end_date' => ['required', 'date_format:Y-m-d', 'after_or_equal:start_date'],
            ]);

            $this->spreadsheet->export(
                Carbon::createFromFormat('Y-m-d', $dates['start_date'])->startOfDay(),
                Carbon::createFromFormat('Y-m-d', $dates['end_date'])->startOfDay()
            );
        } else {
            $this->spreadsheet->export();
        }

        return view('xlsdl');
    }

    /**
     * 送り状発行.
     *
     * @return RedirectResponse
     */
    public function invoice(Request $request): RedirectResponse
    {
        $this->spreadsheet->invoice($request);
        return back();
    }
}
