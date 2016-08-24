<?php 
set_time_limit(0);
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\Remote\WebDriverCapabilityType;
use Facebook\WebDriver\WebDriverExpectedCondition;

function getPageSource($url, $spoof=true)
{
	$host = 'http://localhost:4444/wd/hub';
	$capabilities = array(WebDriverCapabilityType::BROWSER_NAME => 'chrome', WebDriverCapabilityType::JAVASCRIPT_ENABLED => true, WebDriverCapabilityType::PLATFORM => 'Windows');
    $driver = RemoteWebDriver::create($host, $capabilities, 60*1000, 60*000);	
    
    $driver->get($url);
    if ($spoof){    	
    	$driver->wait(120, 1000)->until(
    		WebDriverExpectedCondition::not(WebDriverExpectedCondition::titleIs('Please wait 5 seconds...'))
    		);
	}
    $source = $driver->getPageSource();
    /*$driver->quit();
    $driver->close();*/
    return $source;
}