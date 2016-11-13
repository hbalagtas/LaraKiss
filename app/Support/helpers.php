<?php 
set_time_limit(0);
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\Remote\WebDriverCapabilityType;
use Facebook\WebDriver\WebDriverExpectedCondition;

function getPageSource($url, $spoof=true)
{
    try {
        $host = env('WD_HOST','http://localhost:4444/wd/hub');
        $capabilities = array(WebDriverCapabilityType::BROWSER_NAME => 'firefox', WebDriverCapabilityType::JAVASCRIPT_ENABLED => true, WebDriverCapabilityType::PLATFORM => 'Windows');
        $driver = RemoteWebDriver::create($host, $capabilities, 60*1000, 60*000);   
        
        $driver->get($url);
        if ($spoof){        
            $driver->wait(120, 1000)->until(
                WebDriverExpectedCondition::not(WebDriverExpectedCondition::titleIs('Please wait 5 seconds...'))
                );
        }
        $source = $driver->getPageSource();
        $driver->quit();
        return $source;
    } catch( \Exception $e ){
        \Log::info('Failed to get source');
        return false;
    }
}

function getDownloadStatus()
{
    return LaraKiss\Config::where('setting', 'pause_downloads')->first()->value;
}