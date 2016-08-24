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
            DB::beginTransaction();
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
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            $this->info('Failed to download the episode');
        }
    }
}
