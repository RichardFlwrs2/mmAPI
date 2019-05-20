<?php

namespace App\Http\Controllers\Team;

use App\Team;
use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;

class TeamUserController extends ApiController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index( Team $team )
    {
        $users = $team->users_members;

        return $this->showAll($users);
    }
}
