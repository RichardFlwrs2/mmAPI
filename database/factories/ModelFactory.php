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
use Carbon\Carbon;
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

    $status = $faker->randomElement([1, 2, 3, 4, 5]);

    $hasMoreData = false;
    if ( $status >= 4 ) $hasMoreData = true;

    return [
        'created_by' => User::all()->random()->id,
        'user_id' => User::all()->random()->id,
        'status_id' => $status,
        'client_id' => $faker->randomElement([1, 2, 3]),
        'folio' => str_random(5),
        'numero_orden' => $hasMoreData ? $faker->unique()->numberBetween(1, 999999999) : null,
        'monto_total' => $hasMoreData ? $faker->randomFloat($nbMaxDecimals = 2, $min = 0, $max = 999999999) : null,
    ];
});

$factory->afterCreating(Order::class, function ($order, $faker) {

    $hasMoreData = false;
    if ( $order->status_id >= 4 ) {
        $hasMoreData = true;
        $now = $faker->dateTimeBetween($startDate = 'now', $endDate = 'now', $timezone = 'America/Monterrey');
        $max = Carbon::now()->addHour(24);
        $date = $faker->dateTimeBetween($startDate = 'now', $endDate = $max, $timezone = 'America/Monterrey');
    }

    $record = new Record;
    $record->order_id = $order->id;
    $record->numero_cotizacion = $hasMoreData ? $faker->unique()->numberBetween(1, 999999999) : null;
    $record->monto_total = $hasMoreData ? $faker->numberBetween(1, 999999999) : null;
    $record->sended_at = $hasMoreData ? $date : null;
    $record->temporal = '0';
    $record->save();

    if ( $hasMoreData ) {

        $timeFirst  = strtotime($record->created_at);
        $timeSecond = strtotime($record->sended_at);
        $differenceInSeconds = $timeSecond - $timeFirst;

        $order->timer = $differenceInSeconds;
        $order->save();

    }

});

$factory->define(Record::class, function (Faker $faker) {

    $order = Record::where('sended_at', '!=', null)->get()->random()->order;

    $hasMoreData = false;
    if ( $order->status_id >= 4 ) {
        $hasMoreData = true;
        $max = Carbon::now()->addHour(24);
        $date = $faker->dateTimeBetween($startDate = 'now', $endDate = $max, $timezone = 'America/Monterrey');
    }

    return [
        'order_id' => $order->id,
        'numero_cotizacion' => $hasMoreData ? $faker->unique()->numberBetween(1, 999999999) : null,
        'monto_total' => $hasMoreData ? $faker->randomFloat($nbMaxDecimals = 2, $min = 0, $max = 999999999) : null,
        'sended_at' => $hasMoreData ? $date : null,
        'temporal' => '0',
    ];
});

$factory->afterCreating(Record::class, function ($record, $faker) {
    $order = $record->order()->first();

    if ( isset($record->sended_at) ) {

        $timeFirst  = strtotime($record->created_at);
        $timeSecond = strtotime($record->sended_at);
        $differenceInSeconds = $timeSecond - $timeFirst;

        $order->timer = $differenceInSeconds;
        $order->save();

    }

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

    $admins = User::where('admin', User::USUARIO_ADMINISTRADOR )->get();

    return [
        'name' => $faker->word,
        'owner_id' => $admins->random()->id,
    ];

});


$factory->define(Field::class, function (Faker $faker) {

    return [
        'name' => $faker->word,
        'data' => $faker->word,
        'client_id' => null,
        'user_id' => null,
        'contact_id' => null,
    ];

});
