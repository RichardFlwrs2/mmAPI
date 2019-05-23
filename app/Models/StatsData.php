<?php

namespace App\Models;

use App\User;
use App\Team;
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

        $reqs_cotizadas_number = $reqs != 0 ? ($cotizadas * 100) / $reqs : 0;
        $cots_vendidas_number = $cotizadas != 0 ? ($vendidas * 100) / $cotizadas : 0;

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


    public static function getStatsOfTeam( Team $team ) {

        $from = Carbon::today()->startOfMonth();
        $to = $from->copy()->endOfMonth();

        $team_stats = (object) [
            'reqs' => 0,
            'cotizadas' => 0,
            'vendidas' => 0,
        ];

        $team->users_members()->each(function ($user, $key) use($team_stats, $from, $to) {
            $orders = $user->orders->count();
            $team_stats->reqs = $team_stats->reqs + $orders;

            $vendidas = $user->orders()
                ->where('status_id', Status::APROBADA )
                ->whereBetween('created_at', [$from, $to])
                ->get()
                ->count();
            $team_stats->vendidas = $team_stats->vendidas + $vendidas;

            $cotizadas = $user->orders()
                ->where('status_id', Status::ENVIADA )
                ->whereBetween('created_at', [$from, $to])
                ->get()
                ->count() + $vendidas;
            $team_stats->cotizadas = $team_stats->cotizadas + $cotizadas;
        });

        $reqs_cotizadas_number = $team_stats->reqs != 0 ? ($team_stats->cotizadas * 100) / $team_stats->reqs : 0;
        $cots_vendidas_number = $team_stats->cotizadas != 0 ? ($team_stats->vendidas * 100) / $team_stats->cotizadas : 0;

        $reqs_cotizadas = number_format((float) $reqs_cotizadas_number  , 2, '.', '');
        $cots_vendidas = number_format((float) $cots_vendidas_number  , 2, '.', '');

        return array(
            'reqs' => $team_stats->reqs,
            'cotizadas' => $team_stats->cotizadas,
            'vendidas' => $team_stats->vendidas,
            'reqs_cotizadas' => $reqs_cotizadas.'%',
            'reqs_cotizadas_number' => $reqs_cotizadas_number,
            'cots_vendidas' => $cots_vendidas.'%',
            'cots_vendidas_number' => $cots_vendidas_number,
        );

    }

}
