<?php

use App\User;
use App\Order;
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

        $cantidadUsuarios = 10;
        $cantidadOrdenes = 20;


        $super_admin = new User;
        $super_admin->name = 'Admin';
        $super_admin->email = 'admin@admin.com';
        $super_admin->password = bcrypt('123456789');
        $super_admin->role_id = 1;
        $super_admin->save();

        factory(User::class, $cantidadUsuarios)->create();
        factory(Order::class, $cantidadOrdenes)->create();
    }
}
