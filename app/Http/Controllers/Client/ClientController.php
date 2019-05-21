<?php

namespace App\Http\Controllers\Client;

use App\Client;
use App\Contact;
use App\Field;
use App\Http\Controllers\ApiController;
use Illuminate\Http\Request;

class ClientController extends ApiController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $clientes = Client::all();

        return $this->showAll($clientes);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        $client_data = $request->client;
        $contact_data = $request->contacts;

        // * ------------------------------------------------ //
        // * - Validating Data
        // * ------------------------------------------------ //
        $reglas = [
            // * ----| Client |-----
            'client' => 'required',
            'client.name' => 'required|max:250',
            'client.created_by' => 'required|numeric',
            'client.phone' => 'required|min:6|max:16',
            'client.address' => 'required|max:250',
            'client.ciudad' => 'required|max:250',
            'client.estado' => 'required|max:250',
            'client.pais' => 'required|max:250',
            'client.codigo_postal' => 'required|max:15',
            'client.puesto' => 'required|max:150',
            'client.rfc' => 'required|max:16',

            // * ----| Contacts |-----
            'contacts' => 'required',
            'contacts.*.name' => 'required|max:250',
            'contacts.*.area' => 'required|max:250',
            'contacts.*.puesto' => 'required|max:250',
            'contacts.*.email' => 'required|email|max:250',
            'contacts.*.phone' => 'required|min:6|max:16',
        ];

        $this->validate($request, $reglas);

        // * ------------------------------------------------ //
        // * - Client Store
        // * ------------------------------------------------ //

        $client = Client::create($client_data);

        // Campos Extras
        if ( isset($client_data['fields']) ) {

            foreach ($client_data['fields'] as $key => $value) {
                $value['client_id'] = $client->id;
                $field = Field::create($value);
            }
        }


        // * ------------------------------------------------ //
        // * - Contacts Store
        // * ------------------------------------------------ //
        foreach ($contact_data as $contacts => $contact_value) {
            $contact_value['client_id'] = $client->id;
            $contact = Contact::create($contact_value);

            // Campos Extras
            if ( isset($contact_value['fields']) ) {

                foreach ($contact_value['fields'] as $key => $field_value) {
                    $field_value['contact_id'] = $contact->id;
                    $field = Field::create($field_value);
                }
            }
        }


        // * ------------------------------------------------ //
        // * - Returning data
        // * ------------------------------------------------ //
        $clientData = Client::with(['fields'])->where('id', $client->id)->firstOrFail();
        return $this->showOne($clientData, 201);

        // ? Debuggin Only
        // $clientData = Client::with(['contacts'])->where('id', $client->id)->get();
        // return $this->showAll($clientData, 201);


    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Client  $client
     * @return \Illuminate\Http\Response
     */
    public function show(Client $client)
    {
        $clientData = Client::with(['fields'])->where('id', $client->id)->firstOrFail();

        // dd($client);

        return $this->showOne($clientData);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Client  $client
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Client $client)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Client  $client
     * @return \Illuminate\Http\Response
     */
    public function destroy(Client $client)
    {
        $client->delete();

        return $this->showOne($client);
    }
}
