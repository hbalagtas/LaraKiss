<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEpisodesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('episodes', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->index();
            $table->integer('show_id')->unsigned()->index();
            $table->foreign('show_id')->references('id')->on('shows')->onDelete('cascade');
            $table->string('url');
            $table->text('source');            
            $table->string('filename');
            $table->boolean('watched');
            $table->boolean('downloaded');
            $table->boolean('processing');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('episodes');
    }
}
