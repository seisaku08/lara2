<?php

namespace App\Services;

use App\Models\Sequence;

class SequenceService
{
    /**
     * 例）受注番号を取得する
     * @return mixed
     */
    public function getNewOrderNo(int $store_id)
    {
        $value = $this->getNewValueAndCommit('orders:'.$store_id);

        return $value;
    }

    /**
     * 単純に新しい番号を取得する
     *
     * @param  string    $key      同じキー名を与えると前回の続きの値を返す
     * @return int|float $sequence 基本はintだがPHPの限界値を超えるとfloatになる
     */
    protected function getNewValueAndCommit(string $key)
    {
        // config/sequence.php という設定ファイルを作って初期値を用意しておける。
        // なければ 1 からスタート
        $default = config("sequence.default.$key", 1);

        $sequence = Sequence::lockForUpdate()->find($key);
        if( !$sequence ){
            $sequence = new Sequence;
            $sequence->key = $key;
        }

        if (($sequence->sequence ?? 0) < $default) {
            $sequence->sequence = $default;
        } else {
            $sequence->sequence = ($sequence->sequence??0) + 1;
        }
        $sequence->save();

        return $sequence->sequence;
    }

}