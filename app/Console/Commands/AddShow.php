<?php

namespace LaraKiss\Console\Commands;

use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\Remote\WebDriverCapabilityType;
use Illuminate\Console\Command;
use LaraKiss\Episode;
use LaraKiss\Show;
use Yangqi\Htmldom\Htmldom;

class AddShow extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'kiss:addshow {url}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Add a show and get all episodes';

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
        $url = $this->argument('url');
        $show_name = pathinfo($url)['basename'];
        $show = Show::where(['name' => $show_name])->first();
        if ( is_null($show) ){
            
            $source = getPageSource($url);
            $html = new Htmldom($source);
            if ( $source ){
                $show = new Show;
                $show->name = $show_name;
                $show->folder = str_slug($show->name);  
                $show->source = $source;
                $show->url = $url;
                //$show->cover = $html->find('.barContent img', 2)->src;
                //$show->summary = $html->find('#leftside div p', 6)->plaintext;
                $show->watched = false;
                $show->save();

                $domain = "http://" . parse_url($url, PHP_URL_HOST);
                $html = new Htmldom($source);

                foreach($html->find('td > a') as $element) {
                    $ep_link = $domain.$element->href;
                    $ep_name = explode("/", parse_url($ep_link)["path"])[3];
                    if ( $episode = Episode::where(['name'=> $ep_name, 'show_id' => $show->id])->first()) {

                    } else {
                        $episode = new Episode;
                        $episode->name = str_replace('Episode-', '', $ep_name);
                        $episode->url = $ep_link;
                        // source has to be fresh for link to work
                        #$episode->source = getPageSource($ep_link);
                        $episode->show_id = $show->id;
                        $episode->watched = false;
                        $episode->downloaded = false;
                        $episode->save();
                    }       
                    $this->info("Adding episode " . $episode->name);
                }
            } else {
                $this->info('Failed to fetch source');
            }            
        } else {
            $this->info('Show is already in database');
        }       
    }
}
