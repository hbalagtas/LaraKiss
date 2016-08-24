<?php

namespace LaraKiss\Console\Commands;

use Exception;
use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\Remote\WebDriverCapabilityType;
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverExpectedCondition;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use LaraKiss\Episode;
use Yangqi\Htmldom\Htmldom;

class Download extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'kiss:download {url}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Download file from url';

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
            $url = $this->argument('url');
            
            if ( $episode = Episode::where('url', $url)->first() ){
                $episode->processing = true;
                $episode->save();    
            }
            $this->info("Downloading source...");
            $host = 'http://localhost:4444/wd/hub';

            $capabilities = array(WebDriverCapabilityType::BROWSER_NAME => 'chrome', WebDriverCapabilityType::JAVASCRIPT_ENABLED => true, WebDriverCapabilityType::PLATFORM => 'Windows');
                $driver = RemoteWebDriver::create($host, $capabilities, 70000, 70000);       
            
            $driver->get("http://".parse_url($url)['host']);

            $driver->get($url);
                  
            $driver->wait(240, 1000)->until(
                WebDriverExpectedCondition::not(WebDriverExpectedCondition::titleIs('Please wait 5 seconds...'))
                );
            
            $source = $driver->getPageSource();

            $element = $driver->findElement(WebDriverBy::id("my_video_1_html5_api"));
            $link = $element->getAttribute('src');
            /*$driver->get($link);
            $link = $driver->getCurrentURL();*/
            $this->info($link);
            /*$html = new Htmldom($source);
            $link = $html->getElementById("my_video_1_html5_api")->src;*/

            $dir = env('DOWNLOAD_FOLDER', public_path()) . "/" . pathinfo(pathinfo($url)['dirname'])["basename"];
            if ( !is_dir($dir) ){
                mkdir($dir);
            }
            $filename = explode("?",pathinfo($url)['filename'])[0] . ".avi";
            $outputfile = $dir . '/' . $filename;
            
            #$cmd = "wget -c \"$link\" -O \"$outputfile\" > /dev/null &";
            $cmd = "wget -c \"$link\" -O \"$outputfile\" ";
            #$cmd = "curl -O -J -L \"{$link}\" -o {$outputfile}"; 
            $this->info("Downloading file...");
            $this->info($cmd);
            exec($cmd, $output, $ret_var);
            $this->info("Downloading in the background: $outputfile}");
            
            if ( $ret_var == 0 ){
                if ( $episode ) {
                    $episode->downloaded = true;
                    $episode->processing = false;
                    $episode->save();
                }
                return true;
            } else {
                if ( $episode ) {
                    $episode->downloaded = false;
                    $episode->processing = false;
                    $episode->save();
                }
                return false;
            }
        } catch (\Exception $e) {
            if ( $episode ) {
                $episode->downloaded = false;
                $episode->processing = false;
                $episode->save();
            }            
            $this->info($e->getMessage());
        }
    }
}
