<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGiftRatingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('gift_ratings', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('gift_id');
            $table->bigInteger('user_id');
            $table->decimal('rating', 2, 1);
            $table->integer('created_at')->nullable();
            $table->integer('updated_at')->nullable();
            $table->integer('deleted_at')->nullable();
        });

        Schema::table('gift_ratings', function (Blueprint $table) {
            $table->index('gift_id');
            $table->index('user_id');
            $table->index('rating');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('gift_ratings');
    }
}
