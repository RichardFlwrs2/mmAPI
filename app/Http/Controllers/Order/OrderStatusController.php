<?php

namespace App\Http\Controllers\Order;

use App\Order;
use App\Status;
use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;

class OrderStatusController extends ApiController
{
    // ----------------------------------------------------------------------------------------------------- //
    // ? - U P D A T E
    // ----------------------------------------------------------------------------------------------------- //
    public function update(Request $request, Order $order, Status $status)
    {
        $order->status_id = $status->id;
        $order->save();

        return $this->showMessage('Estatus cambiado con éxito');
    }
}
