<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('types', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('description');
        });

        // ---------------------------------
        // - Tipo de Producto
        // ---------------------------------
        DB::table('types')->insert([
            ['name' => 'usado', 'description' => 'Producto usado'],
            ['name' => 'nuevo', 'description' => 'Producto nuevo'],
            ['name' => 'reconstruido', 'description' => 'Producto reconstruido'],
        ]);

        // ---------------------------------
        // - Tipo de Req
        // ---------------------------------

        DB::table('types')->insert([
            ['name' => 'normal', 'description' => 'Tipo de requisición: normal'],
            ['name' => 'urgente', 'description' => 'Tipo de requisición: urgente'],
            ['name' => 'critico', 'description' => 'Tipo de requisición: critico'],
            ['name' => 'almacen', 'description' => 'Tipo de requisición: almacen'],
        ]);

        // ---------------------------------
        // - Archivos
        // ---------------------------------
        DB::table('types')->insert([
            ['name' => 'avatar', 'description' => 'Archivo tipo: avatar'],
            ['name' => 'image', 'description' => 'Archivo tipo: image'],
            ['name' => 'pdf', 'description' => 'Archivo tipo: pdf'],
        ]);

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('types');
    }
}
