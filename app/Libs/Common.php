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
        $holidays = Yasumi::create('Japan', now()->year);
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
        $holidays = Yasumi::create('Japan', now()->year);
        for ($dcalc = $day, $i=0; $i<$d;){
                $dcalc->subDay();
            //平日かつ非祝日の判定
            if ($dcalc->isWeekday() && !$holidays->isHoliday($dcalc)){
                $i++;
            }
        }
        return $dcalc;
    }


    
}
