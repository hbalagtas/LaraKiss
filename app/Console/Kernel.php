<?php

namespace LaraKiss\Console;

use Artisan;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use LaraKiss\Config as LaraKissConfig;
use LaraKiss\Episode;
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
        Commands\RestartWebdriver::class,
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
            $pause_downloads = LaraKissConfig::where('setting', 'pause_downloads')->first(); 
            #if (!$pause_downloads->value){
            if ( true ) {
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
            } else {
                \Log::info("Downloads are paused.");
                if ( $pause_downloads->updated_at->diffInMinutes() > 30) {
                    \Log::info("Resuming downloads.");
                    $pause_downloads->value = false;
                    $pause_downloads->save();
                }                
            }
                 
        })->cron('*/'.rand(10,20).' * * * * *')
            ->timezone('America/Toronto')
            ->name('Download Episode')
            ->when(function () {
                return date('H') >= env('DOWNLOAD_START', 00) && date('H') <= env('DOWNLOAD_END', 24);
            });;

        /*$schedule->call(function(){
            Artisan::call('kiss:restartwd');
        })->everyMinute();*/

        // $schedule->command('inspire')
        //          ->hourly();
    }
}
