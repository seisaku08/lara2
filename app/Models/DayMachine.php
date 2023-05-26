<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DayMachine extends Model
{
    protected $table = 'day_machine_detail';
    protected $guarded = array(['id']);

    use HasFactory;
}
