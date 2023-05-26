<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MachineDetail extends Model
{
    use HasFactory;
    protected $table = 'machine_details';

    /// 主キーカラム名を指定
    protected $primaryKey = 'machine_id';

    // public function machine_detail_order()
    // {
    //     return $this->belongsToMany('App\Models\Order');
    // }

    public function days()
    {
        return $this->belongsToMany('App\Models\Day');
    }

}
