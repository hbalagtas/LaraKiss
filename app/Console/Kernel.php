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
            $episode = Episode::whereDownloaded(false)->whereProcessing(false)->orderBy('id', 'desc')->first();
            if ( !is_null($episode)){                
                $exitCode = Artisan::call('kiss:getepisode',['id' => $episode->id]);                
            }
        })->everyThirtyMinutes()
            ->timezone('America/Toronto')
            ->when(function () {
                return date('H') >= 2 && date('H') <= 7;
            });;

        // $schedule->command('inspire')
        //          ->hourly();
    }
}
