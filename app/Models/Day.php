<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Day extends Model
{
    use HasFactory;

    /// 主キーカラム名を指定
    protected $primaryKey = 'day';
    /// オートインクリメント無効化
    public $incrementing = false;
    /// Laravel 6.0+以降なら指定
    protected $keyType = 'date';

    public function machine_details()
    {
        return $this->belongsToMany('App\Models\MachineDetail');
    }


}
