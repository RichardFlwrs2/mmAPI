<?php

namespace App\Http\Controllers\Order;

use App\Order;
use App\Status;
use App\Record;
use Carbon\Carbon;
use App\Mail\Requisicion;
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
        $this->allowedAdminAction();

        $order = Order::where('id', $id_order)->firstOrFail();
        $record = Record::where('id', $id_record)->firstOrFail();
        $contacts = $order->client->contacts;

        if ( !$record->pdf_file ) return $this->errorResponse('Ese registro no tiene un archivo pdf disponible', 400);

        // Solo guarda el Timer en el primer registro
        if ( $order->records()->count() === 1 ) {
            if ( !isset( $record->sended_at ) ) {
                $record->sended_at = Carbon::now();
                $record->save();
                $timeFirst  = strtotime($record->created_at);
                $timeSecond = strtotime($record->sended_at);
                $differenceInSeconds = $timeSecond - $timeFirst;

                $order->timer = $differenceInSeconds;
                $order->save();
            }
        }

        foreach ($contacts as $key => $contact) {
            if ( isset( $contact->email ) ) Mail::to($contact)->send(new Requisicion($record) );
        }

        return $this->showMessage('Requisición enviada con éxito');
    }

    // ----------------------------------------------------------------------------------------------------- //
    // ? - Sending Req
    // ----------------------------------------------------------------------------------------------------- //
    public function sendDataOrder(Request $request, $id_order, $id_record)
    {
        $this->allowedAdminAction();

        $order = Order::where('id', $id_order)->firstOrFail();
        $record = Record::where('id', $id_record)->firstOrFail();
        $user = auth()->user();

        // $order->status_id = $status->id;
        // $order->save();

        // Mail::to($leader)->send(new StatusChanged($leader, $status, $order));

        return $this->showMessage('Orden de venta enviada con éxito');
    }
}
