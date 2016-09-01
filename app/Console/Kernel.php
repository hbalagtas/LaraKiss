<?php

namespace LaraKiss\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use LaraKiss\Episode;
use Artisan;
class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        // Commands\Inspire::class,
        Commands\Download::class,
        Commands\DownloadEpisode::class,
        Commands\AddShow::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->call(function(){
            \Log::info('Running episode downloader');
            if ( Episode::whereProcessing(true)->count() < env('DOWNLOAD_MAX', 2) ) {                
                $episode = Episode::whereDownloaded(false)->whereProcessing(false)->orderBy('id', 'desc')->first();
                if ( $episode ){
                    $exitCode = Artisan::call('kiss:getepisode',['id' => $episode->id]);    
                } else {
                    \Log::info("Nothing to download...");
                }                
            } else {
                \Log::info("Queue is currently full retrying later...");
            }      
        })->cron('*/'.rand(2,5).' * * * * *')
            ->timezone('America/Toronto')
            ->name('Download Episode')
            ->when(function () {
                return date('H') >= env('DOWNLOAD_START', 00) && date('H') <= env('DOWNLOAD_END', 24);
            });;

        // $schedule->command('inspire')
        //          ->hourly();
    }
}
