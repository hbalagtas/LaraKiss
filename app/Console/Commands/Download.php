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
            $episode = Episode::where('url', $url)->first();

            $this->info("Downloading source...");
            $host = 'http://localhost:4444/wd/hub';

            $capabilities = array(WebDriverCapabilityType::BROWSER_NAME => 'chrome', WebDriverCapabilityType::JAVASCRIPT_ENABLED => true, WebDriverCapabilityType::PLATFORM => 'Linux');
            try {
                 \Log::info("Starting webdriver...");
                $driver = RemoteWebDriver::create($host, $capabilities, 70000, 70000);
            } catch (\Exception $e){
                $this->info("Failed to initialize webdriver");
                \Log::info("Failed to initialize webdriver");
            }
            
            $driver->get("http://".parse_url($url)['host']);

            $driver->get($url);
                  
            $driver->wait(240, 1000)->until(
                WebDriverExpectedCondition::not(WebDriverExpectedCondition::titleIs('Please wait 5 seconds...'))
                );
            \Log::info("Getting page source...");
            $source = $driver->getPageSource();

            // check for reCaptcha
            // recaptcha-checkbox-spinner
            #driver.findElement(By.className("AddContentBTN")).click();
            /*if ( $reCaptcha = $driver->findElement(WebDriverBy::className("recaptcha-checkbox-spinner")) ) {
                $driver->findElement(WebDriverBy::className("recaptcha-checkbox-spinner")).click();
                sleep(10);
                $driver->findElement(WebDriverBy::className("aButton")).click();
                \Log::info("Abort reCaptcha found!");
                return false;
            } else {
                \Log::info("reCaptcha not found");
            }*/

            if ( $source ){
                \Log::info("Page source retrieved!");
                if ( $episode ){
                    $episode->processing = true;                    
                    $episode->source = $source;
                    $episode->save();
                }
                $element = $driver->findElement(WebDriverBy::id("my_video_1_html5_api"));
                $link = $element->getAttribute('src');
                $driver->quit();
                \Log::info($link);
                if ( empty($link) ) {    
                    \Log::info("FAILED to get link.");
                    if ( $episode ){
                        $episode->processing = false;
                        $episode->save();    
                    }
                    return false;
                } else {
                    \Log::info("Found the missing link...");    
                    if ( $episode ){
                        $episode->processing = true;
                        $episode->save();    
                    }
                }
            } else {
                \Log::info("Failed to get source");
            }
            
            

            /*$driver->get($link);
            $link = $driver->getCurrentURL();*/
            //$this->info($link);
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
            
            $this->info("Downloading file $outputfile");
            \Log::info("Downloading file $outputfile");
            
            #$this->info($cmd);
            exec($cmd, $output, $ret_var);            
            
            if ( file_exists($outputfile) ) {               
                if ( $episode ) {
                     \Log::info("OK: $outputfile");
                    $episode->downloaded = true;
                    $episode->processing = false;
                    $episode->save();
                }                
                return true;
            } else {                
                if ( $episode ) {
                    \Log::info("FAILED: $outputfile");
                    $episode->downloaded = false;
                    $episode->processing = false;
                    $episode->save();
                } 
                return false;
            }
        } catch (\Exception $e) {     
            if ( $episode ) {
                \Log::info("WebDriver Error");
                $episode->downloaded = false;
                $episode->processing = false;
                $episode->save();
            }            

            if ( $reCaptcha = $driver->findElement(WebDriverBy::id("formVerify")) ) {
                \Log::info("Switching to reCaptcha frame!");
                
                /*$driver->findElement(WebDriverBy::className("recaptcha-checkbox-spinner")).click();
                sleep(10);
                $driver->findElement(WebDriverBy::className("aButton")).click();*/
                $driver->switchTo(WebDriverBy::name("undefined"));
                //$driver->switchTo()->frame(WebDriverBy::id("^=oauth2relay"));
                \Log::info("trying to click on reCaptcha");
                #$driver->findElement(WebDriverBy::id("recaptcha-anchor")).click();
                return false;
            } else {
                \Log::info("reCaptcha not found");
            }
                  
            $this->info($e->getMessage());
        }
    }
}
