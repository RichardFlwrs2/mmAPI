<?php

namespace App\Http\Controllers\Order;

use App\Order;
use App\Record;
use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;

class OrderRecordController extends ApiController
{
    public function __construct()
    {
        parent::__construct();
        $this->middleware('can:view,order' , ['except' => ['saveDataRecord']]);
    }

    // ----------------------------------------------------------------------------------------------------- //
    // ? - I N D E X
    // ----------------------------------------------------------------------------------------------------- //
    public function index( Order $order )
    {
        $records = $order->records;
        return $this->showAll($records);
    }



    // ----------------------------------------------------------------------------------------------------- //
    // ? - S H O W
    // ----------------------------------------------------------------------------------------------------- //
    public function show(Order $order, Record $record)
    {
        $orderData = Order::with([
            'records' => function ($query) use($record) {
                $query->where('id', '=', $record->id)->with(['products']);
            },
            'status',
            'client'
        ])
        ->where('id', $order->id)
        ->firstOrFail();

        return $this->showOne($orderData);
    }

    // ----------------------------------------------------------------------------------------------------- //
    // ? - SAVE BEFORE SENDING DATA BY ADMIN
    // ----------------------------------------------------------------------------------------------------- //
    public function saveDataRecord(Request $request, $id_order, $id_record)
    {
        $order = Order::where('id', $id_order)->firstOrFail();
        $record = Record::where('id', $id_record)->firstOrFail();
        $user = auth()->user();

        // * ------------------------------------------------ //
        // * - Validating Data
        // * ------------------------------------------------ //
        $reglas = [
            'numero_cotizacion' => 'required',
            'monto_total' => 'required|numeric',
        ];

        $this->validate($request, $reglas);
        if ( $user->id !== $order->user_id )return $this->errorResponse('No posee permisos para ejecutar esta acción', 400);
        if ( $record->order_id !== $order->id )return $this->errorResponse('Ese registro no pertenece a la orden especificada', 400);

        // * ------------------------------------------------ //
        // * - Storing Data
        // * ------------------------------------------------ //

        $record->fill((array) $request->all());
        $record->save();

        return $this->showOne($record);
    }
}
