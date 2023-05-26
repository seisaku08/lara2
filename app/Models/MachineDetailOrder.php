<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MachineDetailOrder extends Model
{
    protected $table = 'machine_detail_order';
    protected $guarded = array(['id']);

    use HasFactory;
}
