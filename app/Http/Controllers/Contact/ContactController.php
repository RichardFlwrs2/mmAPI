<?php

namespace App\Http\Controllers\Contact;

use App\Contact;
use App\Field;
use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;

class ContactController extends ApiController
{
    // ----------------------------------------------------------------------------------------------------- //
    // ? - I N D E X
    // ----------------------------------------------------------------------------------------------------- //
    public function index()
    {
        $contacts = Contact::all();
        return $this->showAll($contacts);
    }



    // ----------------------------------------------------------------------------------------------------- //
    // ? - S T O R E
    // ----------------------------------------------------------------------------------------------------- //
    public function store(Request $request)
    {
        // * ------------------------------------------------ //
        // * - Validating Data
        // * ------------------------------------------------ //
        $reglas = [
            'client_id' => 'required|numeric',
            'name' => 'required|max:250',
            'area' => 'required|max:250',
            'puesto' => 'required|max:250',
            'email' => 'required|email|max:250',
            'phone' => 'required|min:6|max:16',
            'fields' => 'present|array',
        ];

        $this->validate($request, $reglas);

        $contact = Contact::create($request->all());

        // Campos Extras
        foreach ($request['fields'] as $key => $field_value) {
            $field_value['contact_id'] = $contact->id;
            $field = Field::create($field_value);
        }

        $data = Contact::with(['fields'])->where('id', $contact->id)->firstOrFail();
        return $this->showOne($data, 201);
    }


    // ----------------------------------------------------------------------------------------------------- //
    // ? - U P D A T E
    // ----------------------------------------------------------------------------------------------------- //
    public function update(Request $request, Contact $contact)
    {
        // * ------------------------------------------------ //
        // * - Validating Data
        // * ------------------------------------------------ //
        $reglas = [
            'client_id' => 'required|numeric',
            'name' => 'required|max:250',
            'area' => 'required|max:250',
            'puesto' => 'required|max:250',
            'email' => 'required|email|max:250',
            'phone' => 'required|min:6|max:16',
            'fields' => 'present|array',
            'to_delete' => 'present|array',
        ];

        $this->validate($request, $reglas);

        $contact->fill((array) $request->all());
        $contact->save();

        // Campos Extras
        foreach ($request['fields'] as $key => $field_value) {

            if ( $field_value['id'] === null ) {

                $field_value['contact_id'] = $contact->id;
                $field = Field::create($field_value);

            } else {

                $field = Field::where('id', $field_value['id'])->firstOrFail();
                $field->fill((array) $field_value);
                $field->save();
            }
        }

        // Campos Extras a [ BORRAR ]
        foreach ($request['to_delete'] as $key => $field_id) {

            $data = Field::where('id', $field_id)->firstOrFail();
            $data->delete();

        }

        $contact = Contact::with(['fields'])->where('id', $contact->id)->firstOrFail();
        return $this->showOne($contact);

    }

}
