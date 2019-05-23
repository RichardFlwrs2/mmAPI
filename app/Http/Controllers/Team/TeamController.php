<?php

namespace App\Http\Controllers\Team;

use App\Team;
use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;

class TeamController extends ApiController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $teams = Team::all();

        return $this->showAll($teams);
    }

    public function stats($id) {

        $user = User::with(['orders'])->where('id', $id)->first();
        return $this->showOne($user);

    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // * ------------------------------------------------ //
        // * - Validating Data
        // * ------------------------------------------------ //
        $reglas = [
            'name' => 'required|max:250',
            'owner_id' => 'required|numeric',
            'to_add' => 'required|array',
        ];

        $this->validate($request, $reglas);

        $team = Team::create($request->all());
        $team->users_members()->attach($request['to_add']);

        $teamData = Team::with(['user_leader'])->where('id', $team->id)->firstOrFail();
        return $this->showOne($teamData);

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Team  $team
     * @return \Illuminate\Http\Response
     */
    public function show(Team $team)
    {
        $teamData = Team::with(['user_leader'])->where('id', $team->id)->firstOrFail();
        return $this->showOne($teamData);
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Team  $team
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Team $team)
    {
        // * ------------------------------------------------ //
        // * - Validating Data
        // * ------------------------------------------------ //
        $reglas = [
            'name' => 'required|max:250',
            'owner_id' => 'required|numeric',
            'to_add' => 'present|array',
            'to_delete' => 'present|array',
        ];

        $this->validate($request, $reglas);

        $deletingLeader = in_array( $request['owner_id'], $request['to_delete'] );
        if ($deletingLeader) return $this->showMessage('No puedes borrar al lider del equipo', 400);

        // -------------------------------------------------- //
        // - // UPDATING DATA
        // -------------------------------------------------- //

        $team->fill((array) $request->all());
        $team->save();

        // -------------------------------------------------- //
        // - // ADD TO TEAM
        // -------------------------------------------------- //
        foreach ($request['to_add'] as $key => $user_id) {
            $isInTheTeam = $team->users_members()->get()->contains('id', $user_id);

            if ( !$isInTheTeam ) { // Add it to the Team
                $team->users_members()->attach($user_id);
            }
        }

        // -------------------------------------------------- //
        // - // DELETE FROM TEAM
        // -------------------------------------------------- //
        foreach ($request['to_delete'] as $key => $user_id) {

            $team->users_members()->detach($user_id);

        }


        $teamData = Team::with(['user_leader'])->where('id', $team->id)->firstOrFail();
        return $this->showOne($teamData);


    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Team  $team
     * @return \Illuminate\Http\Response
     */
    public function destroy(Team $team)
    {
        // $team->owner_id = null;
        // $team->users_members()->detach($team->users_members()->get()->values());
        // $team->save();
        $team->delete();
        return $this->showOne($team);
    }
}
