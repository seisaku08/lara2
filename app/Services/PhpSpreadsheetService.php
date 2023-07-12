<?php
namespace App\Services;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Style\Fill as Fill;
use App\Models\MachineDetail;
use App\Models\DayMachine;
use Carbon\Carbon;
use Yasumi\Yasumi;

ini_set("max_execution_time",180);
ini_set('memory_limit', '512M');

class PhpSpreadsheetService
{
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

        $machines = MachineDetail::all();
        // ヘッダー部分出力
        //1列目(A1)
        $sheet->getActiveSheet()
        ->setCellValue([1,1], '機種');
        //A2以降横軸＝機材
        foreach ($machines as $key => $machine) {
            $sheet->getActiveSheet()
            ->setCellValue([$key+2,1], $machine->machine_name);
            $sheet->getActiveSheet()->getStyle([$key+2,1])->
            getFill() -> setFillType(Fill::FILL_SOLID) -> getStartColor() -> setARGB('00E6B8B7');
        }


        
        //縦軸＝1日を400日間（だいたい1カ月前から1年後まで）
        for($i = 1; $i <= 400; $i++) {
            $day = Carbon::today()->submonth()->addDay($i);
            $holidays = Yasumi::create('Japan', $day->year);

            $sheet->getActiveSheet()
            ->setCellValue([1,$i+1], $day->isoFormat('YYYY年M月D日（ddd）'));
            if($holidays->isHoliday($day) == true || $day->isweekday() != true){
                $sheet->getActiveSheet()->getStyle([1,$i+1])->
                getFill() -> setFillType(Fill::FILL_SOLID) -> getStartColor() -> setARGB('00ffcccc');

            }

            //使用中の機材を検索、セミナー名を取得
            foreach ($machines as $key => $machine) {
            $usage = DayMachine::join('machine_detail_order', 'day_machine_detail.machine_id', '=', 'machine_detail_order.machine_id')
            ->join('orders', 'machine_detail_order.order_id', '=', 'orders.order_id')
            ->join('users', 'orders.user_id', '=', 'users.id')
            ->where('day', $day)->where('machine_detail_order.machine_id', $machine->machine_id)->first();

            //使用中のセミナーがある場合書き込む
            if(!empty($usage->seminar_name)){
                $uday = Carbon::parse($usage->seminar_day)->format('md');
                $usage_data = "{$usage->seminar_name}（{$usage->name}）";
                $sheet->getActiveSheet()
                ->setCellValue([$key+2,$i+1], $usage_data);
                $sheet->getActiveSheet()->getStyle([$key+2,$i+1])->
                getFill() -> setFillType(Fill::FILL_SOLID) -> getStartColor() -> setARGB('00ff7f50');


            }

            }
            //デバッグ用
            // $sheet->getActiveSheet()
            // ->setCellValue([$key+2,$i+1], floor(memory_get_usage() / 1024).'KB');
        }


        // 1行目(ヘッダー)を固定
        $sheet->getActiveSheet()->freezePane('A2');

        // 列幅を調整
        $sheet->getActiveSheet()->getRowDimension(1)->setRowHeight(40);
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
        $sheet->getActiveSheet()->getStyle("B2");

        // Excelファイルをダウンロード
        $file_name = "機材管理表_{$today}.xlsx";
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet;');
        header("Content-Disposition: attachment; filename=\"{$file_name}\"");
        header('Cache-Control: max-age=0');

        $writer = IOFactory::createWriter($sheet, 'Xlsx');
        $writer->save('php://output');
        exit;
    }
    
    
}

