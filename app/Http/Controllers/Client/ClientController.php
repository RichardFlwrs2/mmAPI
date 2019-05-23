<?php

namespace App\Http\Controllers\Client;

use App\Client;
use App\Contact;
use App\Field;
use App\Http\Controllers\ApiController;
use Illuminate\Http\Request;

class ClientController extends ApiController
{
    // ----------------------------------------------------------------------------------------------------- //
    // ? - I N D E X
    // ----------------------------------------------------------------------------------------------------- //
    public function index()
    {
        $clientes = Client::all();

        return $this->showAll($clientes);
    }

    // ----------------------------------------------------------------------------------------------------- //
    // ? - S T O R E
    // ----------------------------------------------------------------------------------------------------- //
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
        if (isset($client_data['fields'])) {

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
            if (isset($contact_value['fields'])) {

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

    // ----------------------------------------------------------------------------------------------------- //
    // ? - S H O W
    // ----------------------------------------------------------------------------------------------------- //
    public function show(Client $client)
    {
        $clientData = Client::with(['fields'])->where('id', $client->id)->firstOrFail();

        // dd($client);

        return $this->showOne($clientData);
    }

    // ----------------------------------------------------------------------------------------------------- //
    // ? - U P D A T E
    // ----------------------------------------------------------------------------------------------------- //
    public function update(Request $request, Client $client)
    {
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
            'client.rfc' => 'required|max:16',
            'client.fields' => 'present|array',

            // * ----| Entities to Delete |-----
            'to_delete' => 'required',
            'to_delete.contacts' => 'present|array',
            'to_delete.fields' => 'present|array',
        ];
        $this->validate($request, $reglas);

        $client_data = $request->client;
        $contacts_to_delete = $request->to_delete['contacts'];
        $fields_to_delete = $request->to_delete['fields'];

        foreach ($contacts_to_delete as $key => $c_id) {
           if ( !$client->contacts()->get()->contains( 'id', $c_id ) )
            return $this->showMessage('El contacto con el id: '. $c_id . ' no existe con este cliente', 400);
        }

        foreach ($client_data['fields'] as $key => $field_value) {
            if ( $field_value['id'] != null && !$client->fields()->get()->contains( 'id', $field_value['id'] ) )
             return $this->showMessage('El campo con el id: '. $field_value['id'] . ' no existe con este cliente', 400);
        }

        foreach ($fields_to_delete as $key => $f_id) {
            if ( !$client->fields()->get()->contains( 'id', $f_id ) )
             return $this->showMessage('El campo con el id: '. $f_id . ' no existe con este cliente', 400);
        }


        // * ------------------------------------------------ //
        // * - Client Update
        // * ------------------------------------------------ //

        $client->fill((array) $client_data);
        $client->save();


        // Campos Extras
        foreach ($client_data['fields'] as $key => $field_value) {
            // dd($field_value);

            if ( isset( $field_value['id'] ) ) {

                $field = Field::where('id', $field_value['id'])->firstOrFail();
                $field->fill((array) $field_value);
                $field->save();

            } else {

                $field_value['client_id'] = $client->id;
                $field = Field::create($field_value);

            }

        }

        // * ------------------------------------------------ //
        // * - Entities To Delete
        // * ------------------------------------------------ //
        foreach ($contacts_to_delete as $contacts => $contact_id) {
            $data = Contact::where('id', $contact_id)->firstOrFail();
            $data->client_id = null;
            $data->deleted_at = date('Y-m-d H:i:s');
            $data->save();
        }

        foreach ($fields_to_delete as $fields => $field_id) {
            $data = Field::where('id', $field_id)->firstOrFail();
            $data->delete();
        }

        // * ------------------------------------------------ //
        // * - Returning data
        // * ------------------------------------------------ //
        $clientData = Client::with(['fields'])->where('id', $client->id)->firstOrFail();
        return $this->showOne($clientData);
    }

    // ----------------------------------------------------------------------------------------------------- //
    // ? - D E S T R O Y
    // ----------------------------------------------------------------------------------------------------- //
    public function destroy(Client $client)
    {
        $client->delete();

        return $this->showOne($client);
    }
}
