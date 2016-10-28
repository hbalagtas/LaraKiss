<?php

namespace LaraKiss\Console\Commands;

use Illuminate\Console\Command;
use LaraKiss\Config as LaraKissConfig;

class RestartWebdriver extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'kiss:restartwd';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Restart webdriver';

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
        $pause_downloads = LaraKissConfig::where('setting', 'pause_downloads')->first();
        #if ($pause_downloads->value){
            \Log::info("Restarting webdriver services");
            $cmd = '/home/vagrant/Code/larakiss/killdriver.sh';
            exec($cmd);
            $cmd2 = '/home/vagrant/Code/larakiss/screen.sh';
            popen($cmd2, "r");
            $pause_downloads->value = false;
            $pause_downloads->save();
        #}
    }
}
