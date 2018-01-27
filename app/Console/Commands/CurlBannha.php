<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Data;
use DB;

class CurlBannha extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cron:bds';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $data = new Data();
        $result = $data->banhangsg('http://bannhasg.com/ban-nha-rieng-tp-hcm.htm');
        if(count($result) == 20){
            $data_bannha2 = $data->banhangsg("http://bannhasg.com/ban-nha-rieng-tp-hcm/p2.htm");
            $result = array_merge($result,$data_bannha2);
        }

        $result = array_reverse($result);
        $results = DB::table('data')->insert($result);
        if(count($result) < 1){
            $this->info('Fail');
        }else{
            $this->info('Success');
        }
        
    }
}
