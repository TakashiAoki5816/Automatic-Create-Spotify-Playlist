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
        Schema::create('genre_categories', function (Blueprint $table) {
            $table->id()->comment('ジャンルカテゴリーID');
            $table->string('name', 255)->comment('ジャンルカテゴリー名');
            $table->integer('view_order')->comment('表示順');
            $table->timestamps();
            $table->softDeletes();
            $table->comment('ジャンルカテゴリー');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('genre_categories');
    }
};
