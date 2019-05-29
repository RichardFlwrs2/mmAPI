<?php

namespace App\Http\Controllers\Order;

use App\Order;
use App\Status;
use App\Record;
use Carbon\Carbon;
use App\Mail\StatusChanged;
use Illuminate\Support\Facades\Mail;
use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;

class OrderSendController extends ApiController
{
    // ----------------------------------------------------------------------------------------------------- //
    // ? - Sending Req
    // ----------------------------------------------------------------------------------------------------- //
    public function sendDataRecord(Request $request, $id_order, $id_record)
    {
        $user = auth()->user();
        if ( !$user->esAdmin() ) return $this->errorResponse('No tienes autorización para hacer esto, ponte en contacto con tu administrador', 400);

        $order = Order::where('id', $id_order)->firstOrFail();
        $record = Record::where('id', $id_record)->firstOrFail();

        // Solo guarda el Timer en el primer registro
        if ( $order->records()->count() === 1 ) {

            $record->sended_at = Carbon::now();
            $record->save();
            $timeFirst  = strtotime($record->created_at);
            $timeSecond = strtotime($record->sended_at);
            $differenceInSeconds = $timeSecond - $timeFirst;

            $order->timer = $differenceInSeconds;
            $order->save();

        }



        return $this->showMessage('Requisición enviada con éxito');
    }

    // ----------------------------------------------------------------------------------------------------- //
    // ? - Sending Req
    // ----------------------------------------------------------------------------------------------------- //
    public function sendDataOrder(Request $request, $id_order, $id_record)
    {
        $order = Order::where('id', $id_order)->firstOrFail();
        $record = Record::where('id', $id_record)->firstOrFail();
        $user = auth()->user();

        // $order->status_id = $status->id;
        // $order->save();

        // Mail::to($leader)->send(new StatusChanged($leader, $status, $order));

        return $this->showMessage('Orden de venta enviada con éxito');
    }
}
