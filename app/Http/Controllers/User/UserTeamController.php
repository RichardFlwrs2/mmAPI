<?php

namespace App\Http\Controllers\User;

use App\User;
use App\Team;
use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;

class UserTeamController extends ApiController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index( User $user )
    {
        $teams = Team::where('owner_id', $user->id )->get();

        return $this->showAll($teams);
    }
}
