<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */
use App\Order;
use App\Product;
use App\Record;
use App\Team;
use App\Role;
use App\User;
use App\Client;
use App\Contact;
use App\Field;
use Faker\Generator as Faker;

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| This directory should contain each of the model factory definitions for
| your application. Factories provide a convenient way to generate new
| model instances for testing / seeding your application's database.
|
 */

$factory->define(User::class, function (Faker $faker) {
    static $password;

    return [
        'name' => $faker->name,
        'email' => $faker->unique()->safeEmail,
        'password' => $password ?: $password = bcrypt('123456789'),
        'remember_token' => str_random(10),
        'verified' => $verificado = $faker->randomElement([User::USUARIO_VERIFICADO, User::USUARIO_NO_VERIFICADO]),
        'verification_token' => $verificado == User::USUARIO_VERIFICADO ? null : User::generarVerificationToken(),
        'admin' => User::USUARIO_REGULAR,
        'role_id' => $faker->randomElement([
            Role::COTIZADOR,
            Role::VENDEDOR,
            ]),
        'phone' => $faker->numerify('##########'),
        'birthdayDate' => $faker->date($format = 'Y-m-d', $max = 'now'),
        'puesto' => $faker->word,
        'address' => $faker->address,
    ];
});


$factory->define(Client::class, function (Faker $faker) {

    return [
        'name' => $faker->name,
        'created_by' => 1,
        'phone' => $faker->numerify('##########'),
        'address' => $faker->address,
        'ciudad' => $faker->city,
        'estado' => $faker->state,
        'pais' => $faker->country,
        'codigo_postal' => $faker->postcode,
        'rfc' => strtoupper( $faker->shuffle(  $faker->bothify( '????????#####' ))),
    ];
});

$factory->define(Contact::class, function (Faker $faker) {

    return [
        'name' => $faker->name,
        'client_id' => Client::all()->random()->id,
        'area' => $faker->word,
        'puesto' => $faker->word,
        'email' => $faker->unique()->safeEmail,
        'phone' => $faker->numerify('##########'),
    ];
});


$factory->define(Order::class, function (Faker $faker) {

    return [
        'created_by' => User::all()->random()->id,
        'user_id' => User::all()->random()->id,
        'status_id' => $faker->randomElement([1, 2, 3, 4, 5]),
        'client_id' => $faker->randomElement([1, 2, 3]),
        'folio' => str_random(5),
        'numero_orden' => $faker->unique()->numberBetween(1, 999999999),
        'monto_total' => $faker->numberBetween(1, 999999999),
    ];
});

$factory->define(Record::class, function (Faker $faker) {

    return [
        'order_id' => Order::all()->random()->id,
        'numero_cotizacion' => $faker->unique()->numberBetween(1, 999999999),
        'monto_total' => $faker->numberBetween(1, 999999999),
        'temporal' => '0',
    ];
});

$factory->define(Product::class, function (Faker $faker) {

    $tieneCostos = $faker->randomElement([true, false]);

    $costoU = $faker->randomFloat(2, 1, 99999);
    $quantity = $faker->numberBetween(1, 10);
    $costoT = $quantity * $costoU;

    return [
        'record_id' => Record::all()->random()->id,
        'quantity' => $quantity,
        'brand' => $faker->numberBetween(1, 99999),
        'model_number' => $faker->numberBetween(1, 99999),
        'serial_number' => $faker->numberBetween(1, 99999),
        'details' => $faker->paragraph(1),
        'description' => $faker->paragraph(1),
        'condition_id' => $faker->randomElement([1, 2, 3]),
        'type_id' => $faker->randomElement([4, 5, 6, 7]),
        'costo_u' => $tieneCostos ? $faker->randomElement([$costoU, 5]) : null,
        'costo_t' => $tieneCostos ? $costoT : null,
    ];
});

$factory->define(Team::class, function (Faker $faker) {

    $admins = User::where('admin', 'true' )->get();

    return [
        'name' => $faker->word,
        'owner_id' => $admins->random()->id,
    ];

});


$factory->define(Field::class, function (Faker $faker) {

    $client_id = Client::all()->random()->id;
    $user_id = User::all()->random()->id;
    $contact_id = Contact::all()->random()->id;

    return [
        'name' => $faker->word,
        'data' => $faker->word,
        'client_id' => $attempt1 = $faker->randomElement([$client_id, null]),
        'user_id' => $attempt2 = $attempt1 != null ? null : $faker->randomElement([$user_id, null]),
        'contact_id' => $attempt2 == null && $attempt1 == null ? $contact_id : null,
    ];

});
