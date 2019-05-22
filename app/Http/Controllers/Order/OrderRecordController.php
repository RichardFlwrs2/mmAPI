<?php

namespace App\Http\Controllers\Order;

use App\Order;
use App\Record;
use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;

class OrderRecordController extends ApiController
{
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
}
