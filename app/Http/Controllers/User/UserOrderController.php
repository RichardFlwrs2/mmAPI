<?php

namespace App\Http\Controllers\User;

use App\User;
use App\Order;
use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;

class UserOrderController extends ApiController
{
    // ----------------------------------------------------------------------------------------------------- //
    // ? - I N D E X
    // ----------------------------------------------------------------------------------------------------- //
    public function index( User $user )
    {
        $orders = Order::where('user_id', $user->id )->get();
        return $this->showAll($orders);
    }
}
