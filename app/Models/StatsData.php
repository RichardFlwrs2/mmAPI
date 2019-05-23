<?php

namespace App\Models;

use App\User;
use App\Order;
use App\Status;
use Carbon\Carbon;

class StatsData
{

    public static function getStatsOfUser( User $user ) {

        $from = Carbon::today()->startOfMonth();
        $to = $from->copy()->endOfMonth();

        $reqs = $user->orders()->count();
        $vendidas = $user->orders()
            ->where('status_id', Status::APROBADA )
            ->whereBetween('created_at', [$from, $to])
            ->get()
            ->count();
        $cotizadas = $user->orders()
            ->where('status_id', Status::ENVIADA )
            ->whereBetween('created_at', [$from, $to])
            ->get()
            ->count() + $vendidas;

        $reqs_cotizadas_number = ($cotizadas * 100) / $reqs;
        $cots_vendidas_number = ($vendidas * 100) / $cotizadas;

        $reqs_cotizadas = number_format((float) $reqs_cotizadas_number  , 2, '.', '');
        $cots_vendidas = number_format((float) $cots_vendidas_number  , 2, '.', '');

        return array(
            'reqs' => $reqs,
            'cotizadas' => $cotizadas,
            'vendidas' => $vendidas,
            'reqs_cotizadas' => $reqs_cotizadas.'%',
            'reqs_cotizadas_number' => $reqs_cotizadas_number,
            'cots_vendidas' => $cots_vendidas.'%',
            'cots_vendidas_number' => $cots_vendidas_number,
        );
    }

}
