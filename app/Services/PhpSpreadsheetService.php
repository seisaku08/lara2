<?php
namespace App\Services;

use Carbon\Carbon;
use Yasumi\Yasumi;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Style\Fill as Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment as Align;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx as XReader;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx as XWriter;
use App\Libs\Common;
use App\Models\MachineDetail;
use App\Models\DayMachine;
use App\Models\Order;


ini_set("max_execution_time", 180);
ini_set('memory_limit', '512M');

class PhpSpreadsheetService
{
    protected Spreadsheet $spreadsheet;

    /**
     * Excelファイルを出力.
     *
     * @return void
     */
    public function export(): void
    {
        
    //     // セルキャッシュ（phpTemp）
    // \PhpOffice\PhpSpreadsheet\Settings::setCacheStorageMethod(
    //     \PhpOffice\PhpSpreadsheet\CachedObjectStorageFactory::cache_to_phpTemp,
    //     ['memoryCacheSize' => '32MB']
    // );

    $this->spreadsheet = new Spreadsheet();
    $sheet = $this->spreadsheet;
    $activeSheet = $sheet->getActiveSheet();
    $activeSheet->getSheetView()->setZoomScale(85);

    $today = Carbon::now()->format('Ymd-His');

    // 機材一覧（横軸）
    $machines = MachineDetail::where('machine_is_expired', '!=', 1)
        ->get(['machine_id', 'machine_name']);

    // ヘッダ（機材ID / 機材名）
    $activeSheet->setCellValue([1, 1], '機種');
    $machineIndexMap = [];
    foreach ($machines as $key => $machine) {
        $col = $key + 2;
        $machineIndexMap[$machine->machine_id] = $col;
    }

    // ヘッダの値は fromArray でまとめて書き込み、スタイル適用回数を削減
    if ($machines->isNotEmpty()) {
        $machineIds = [];
        $machineNames = [];
        foreach ($machines as $machine) {
            $machineIds[] = $machine->machine_id;
            $machineNames[] = $machine->machine_name;
        }
        $activeSheet->fromArray([$machineIds], null, 'B1');
        $activeSheet->fromArray([$machineNames], null, 'B2');

        $lastMachineCol = Coordinate::stringFromColumnIndex($machines->count() + 1);
        $activeSheet->getStyle("B1:{$lastMachineCol}1")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('00E6B8B7');
        $activeSheet->getStyle("B2:{$lastMachineCol}2")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('00E6B8B7');
        $activeSheet->getStyle("B1:{$lastMachineCol}2")->getAlignment()->setHorizontal(Align::HORIZONTAL_CENTER);
    }

    // 日付範囲：400日
    $start = Carbon::today()->subMonth()->addDays(1);
    $end = (clone $start)->addDays(399);

    // 1回クエリで使用情報を取得（orders / users を結合）
    $usages = DayMachine::select(
        'day_machine_detail.day',
        'day_machine_detail.machine_id',
        'day_machine_detail.order_status',
        'orders.seminar_name',
        'orders.seminar_day',
        'orders.user_id',
        'orders.temporary_name',
        'orders.seminar_venue_pending',
        'users.name as user_name'
    )
    ->leftJoin('orders', 'day_machine_detail.order_id', '=', 'orders.order_id')
    ->leftJoin('users', 'orders.user_id', '=', 'users.id')
    ->whereBetween('day_machine_detail.day', [$start->toDateString(), $end->toDateString()])
    ->get();

    // A列の日付だけをまとめて書き込む（空セルは生成しない）
    $dateLabels = [];
    for ($i = 0; $i < 400; $i++) {
        $day = (clone $start)->addDays($i);
        $dateLabels[] = [$day->isoFormat('YYYY年M月D日（ddd）')];
    }
    $activeSheet->fromArray($dateLabels, null, 'A3');

    // 付随スタイル：A列の休日塗り（400 件）
    $holidayProviders = [];
    $holidayDateCells = [];
    for ($i = 0; $i < 400; $i++) {
        $day = (clone $start)->addDays($i);
        $rowIndex = $i + 3;
        $year = $day->year;
        if (!isset($holidayProviders[$year])) {
            $holidayProviders[$year] = Yasumi::create('Japan', $year);
        }
        if ($holidayProviders[$year]->isHoliday($day) || !$day->isWeekday()) {
            $holidayDateCells[] = "A{$rowIndex}";
        }
    }
    if (!empty($holidayDateCells)) {
        foreach ($holidayDateCells as $cellAddress) {
            $activeSheet->getStyle($cellAddress)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('00ffcccc');
        }
    }

    // 付随スタイル：使用セルの色付けは usage レコードのみ走査（最小限）
    $usageCellsByColor = [
        '00ff0000' => [],
        '00ffff00' => [],
        '00dddddd' => [],
        '0060ff70' => [],
    ];
    foreach ($usages as $u) {
        if (empty($u->seminar_name)) {
            continue;
        }
        $colIndex = $machineIndexMap[$u->machine_id] ?? null;
        if (!$colIndex) {
            continue;
        }

        $rowIndex = Carbon::parse($u->day)->diffInDays($start) + 3;
        if ($rowIndex < 3 || $rowIndex > 402) {
            continue;
        }

        $colLetter = Coordinate::stringFromColumnIndex($colIndex);
        $addr = "{$colLetter}{$rowIndex}";

        if ($u->user_id == 2) {
            $cellText = "{$u->seminar_name}（{$u->temporary_name}（仮））";
            $usageCellsByColor['00ff0000'][] = $addr;
        } else {
            $cellText = "{$u->seminar_name}（{$u->user_name}）";
            if ($u->seminar_venue_pending == true) {
                $usageCellsByColor['00ffff00'][] = $addr;
            } elseif ($u->order_status == '返却完了') {
                $usageCellsByColor['00dddddd'][] = $addr;
            } else {
                $usageCellsByColor['0060ff70'][] = $addr;
            }
        }

        $activeSheet->setCellValue($addr, $cellText);
    }

    foreach ($usageCellsByColor as $color => $addresses) {
        if (empty($addresses)) {
            continue;
        }
        foreach ($addresses as $cellAddress) {
            $activeSheet->getStyle($cellAddress)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB($color);
        }
    }


        // 1行目(ヘッダー)を固定
        $activeSheet->freezePane('B3');

        // 列幅を調整
        $activeSheet->getRowDimension(1)->setRowHeight(20);
        $activeSheet->getRowDimension(2)->setRowHeight(20);
        $activeSheet->getColumnDimension('A')->setWidth(20);

        // 最終行まで一括で
        $limitCol = $activeSheet->getHighestColumn();
        $limitCol++;
        $currentCol = "B";
        while( $currentCol != $limitCol ){
            $activeSheet->getColumnDimension($currentCol)->setWidth(16);
            $currentCol++;
        }

        
        $limitRow = $activeSheet->getHighestRow();
        $limitRow++;

        $currentRow = 2;
        while( $currentRow != $limitRow ){
            $activeSheet->getRowDimension($currentRow)->setRowHeight(14);
            $currentRow++;
        }


        $max_row = $activeSheet->getHighestRow(); //最終行（最下段）の取得
        $max_col = $activeSheet->getHighestColumn(); //最終列（右端）の取得
        $maxCellAddress = $max_col.$max_row; //最終セルのアドレスを格納する変数

        $styleArray = [
            'font' => [
                // フォント
                'name' => 'メイリオ',
                // フォントサイズ
                'size' => '9',
            ],

        ];

        $activeSheet->getStyle("A1:{$maxCellAddress}")->applyFromArray($styleArray);
        $activeSheet->getStyle("B32");

        // Excelファイルをダウンロード
        $file_name = "機材管理表_{$today}.xlsx";
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet;');
        header("Content-Disposition: attachment; filename=\"{$file_name}\"");
        header('Cache-Control: max-age=0');

        $writer = IOFactory::createWriter($sheet, 'Xlsx');
        $writer->save('php://output');
        exit;
    }
    
    
    public function invoice(Request $request): void
    {

        $xreader = new XReader();
        // $template = $_SERVER['DOCUMENT_ROOT']."/tmp/invoice_master.xlsx"; //任意のテンプレート
        $template = './tmp/invoice_master.xlsx'; //任意のテンプレート
        $xreader -> setReadDataOnly(false); //これをfalseにしないと複写できない
        $spread = $xreader -> load($template); //テンプレートをロードする
        // $sheet = $spread -> getActiveSheet();
        $sheet = $spread -> getSheet(0);
        $sheet->getSheetView() -> setZoomScale(85);

        $ship_data = Order::join('users', 'orders.user_id', '=', 'users.id')->join('shippings','orders.order_id', '=', 'shippings.order_id')->join('venues', 'shippings.venue_id', '=', 'venues.venue_id')->where('orders.order_id', '=', $request->id)->first();

        //送り状枚数計算用
         $machines = MachineDetail::join('machine_detail_order', 'machine_details.machine_id', '=', 'machine_detail_order.machine_id')->where('machine_detail_order.order_id', '=', $request->id)->orderBy('machine_details.machine_id','asc')->get();

         //到着時間指定
        if($ship_data->shipping_arrive_time == '午前中'){
            $shipping_arrive_time = '0812';
        }elseif($ship_data->shipping_arrive_time == '14時～16時'){
            $shipping_arrive_time = '1416';
        }elseif($ship_data->shipping_arrive_time == '16時～18時'){
            $shipping_arrive_time = '1618';
        }elseif($ship_data->shipping_arrive_time == '18時～20時'){
            $shipping_arrive_time = '1820';
        }elseif($ship_data->shipping_arrive_time == '20時～21時'){
            $shipping_arrive_time = '1921';
        }else{
            $shipping_arrive_time = '';
        }


        $invoice_data = [
            [//発払いのほう
                '0',//送り状種類
                Common::daybefore(Carbon::parse($ship_data->shipping_arrive_day),2)->format('Y/m/d'),//出荷予定日
                Carbon::parse($ship_data->shipping_arrive_day)->format('Y/m/d'),//お届け予定（指定）日
                $shipping_arrive_time,//配達時間帯
                $ship_data->venue_tel,//お届け先電話番号
                $ship_data->venue_zip,//お届け先郵便番号
                $ship_data->venue_addr1,//お届け先住所1
                $ship_data->venue_addr2,//お届け先住所2
                $ship_data->venue_addr3,//お届け先会社・部門名１
                $ship_data->venue_addr4,//お届け先会社・部門名２
                $ship_data->venue_name,//お届け先名
                '03-3292-1488',//ご依頼主電話番号
                '1010047',//ご依頼主郵便番号
                '東京都千代田区内神田1-7-5',//ご依頼主住所
                '旭栄ビル',//ご依頼主住所（アパートマンション名）
                '株式会社 大應',//ご依頼主名
                'セミナー使用機材',//品名１
                $ship_data->seminar_name,//品名２
                '精密機器',//荷扱い１
                "予約No.".$ship_data->order_no,//記事
                // "=roundup(".$machines->count()."/7,0)",//発行枚数
                (int)ceil($machines->count()/7),//発行枚数
                '3',//個数口枠の印字
                '033292148807',//ご請求先顧客コード
                '01',//運賃管理番号
            ],
            [//着払いのほう
                5,//送り状種類
                Carbon::parse($ship_data->shipping_return_day)->format('Y/m/d'),//出荷予定日
                Common::dayafter(Carbon::parse($ship_data->shipping_return_day),1)->format('Y/m/d'),//お届け予定（指定）日
                '0812',//配達時間帯
                '03-3292-1488',//お届け先電話番号
                '1010047',//お届け先郵便番号
                '東京都千代田区内神田1-7-5',//お届け先住所1
                '旭栄ビル 3F',//お届け先住所2
                '株式会社 大應',//お届け先会社・部門名１
                '機材管理システム　管理チーム',//お届け先会社・部門名２
                '藤森',//お届け先名
                '03-3292-1488',//ご依頼主電話番号
                '1010047',//ご依頼主郵便番号
                '東京都千代田区内神田1-7-5',//ご依頼主住所
                '旭栄ビル',//ご依頼主住所（アパートマンション名）
                '株式会社 大應',//ご依頼主名
                'セミナー使用機材 返送',//品名１
                $ship_data->seminar_name,//品名２
                '精密機器',//荷扱い１
                "予約No.".$ship_data->order_no,//記事
                (int)ceil($machines->count()/7),//発行枚数
                null,//個数口枠の印字
                null,//ご請求先顧客コード
                null,//運賃管理番号
            ]
        ];


        $sheet->fromArray($invoice_data,NULL, 'A2');


        //納品書を作る

        $nouhin = $spread -> getSheet(1);
        $nouhin->setCellValue('B3', "{$ship_data->name} 様（予約No.{$ship_data->order_no}）");
        $nouhin->setCellValue('B9', "案件名：{$ship_data->seminar_name}");
        $nouhin->setCellValue('E3', Carbon::parse($ship_data->shipping_arrive_day)->format("Y年n月j日"));

        $nouhin_data = [];
        foreach($machines as $key => $machine){
        $nouhin_data[$key] = [
            $key+1,//通し番号
            $machine->machine_id,
            $machine->machine_name,

        ];
        }
        $nouhin->fromArray($nouhin_data,NULL, "A11");
        // dd($invoice_data,$machines,$nouhin_data);

        //作業指示書を作る
        $shiji = $spread -> getSheet(2);
        $ship_day = Carbon::parse($ship_data->shipping_arrive_day)->format("n月j日");
        $shiji->setCellValue('A2', Carbon::now()->format("Y年n月j日"));
        $shiji->setCellValue('A6', "No.{$ship_data->order_no}");
        $shiji->setCellValue('C6', $ship_data->seminar_name);
        $shiji->setCellValue('A8', Carbon::parse($ship_data->seminar_day)->format("Y年n月j日"));
        $shiji->setCellValue('C8', Common::daybefore(Carbon::parse($ship_data->shipping_arrive_day),2)->format("n月j日"));
        $shiji->setCellValue('E8', "{$ship_day}－{$ship_data->shipping_arrive_time}");
        $shiji->setCellValue('A10', $ship_data->shipping_note);

        $shiji_data = [];
        foreach($machines as $key => $machine){
            $shiji_data[$key] = [
                $key+1,//通し番号
                $machine->machine_id." - ".$machine->machine_name,
    
            ];
            }
            $shiji->fromArray($shiji_data,NULL, "A13");
    
        // Excelファイルをダウンロード
        $file_name = "予約No_{$ship_data->order_no}.xlsx";
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet;');
        header("Content-Disposition: attachment; filename=\"{$file_name}\"");
        header('Cache-Control: max-age=0');

        $writer = IOFactory::createWriter($spread, 'Xlsx');
        $writer->save('php://output');
        exit;
    }
    
 
}

