<?php

namespace LaraKiss\Console\Commands;

use Exception;
use Facebook\WebDriver\Chrome\ChromeOptions;
use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\Remote\WebDriverCapabilityType;
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverExpectedCondition;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use LaraKiss\Config as LaraKissConfig;
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

            $options = new ChromeOptions();
            $options->addArguments(['--user-data-dir=/home/vagrant/.config/google-chrome/Default']);

            $caps = DesiredCapabilities::chrome();
            $caps->setCapability(ChromeOptions::CAPABILITY, $options);
            $caps->setCapability(WebDriverCapabilityType::APPLICATION_CACHE_ENABLED, true);
            $caps->setCapability(WebDriverCapabilityType::JAVASCRIPT_ENABLED, true);
            $caps->setCapability(WebDriverCapabilityType::PLATFORM, 'Linux');
            $caps->setCapability(WebDriverCapabilityType::DATABASE_ENABLED, true);
            $caps->setCapability(WebDriverCapabilityType::APPLICATION_CACHE_ENABLED, true);
            $caps->setCapability(WebDriverCapabilityType::TAKES_SCREENSHOT, false);
            
            /*$capabilities = array(WebDriverCapabilityType::APPLICATION_CACHE_ENABLED => true, WebDriverCapabilityType::BROWSER_NAME => 'chrome', WebDriverCapabilityType::JAVASCRIPT_ENABLED => true, WebDriverCapabilityType::PLATFORM => 'Linux');*/
            $capabilities = array(WebDriverCapabilityType::APPLICATION_CACHE_ENABLED => true, WebDriverCapabilityType::BROWSER_NAME => 'chrome', WebDriverCapabilityType::JAVASCRIPT_ENABLED => true, WebDriverCapabilityType::PLATFORM => 'Linux', ChromeOptions::CAPABILITY => $options);
            if ( rand(0,1) ){
                $drivercaps = $caps;
            } else {
                $drivercaps = $capabilities;
            }
            try {
                 \Log::info("Starting webdriver...");                
                $driver = RemoteWebDriver::create($host, $drivercaps, 70000, 70000);
                \Log::info("Webdriver successfully initialized..."); 
            } catch (\Exception $e){                
                $this->info("Failed to initialize webdriver");
                \Log::info("Failed to initialize webdriver");
                $driver->quit();                
            }
            
            try {
                $driver->get("http://".parse_url($url)['host']);
                \Log::info("Getting url: {$url}"); 
                $driver->get($url);
                $title = $driver->getTitle();
                \Log::info( "Page title is {$title}");    
            } catch (\Exception $e){
                \Log::info("Failed to get url: {$url}"); 
            }
            
            #try {
                \Log::info("Waiting for countdown page to complete...");      
                $driver->wait(240, 1000)->until(
                    WebDriverExpectedCondition::not(WebDriverExpectedCondition::titleIs('Please wait 5 seconds...'))
                    );
            /*} catch (\Exception $e){
                $title = $driver->getTitle();
                \Log::info( "Page title is {$title}");
                $this->info("Failed waiting for page to countdown");
                \Log::info("Failed waiting for page to countdown");
                $source = $driver->getPageSource(); 
                $episode->source = $source;
                $episode->save();
                $driver->quit();
            }  */          

            #try {
                \Log::info("Getting page source...");
                $source = $driver->getPageSource();    
            /*} catch( \Exception $e){
                $this->info("Failed to retrieve page source");
                \Log::info("Failed to retrieve page source");
                $driver->quit();
            }*/
            

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
                #\Log::info($link);
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

            // pause for an hour
            $pause_downloads = LaraKissConfig::where('setting', 'pause_downloads')->first(); 
            $pause_downloads->value = true;
            $pause_downloads->save();            
                  
            $this->info($e->getMessage());
            $driver->quit();
        }
    }
}
