<?php

namespace App\Http\Controllers\Order;

use App\Order;
use App\Status;
use App\Mail\StatusChanged;
use Illuminate\Support\Facades\Mail;
use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;

class OrderStatusController extends ApiController
{
    // ----------------------------------------------------------------------------------------------------- //
    // ? - U P D A T E
    // ----------------------------------------------------------------------------------------------------- //
    public function update(Request $request, Order $order, Status $status)
    {
        $team = $order->userAssigned->teams()->first();
        $leader = $team->user_leader()->first();

        $value=\Config::get('globals.front_url');

        dd( $value );

        Mail::to($leader)->send(new StatusChanged($leader, $status, $order));

        $order->status_id = $status->id;
        $order->save();


        return $this->showMessage('Estatus cambiado con éxito');
    }
}
