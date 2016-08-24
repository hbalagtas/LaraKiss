<?php

use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\Remote\WebDriverCapabilityType;
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverExpectedCondition;

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

Route::auth();

Route::get('/home', 'HomeController@index');
Route::resource('show', 'ShowController');
Route::get('/', function () {
	return view('welcome');
});

Route::get('/transaction-test', function(){
	DB::beginTransaction();
	$episode = Episode::whereDownloaded(false)->whereProcessing(false)->first();
	$episode->processing = true;
	$episode->save();
	echo "<p>{$episode->name} - {$episode->processing} </p>";
	$episode = Episode::whereDownloaded(false)->whereProcessing(false)->first();
	$episode->processing = true;
	$episode->save();
	echo "<p>{$episode->name} - {$episode->processing} </p>";
	DB::commit();
});

Route::get('/test', function () {
	$url = "http://kissanime.to/Anime/Masou-Gakuen-HxH/Episode-001-Uncensored?id=127482";
	$host = 'http://localhost:4444/wd/hub';
	$capabilities = array(WebDriverCapabilityType::BROWSER_NAME => 'chrome', WebDriverCapabilityType::JAVASCRIPT_ENABLED => true, WebDriverCapabilityType::PLATFORM => 'Windows 10');
    $driver = RemoteWebDriver::create($host, $capabilities, 5000);	
    $driver->manage()->timeouts()->implicitlyWait(10);

    $driver->get($url);
    sleep(5);
    $driver->get($url);
    $source = $driver->getPageSource();
    file_put_contents('/tmp/kiss.txt', $source);
    $element = $driver->findElement(WebDriverBy::id("my_video_1_html5_api"));
    $link = $element->getAttribute('src');
    $driver->quit();
    $dir = "/tmp/" . pathinfo(pathinfo($url)['dirname']);
    if ( !is_dir($dir) ){
    	mkdir($dir);
    }
    $filename = explode("?",pathinfo($url)['filename'])[0];
    $outputfile = $dir . '/' . $filename;
    $cmd = "wget -q \"$link\" -O $outputfile";
    exec($cmd);

    echo "Video Link: {$link}";
});

Route::get('episodes', function() {

	$url = "http://kissasian.com/Drama/Cinderella-and-the-Four-Knights";

	$host = 'http://localhost:4444/wd/hub';
	$capabilities = array(WebDriverCapabilityType::BROWSER_NAME => 'chrome', WebDriverCapabilityType::JAVASCRIPT_ENABLED => true, WebDriverCapabilityType::PLATFORM => 'Windows 10');
    $driver = RemoteWebDriver::create($host, $capabilities, 5000);	
    $driver->manage()->timeouts()->implicitlyWait(10);
    $driver->get($url);
    sleep(5);
    $driver->get($url);
    $source = $driver->getPageSource();
	
	$show_name = pathinfo($url)['basename'];
	if ( $show = Show::where(['name' => $show_name])->first() ){

	} else {
		$show = new Show;
		$show->name = $show_name;
		$show->folder = str_slug($show->name);	
		$show->source = $source;
		$show->watched = false;
		$show->save();
	}

	$domain = "http://" . parse_url($url, PHP_URL_HOST);

	$html = new Htmldom($source);
	echo "<ul>";
	foreach($html->find('td > a') as $element) {
		$ep_link = $domain.$element->href;
		$ep_name = explode("/", parse_url($ep_link)["path"])[3];
		if ( $episode = Episode::where(['name'=> $ep_name, 'show_id' => $show->id])->first()) {

		} else {
			$episode = new Episode;
			$episode->name = $ep_name;
			$episode->url = $ep_link;
			$episode->show_id = $show->id;
			$episode->watched = false;
			$episode->downloaded = false;
			$episode->save();
		}		

		echo '<li><a href="'.$ep_link.'">'.$episode->name.'</a></li>';
	}
	echo "</ul>";
});

