<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;
    /// 主キーカラム名を指定
    protected $primaryKey = 'order_id';
    protected $guarded = array(['order_id']);

    public function machine_detail_order()
    {
        return $this->belongsToMany('App\Models\MachineDetail');
    }

}
