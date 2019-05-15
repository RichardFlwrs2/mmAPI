<?php

use App\User;
use App\Order;
use App\Record;
use App\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS = 0');

        User::truncate();
        Order::truncate();
        Record::truncate();
        Product::truncate();

        $cantidadUsuarios = 3;
        $cantidadOrdenes = 6;
        $cantidadRecords = 20;
        $cantidadProductos = 60;


        $super_admin = new User;
        $super_admin->name = 'Admin';
        $super_admin->email = 'admin@admin.com';
        $super_admin->password = bcrypt('123456789');
        $super_admin->remember_token = str_random(10);
        $super_admin->verified = User::USUARIO_VERIFICADO;
        $super_admin->verification_token = User::generarVerificationToken();
        $super_admin->role_id = 1;
        $super_admin->save();

        factory(User::class, $cantidadUsuarios)->create();
        factory(Order::class, $cantidadOrdenes)->create();
        factory(Record::class, $cantidadRecords)->create();
        factory(Product::class, $cantidadProductos)->create();
    }
}
