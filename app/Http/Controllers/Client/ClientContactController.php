<?php

namespace App\Http\Controllers\Client;

use App\Client;
use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;

class ClientContactController extends ApiController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index( Client $client )
    {
        $contacts = $client->contacts()->with('fields')->get();

        return $this->showAll($contacts);
    }
}
