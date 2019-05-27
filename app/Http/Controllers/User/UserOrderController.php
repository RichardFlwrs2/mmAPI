<?php

namespace App\Http\Controllers\User;

use App\User;
use App\Order;
use App\Status;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;

class UserOrderController extends ApiController
{
    // ----------------------------------------------------------------------------------------------------- //
    // ? - I N D E X
    // ----------------------------------------------------------------------------------------------------- //
    public function index( User $user )
    {
        $from = Carbon::today()->startOfMonth();
        $to = $from->copy()->endOfMonth();

        $user = $user->append('stats');
        $orders = Order::where('user_id', $user->id )->with(['last_record'])->whereBetween('created_at', [$from, $to])->get();


        $reqsAll = (object) [
            'reqs' => [],
            'cotizadas' => [],
            'vendidas' => [],
        ];

        $orders->each(function ($value, $key) use ($reqsAll) {
            if ($value->status_id == Status::NUEVA) array_push( $reqsAll->reqs , $value );
            if ($value->status_id == Status::EN_PROCESOS) array_push( $reqsAll->reqs , $value );
            if ($value->status_id == Status::PRECOTIZADA) array_push( $reqsAll->cotizadas , $value );
            if ($value->status_id == Status::ENVIADA) array_push( $reqsAll->cotizadas , $value );
            if ($value->status_id == Status::APROBADA) array_push( $reqsAll->vendidas , $value );
        });

        $data = array(
            'user_stats' => $user->stats,
            'reqs_abiertas' => $reqsAll->reqs,
            'cotizadas_pre' => $reqsAll->cotizadas,
            'vendidas' => $reqsAll->vendidas,
        );

        return response()->json($data, 200);
    }
}
