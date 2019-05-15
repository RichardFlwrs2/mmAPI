<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('record_id')->unsigned();
            $table->integer('quantity')->unsigned();
            $table->integer('brand')->unsigned();
            $table->integer('model_number')->unsigned();
            $table->integer('serial_number')->unsigned();
            $table->string('details');
            $table->string('description');
            $table->integer('type_id')->unsigned();
            $table->integer('condition_id')->unsigned();
            $table->integer('costo_u')->unsigned()->nullable();
            $table->integer('costo_t')->unsigned()->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('record_id')->references('id')->on('records');
            $table->foreign('type_id')->references('id')->on('types');
            $table->foreign('condition_id')->references('id')->on('types');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('products');
    }
}
