<?php

use App\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password');
            $table->rememberToken();
            $table->string('verification_token')->nullable();
            $table->integer('role_id')->unsigned();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('role_id')->references('id')->on('roles');
        });

        $super_admin = new User;
        $super_admin->name = 'Admin';
        $super_admin->email = 'admin@admin.com';
        $super_admin->password = '123456789';
        $super_admin->role_id = 1;
        $super_admin->save();

        $cotizador = new User;
        $cotizador->name = 'Cotizador';
        $cotizador->email = 'cotizador@test.com';
        $cotizador->password = '123456789';
        $cotizador->role_id = 3;
        $cotizador->save();

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
