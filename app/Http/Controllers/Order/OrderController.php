<?php

namespace App\Http\Controllers\Order;

use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;
use App\Order;

class OrderController extends ApiController
{
    // ---------------------------------------------------------------------------------
    /** --------------------------------------------------------------------------------
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $orders = Order::all();

        return $this->showAll($orders);
    }

    // ---------------------------------------------------------------------------------
    /** --------------------------------------------------------------------------------
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    // ---------------------------------------------------------------------------------
    /** --------------------------------------------------------------------------------
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Order $order)
    {
        $orderData = Order::with(['last_record.products', 'status', 'client'])
                        ->where('id', $order->id)
                        ->firstOrFail();

        return $this->showOne($orderData);
    }


    // ---------------------------------------------------------------------------------
    /** --------------------------------------------------------------------------------
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Order $order)
    {
        $order->delete();

        return $this->showOne($order);
    }
}
