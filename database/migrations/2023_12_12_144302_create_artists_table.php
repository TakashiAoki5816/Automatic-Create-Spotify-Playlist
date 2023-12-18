<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('artists', function (Blueprint $table) {
            $table->id()->comment('アーティストID');
            $table->string('name', 255)->comment('アーティスト名');
            $table->json('genres')->comment('Spotify APIで設定されているアーティストジャンル');
            $table->string('artist_id', 255)->comment('Spotify APIで設定されているアーティストID');
            $table->timestamps();
            $table->softDeletes();
            $table->comment('アーティスト');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('artists');
    }
};
