<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */
use App\Order;
use App\Product;
use App\Record;
use App\Role;
use App\User;
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
        'password' => $password ?: $password = bcrypt('secret'),
        'remember_token' => str_random(10),
        'verified' => $verificado = $faker->randomElement([User::USUARIO_VERIFICADO, User::USUARIO_NO_VERIFICADO]),
        'verification_token' => $verificado == User::USUARIO_VERIFICADO ? null : User::generarVerificationToken(),
        'admin' => $faker->randomElement([User::USUARIO_ADMINISTRADOR, User::USUARIO_REGULAR]),
        'role_id' => $faker->randomElement([
            Role::COTIZADOR,
            Role::VENDEDOR,
        ]),
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

    $order = Order::all()->random();
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