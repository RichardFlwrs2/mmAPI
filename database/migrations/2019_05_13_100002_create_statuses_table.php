<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStatusesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('statuses', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('description');
        });

        DB::table('statuses')->insert([
            ['name' => 'nueva', 'description' => 'Requisición nueva'],
            ['name' => 'en proceso', 'description' => 'Requisición en proceso'],
            ['name' => 'precotizada', 'description' => 'Requisición pre-cotizada'],
            ['name' => 'enviada', 'description' => 'Requisición enviada'],
            ['name' => 'aprobada', 'description' => 'Requisición aprobada'],
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('statuses');
    }
}
