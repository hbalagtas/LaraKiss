<?php

namespace LaraKiss\Console\Commands;

use Artisan;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use LaraKiss\Episode;

class DownloadEpisode extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'kiss:getepisode {id?}';

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
        try {
            
            if (is_null($this->argument('id'))){
                $episode = Episode::whereDownloaded(false)->whereProcessing(false)->orderBy('id', 'desc')->first();
            } else {
                $episode = Episode::find($this->argument('id'));    
            }
            \Log::info('Downloading: ' . $episode->show->name . ' - ' . $episode->name);
            if (!is_null($episode) && !$episode->processing ){
                /*$episode->processing = true;
                $episode->save();*/
                $exitCode = Artisan::call('kiss:download',['url' => $episode->url]);
                /*if ( $exitCode ){
                    $episode->downloaded = true;
                    $episode->processing = false;
                    $episode->save();
                } else {
                    $episode->downloaded = false;
                    $episode->processing = false;
                    $episode->save();
                }*/
            } else {
                $this->info('Could not download episode!');
            }
            
        } catch (\Exception $e) {
            /*$episode->downloaded = false;
            $episode->processing = false;
            $episode->save();*/
            $this->info('Failed to download the episode');
        }
    }
}
