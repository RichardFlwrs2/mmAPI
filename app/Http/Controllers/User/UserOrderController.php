<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\ApiController;
use App\Order;
use App\Role;
use App\Status;
use App\User;
use App\Models\StatsData;
use Carbon\Carbon;

class UserOrderController extends ApiController
{
    // ----------------------------------------------------------------------------------------------------- //
    // ? - I N D E X
    // ----------------------------------------------------------------------------------------------------- //
    public function index(User $user)
    {
        $from = Carbon::today()->startOfMonth();
        $to = $from->copy()->endOfMonth();
        $appends = ['lastest_record', 'client_name', 'created_by_name', 'cotizador_name'];

        $user = $user->append('stats');

        $user_stats = $user->stats;

        // ---------------------------------------------------------- //
        // - Normal, solo obtiene los datos propios
        // ---------------------------------------------------------- //
        if ($user->role_id == Role::COTIZADOR || $user->role_id == Role::VENDEDOR) {

            $orders = Order::where('user_id', $user->id)->whereBetween('created_at', [$from, $to])->get()
                ->each(function ( $value ) use($appends) {
                    $value->append($appends);
                });

        }

        // ---------------------------------------------------------- //
        // - Admin Leader, obtiene los datos de todo el equipo
        // ---------------------------------------------------------- //
        else if ($user->role_id == Role::ADMIN) {
            $team = $user->teams()->first();

            $reqsAll = (object) [
                'reqs' => [],
                'cotizadas' => [],
                'vendidas' => [],
            ];

            $team->users_members->each(function ($user, $key) use ($reqsAll) {
                $user_orders = $user->orders;
                $temp = $this->buildReqsData($user_orders);

                foreach ($temp->reqs as $key => $value) array_push($reqsAll->reqs, $value);
                foreach ($temp->cotizadas as $key => $value) array_push($reqsAll->cotizadas, $value);
                foreach ($temp->vendidas as $key => $value) array_push($reqsAll->vendidas, $value);

            });

            $orders = Order::where('user_id', $user->id)
                ->whereBetween('created_at', [$from, $to])
                ->get()
                ->each(function ( $value ) use($appends) {
                    $value->append($appends);
                });

        }

        // ---------------------------------------------------------- //
        // - Super Admin, obtiene los datos de todo
        // ---------------------------------------------------------- //
        else if ($user->role_id == Role::SUPER_ADMIN) {

            $user_stats = StatsData::getStatsOfAll();
            $orders = Order::whereBetween('created_at', [$from, $to])->get()->each(function ( $value ) use($appends) {
                $value->append($appends);
            });


        }

        if ( $user->role_id != Role::ADMIN ) $reqsAll = $this->buildReqsData($orders);

        $data = array(
            'user_stats' => $user_stats,
            'reqs_abiertas' => $reqsAll->reqs,
            'cotizadas_pre' => $reqsAll->cotizadas,
            'vendidas' => $reqsAll->vendidas,
        );

        return response()->json($data, 200);
    }

    private function buildReqsData( $orders ) {

        $reqsAll = (object) [
            'reqs' => [],
            'cotizadas' => [],
            'vendidas' => [],
        ];

        $orders->each(function ($value, $key) use ($reqsAll) {
            if ($value->status_id == Status::NUEVA) {
                array_push($reqsAll->reqs, $value);
            }

            if ($value->status_id == Status::EN_PROCESOS) {
                array_push($reqsAll->reqs, $value);
            }

            if ($value->status_id == Status::PRECOTIZADA) {
                array_push($reqsAll->cotizadas, $value);
            }

            if ($value->status_id == Status::ENVIADA) {
                array_push($reqsAll->cotizadas, $value);
            }

            if ($value->status_id == Status::APROBADA) {
                array_push($reqsAll->vendidas, $value);
            }

        });

        return $reqsAll;

    }
}
