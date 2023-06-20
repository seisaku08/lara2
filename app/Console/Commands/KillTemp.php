<?php

namespace App\Console\Commands;

use App\Models\Temporary;
use Carbon\Carbon;
use Illuminate\Console\Command;

class KillTemp extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'kill-temp';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
    //30分より前に登録されているTemporaryデータを抽出
    Temporary::where('created_at', '<=', Carbon::now()->subminute(30))->delete();
    
        //
    }
}
