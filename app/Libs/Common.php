<?php

namespace app\Libs;
use Carbon\Carbon;
use DateTime;
use Yasumi\Yasumi;


/**
 * 
 *  @method dayafter
 * 
 **/
class Common
{

    public static function dayafter(object $day, int $d) :object {
        Carbon::setLocale('ja');
        $holidays = Yasumi::create('Japan', $day->year);
        for ($dcalc = $day, $i=0; $i<$d;){
                $dcalc->addDay();
            //平日かつ非祝日の判定
            if ($dcalc->isWeekday() && !$holidays->isHoliday($dcalc)){
                $i++;
            }
        }
        return $dcalc;
    }

    public static function daybefore(object $day, int $d) :object {
        Carbon::setLocale('ja');
        $holidays = Yasumi::create('Japan', $day->year);
        for ($dcalc = $day, $i=0; $i<$d;){
                $dcalc->subDay();
            //平日かつ非祝日の判定
            if ($dcalc->isWeekday() && !$holidays->isHoliday($dcalc)){
                $i++;
            }
        }
        return $dcalc;
    }

    public static function businessdaycheck($date)  {
        if($date == null){
            $numberOfDay = '日時未入力';
            return $numberOfDay;
        }
        $startDate = Carbon::today();
        $endDate = Carbon::parse($date);

        // 土日を除く平日を取得
        $days = (int)$startDate->diffInDaysFiltered(
            function (Carbon $date) {
                return $date->isWeekday();
            }, $endDate
        );

        // 祝日を取得
        $holidays = Yasumi::create('Japan', now()->year, 'ja_JP');

        if(Carbon::create($endDate)->isFuture()){
            $holidaysInBetweenDays = $holidays->between(
                \DateTime::createFromFormat('m/d/Y', $startDate->format('m/d/Y')),
                \DateTime::createFromFormat('m/d/Y', $endDate->format('m/d/Y'))
            );
            $timing = '営業日後';
        }else{
            $holidaysInBetweenDays = $holidays->between(
                \DateTime::createFromFormat('m/d/Y', $endDate->format('m/d/Y')),
                \DateTime::createFromFormat('m/d/Y', $startDate->format('m/d/Y'))
            );
            $timing = '営業日前';

        }

        $numberOfHoliday = 0;
        foreach ($holidaysInBetweenDays as $holiday) {
            if ((new Carbon($holiday))->isWeekend() === false) {
                $numberOfHoliday++;
            }
        }

        // さらに祝日の数を引いた平日の日数を取得
        if($days - $numberOfHoliday == 0){
            $numberOfDay = '当日';
        }else{
            $numberOfDay = $days - $numberOfHoliday.$timing;
        }
        return $numberOfDay;
    }


    
}
