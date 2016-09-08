<?php

use Illuminate\Database\Seeder;
use LaraKiss\Config;

class ConfigSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $c = new Config;
        $c->setting = 'pause_downloads';
        $c->value = 0;
        $c->save();        
    }
}
