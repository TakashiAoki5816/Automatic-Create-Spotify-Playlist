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
        Schema::create('genres', function (Blueprint $table) {
            $table->id()->comment('ジャンルID');
            $table->string('name', 255)->comment('ジャンル名');
            $table->integer('genre_category_id')->comment('ジャンルカテゴリーID');
            $table->integer('view_order')->comment('表示順');
            $table->timestamps();
            $table->softDeletes();
            $table->comment('ジャンル');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('genres');
    }
};
