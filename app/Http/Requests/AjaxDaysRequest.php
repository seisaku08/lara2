<?php

namespace App\Http\Requests;

use Illuminate\Http\Request;
use Illuminate\Foundation\Http\FormRequest;
use App\Libs\Common;
use Carbon\Carbon;

class AjaxDaysRequest extends FormRequest
{
    //オーバーライド
            //使用日関連の変数を作る
    
    public function __construct(Request $request) {
    $day1after = Common::dayafter(today()->copy,1);
    $day4after = Common::dayafter(today()->copy,4);
    $day5after = Common::dayafter(today()->copy,5);
    $daysemi3before = Common::daybefore(Carbon::parse($request->seminar_day)->copy,3);
    $daysemi4before = Common::daybefore(Carbon::parse($request->seminar_day)->copy,4);
    $daysemi3after = Common::dayafter(Carbon::parse($request->seminar_day)->copy,3);

    }
    
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        global $day5after;
        global $daysemi4before;
        global $day1after;
        global $daysemi3after;

        return [
            'seminar_day' => ['date','required_with_all:from,to', "after_or_equal:{$day5after}"],
            'from' => ['required_with_all:seminar_day,to', "after_or_equal:{$day1after}", "before_or_equal:{$daysemi4before}"],
            'to' => ['required_with_all:seminar_day,from', "after_or_equal:{$daysemi3after}"],

            //
        ];
    }
    public function messages(): array{
        
        return [
            // 'seminar_day.required_with_all' => 'セミナー開催日は入力必須です。',
            // 'from.required_with_all' => '予約開始日は入力必須です。',
            // 'to.required_with_all' => '予約終了日は入力必須（セミナー開催日の3営業日後（'.$this->daysemi3after->format('Y/m/d').'）から入力可能）です。',
            // 'seminar_day.after_or_equal' => 'セミナー開催日は本日の5営業日後（'.$this->day5after->format('Y/m/d').'）から入力可能です。',
            // 'from.after_or_equal' => '予約開始日は翌営業日以降（'.$this->day1after->format('Y/m/d').'）から入力可能です。',
            // 'from.before_or_equal' => '予約開始日はセミナー開催日の4営業日前（'.$this->daysemi4before->format('Y/m/d').'）まで入力可能です。',
            // 'to.after_or_equal' => '予約終了日はセミナー開催日の3営業日後（'.$this->daysemi3after->format('Y/m/d').'）から入力可能です。',

        ];
    }

}
