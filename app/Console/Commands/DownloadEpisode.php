<?php

namespace LaraKiss\Console\Commands;

use Illuminate\Console\Command;
use LaraKiss\Episode;
use Artisan;

class DownloadEpisode extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'kiss:downloadepisode {id?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Downloads the currently queued or specific episode';

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
        if (is_null($this->argument('id'))){
            $episode = Episode::whereDownloaded(false)->whereProcessing(false)->first();
        } else {
            $episode = Episode::find($this->argument('id'));    
        }
        
        if (!is_null($episode) && !$episode->processing ){
            $episode->processing = true;
            $episode->save();
            $exitCode = Artisan::call('kiss:download',['url' => $episode->url]);
            if ( $exitCode == 0){
                $episode->downloaded = true;
                $episode->processing = false;
                $episode->save();
            }
        } else {
            $this->info('Could not download episode!');
        }
    }
}
