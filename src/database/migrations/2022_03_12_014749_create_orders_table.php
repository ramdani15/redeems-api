<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('user_id');
            $table->string('attachable_type', 100)->nullable();
            $table->bigInteger('attachable_id')->nullable();
            $table->tinyInteger('qty')->default(1);
            $table->integer('point')->default(0);
            $table->enum('status', ['cart', 'ordered']);
            $table->integer('created_at')->nullable();
            $table->integer('updated_at')->nullable();
            $table->integer('deleted_at')->nullable();
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->index('user_id');
            $table->index(['attachable_type', 'attachable_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('orders');
    }
}
