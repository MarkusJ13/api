<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateNotificationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('notifications', function (Blueprint $table) {// new answer, upvote, 
            $table->increments('id');//add qid also
            $table->integer('uid');
            $table->integer('did');
            $table->integer('seen');//can make it bool
            $table->integer('falana_dhikada');//someone who is responsible for notification
            $table->integer('type');//1 = new answer, 2 = upvote, 3=downvote
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
        Schema::dropIfExists('notifications');
    }
}