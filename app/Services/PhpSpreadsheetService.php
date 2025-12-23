<?php
namespace App\Services;

use Carbon\Carbon;
use Yasumi\Yasumi;
use Illuminate\Http\Request;
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
    protected $spreadsheet;

    /**
     * Excelファイルを出力.
     *
     * @return void
     */
    public function export(): void
    {
        $this->spreadsheet = new Spreadsheet();
        $sheet = $this->spreadsheet;
        $sheet -> getActiveSheet()->getSheetView() -> setZoomScale(85);

        $today = Carbon::now()->format('Ymd-His');

        // $machines = MachineDetail::all();
        $machines = MachineDetail::where('machine_is_expired','!=',1)->get();
        // ヘッダー部分出力
        //1列目(A1)
        $sheet->getActiveSheet()
        ->setCellValue([1,1], '機種');
        //A2以降横軸＝機材
        foreach ($machines as $key => $machine) {
            $sheet->getActiveSheet()
            ->setCellValue([$key+2,1], $machine->machine_id);
            $sheet->getActiveSheet()->getStyle([$key+2,1])->
            getFill() -> setFillType(Fill::FILL_SOLID) -> getStartColor() -> setARGB('00E6B8B7');
            $sheet->getActiveSheet()->getStyle([$key+2,1])
            ->getAlignment() -> setHorizontal(Align::HORIZONTAL_CENTER);
            $sheet->getActiveSheet()
            ->setCellValue([$key+2,2], $machine->machine_name);
            $sheet->getActiveSheet()->getStyle([$key+2,2])->
            getFill() -> setFillType(Fill::FILL_SOLID) -> getStartColor() -> setARGB('00E6B8B7');
            $sheet->getActiveSheet()->getStyle([$key+2,2])->getAlignment() -> setHorizontal(Align::HORIZONTAL_CENTER);
        }


        
        //縦軸＝1日を400日間（だいたい1カ月前から1年後まで）
        for($i = 1; $i <= 400; $i++) {
            $day = Carbon::today()->submonth()->addDay($i);
            $holidays = Yasumi::create('Japan', $day->year);

            $sheet->getActiveSheet()
            ->setCellValue([1,$i+2], $day->isoFormat('YYYY年M月D日（ddd）'));
            if($holidays->isHoliday($day) == true || $day->isweekday() != true){
                $sheet->getActiveSheet()->getStyle([1,$i+2])->
                getFill() -> setFillType(Fill::FILL_SOLID) -> getStartColor() -> setARGB('00ffcccc');

            }

            //使用中の機材を検索、セミナー名を取得
            foreach ($machines as $key => $machine) {
            $usage = DayMachine::join('machine_detail_order', 'day_machine_detail.machine_id', '=', 'machine_detail_order.machine_id')
            ->join('orders', 'machine_detail_order.order_id', '=', 'orders.order_id')
            ->join('users', 'orders.user_id', '=', 'users.id')
            ->where('day', $day)->where('machine_detail_order.machine_id', $machine->machine_id)
            ->where('orders.order_use_from', '<=', $day )
            ->where('orders.order_use_to', '>=', $day )
            ->first();

            // dd($usage);

            //使用中のセミナーがある場合書き込む
            if(!empty($usage->seminar_name)){
                $uday = Carbon::parse($usage->seminar_day)->format('md');
                if($usage->user_id == 2){
                    $usage_data = "{$usage->seminar_name}（{$usage->temporary_name}（仮））";
                }else{
                    $usage_data = "{$usage->seminar_name}（{$usage->name}）";
                }
                $sheet->getActiveSheet()
                ->setCellValue([$key+2,$i+2], $usage_data);
                //仮登録の場合、セルを真っ赤に塗りつぶす
                if($usage->user_id == 2){
                    $sheet->getActiveSheet()->getStyle([$key+2,$i+2])->
                    getFill() -> setFillType(Fill::FILL_SOLID) -> getStartColor() -> setARGB('00ff0000');
                //住所登録がまだの場合、セルを黄色に塗りつぶす
                }elseif($usage->seminar_venue_pending == true){
                    $sheet->getActiveSheet()->getStyle([$key+2,$i+2])->
                    getFill() -> setFillType(Fill::FILL_SOLID) -> getStartColor() -> setARGB('00ffff00');
                //完了した予約の場合、セルをグレーに塗りつぶす
                }elseif($usage->order_status == '返却完了'){
                    $sheet->getActiveSheet()->getStyle([$key+2,$i+2])->
                    getFill() -> setFillType(Fill::FILL_SOLID) -> getStartColor() -> setARGB('00dddddd');
                //不備のない予約の場合、セルを緑に塗りつぶす
                }else{
                    $sheet->getActiveSheet()->getStyle([$key+2,$i+2])->
                    getFill() -> setFillType(Fill::FILL_SOLID) -> getStartColor() -> setARGB('0060ff70');
            }

            }
            }
            //デバッグ用
            // $sheet->getActiveSheet()
            // ->setCellValue([$key+2,$i+1], floor(memory_get_usage() / 1024).'KB');
        }


        // 1行目(ヘッダー)を固定
        $sheet->getActiveSheet()->freezePane('B3');

        // 列幅を調整
        $sheet->getActiveSheet()->getRowDimension(1)->setRowHeight(20);
        $sheet->getActiveSheet()->getRowDimension(2)->setRowHeight(20);
        $sheet->getActiveSheet()->getColumnDimension('A')->setWidth(20);

        // 最終行まで一括で
        $limitCol = $sheet->getActiveSheet() -> getHighestColumn();
        $limitCol++;
        $currentCol = "B";
        while( $currentCol != $limitCol ){
            $sheet->getActiveSheet()->getColumnDimension($currentCol)->setWidth(16);
            $currentCol++;
        }

        
        $limitRow = $sheet->getActiveSheet() -> getHighestRow();
        $limitRow++;

        $currentRow = 2;
        while( $currentRow != $limitRow ){
            $sheet->getActiveSheet()->getRowDimension($currentRow)->setRowHeight(14);
            $currentRow++;
        }


        $max_row = $sheet->getActiveSheet()->getHighestRow(); //最終行（最下段）の取得
        $max_col = $sheet->getActiveSheet()->getHighestColumn(); //最終列（右端）の取得
        $maxCellAddress = $max_col.$max_row; //最終セルのアドレスを格納する変数

        $styleArray = [
            'font' => [
                // フォント
                'name' => 'メイリオ',
                // フォントサイズ
                'size' => '9',
            ],

        ];

        $sheet->getActiveSheet()->getStyle("A1:{$maxCellAddress}")->applyFromArray($styleArray);
        $sheet->getActiveSheet()->getStyle("B32");

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

        foreach($machines as $key => $machine){
            $nouhin_data[$key] = [
                $key+1,//通し番号
                $machine->machine_id." - ".$machine->machine_name,
    
            ];
            }
            $shiji->fromArray($nouhin_data,NULL, "A13");
    
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

