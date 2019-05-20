<?php

use App\User;
use App\Order;
use App\Record;
use App\Product;
use App\Client;
use App\Contact;
use App\Team;
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
        Client::truncate();
        Order::truncate();
        Record::truncate();
        Product::truncate();
        Team::truncate();

        $cantidadUsuarios = 3;
        $cantidadClientes = 3;
        $cantidadContactos = 9;
        $cantidadOrdenes = 6;
        $cantidadRecords = 20;
        $cantidadProductos = 60;

        $cantidadTeams = 3;


        $super_admin = new User;
        $super_admin->name = 'Admin';
        $super_admin->email = 'admin@admin.com';
        $super_admin->password = bcrypt('123456789');
        $super_admin->admin = User::USUARIO_ADMINISTRADOR;
        $super_admin->remember_token = str_random(10);
        $super_admin->verified = User::USUARIO_VERIFICADO;
        $super_admin->verification_token = User::generarVerificationToken();
        $super_admin->role_id = 1;
        $super_admin->phone = '8123995671';
        $super_admin->birthdayDate = '1996-12-17';
        $super_admin->puesto = 'Jefe';
        $super_admin->address = 'Nuevo León, Monterrey';
        $super_admin->save();

        factory(User::class, $cantidadUsuarios)->create();
        factory(Client::class, $cantidadClientes)->create();
        factory(Contact::class, $cantidadContactos)->create();
        factory(Order::class, $cantidadOrdenes)->create();
        factory(Record::class, $cantidadRecords)->create();
        factory(Product::class, $cantidadProductos)->create();

        factory(Team::class, $cantidadTeams)->create()->each(
			function ($team) {
                $team->users_members()->attach($team->owner_id);

                $users = User::all()->random(mt_rand(1, 3))->pluck('id');
				$team->users_members()->attach($users);
			}
		);
    }
}
