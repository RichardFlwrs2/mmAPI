<?php

use App\User;
use App\Order;
use App\Record;
use App\Product;
use App\Client;
use App\Contact;
use App\Team;
use App\Field;
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
        DB::table('team_user')->truncate();

        $cantidadUsuarios = 9;
        $cantidadClientes = 12;
        $cantidadContactos = $cantidadClientes * 3;
        $cantidadFields = $cantidadClientes + $cantidadContactos + $cantidadUsuarios;

        $cantidadOrdenes = 15;
        $cantidadRecords = 20;
        $cantidadProductos = 60;

        $cantidadTeams = 10;


        $this->createAdmin();

        factory(User::class, $cantidadUsuarios)->create();
        factory(Client::class, $cantidadClientes)->create()->each(
			function ($client) {
                factory(Field::class, mt_rand(0, 3))->create([
                    'client_id' => $client->id,
                ]);
			}
		);

        factory(Contact::class, $cantidadContactos)->create()->each(
			function ($contact) {
                factory(Field::class, mt_rand(0, 3))->create([
                    'contact_id' => $contact->id,
                ]);
			}
		);

        factory(Order::class, $cantidadOrdenes)->create()->each(
			function ($order) {
                $hasMoreData = false;
                if ( $order->status_id >= 4 ) $hasMoreData = true;
                factory(Record::class, 1)->create([
                    'order_id' => $order->id,
                    'numero_cotizacion' => $hasMoreData ? 10101 + $order->id  : null,
                    'monto_total' => $hasMoreData ? 11101 : null,
                ]);
			}
		);
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



    protected function createAdmin() {
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
    }
}
