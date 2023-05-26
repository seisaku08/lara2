<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sequence extends Model
{
    use HasFactory;
    // 独自コネクション名
    // ※ここでしか使わないからあえて config に書かない
    const DB_CONNECTION = 'mysql_sequence';

    // 独自コネクション
    protected $connection = self::DB_CONNECTION;

    // タイムスタンプなし
    public $timestamps = false;

    // キー変更
    protected $primaryKey = 'key';

    protected static function boot()
    {
        parent::boot();

        // デフォルトコネクションをコピーして独自コネクションを作る
        config(['database.connections.' . self::DB_CONNECTION =>
            config('database.connections.' . config('database.default')),
        ]);
    }
}
