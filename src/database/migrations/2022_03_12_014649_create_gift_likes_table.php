<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGiftLikesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('gift_likes', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('gift_id');
            $table->bigInteger('user_id');
            $table->integer('created_at')->nullable();
            $table->integer('updated_at')->nullable();
        });

        Schema::table('gift_likes', function (Blueprint $table) {
            $table->index('gift_id');
            $table->index('user_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('gift_likes');
    }
}
