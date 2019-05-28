<?php

namespace App\Http\Controllers\File;

use Validator;

use App\File;

use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;

class FileController extends ApiController
{

    // ----------------------------------------------------------------------------------------------------- //
    // ? - GET | DOWNLOAD FILE
    // ----------------------------------------------------------------------------------------------------- //
    public function getFile($id, $type)
    {
        $file = File::where('id', $id)->firstOrFail();
        $file_location = $file->path;
        $file_name = $file->name;

        // Check if file exists in 'app/storage/app/pdf/' folder
        $file_path = null;

        switch ( $type ) {
            case 'pdf':
                $file_path = storage_path() .'/app/'. $file_location;
                break;

            case 'product':
                $file_path = storage_path() .'/app/products/'. $file_location;
                break;

            default:
                return $this->errorResponse('Tipo de peticion: ' . $request['type_petition'] . ' no es valido. Tipos válidos: pdf, product', 403);
        }

        if (file_exists($file_path))
        {
            // Send Download
            return response()->download($file_path, $file_name, [
                'Content-Length: '. filesize($file_path)
            ]);
        }
        else
        {
            // Error
            return $this->errorResponse('Requested file does not exist on our server!', 404);
        }
    }

    // ----------------------------------------------------------------------------------------------------- //
    // ? - S T O R E
    // ----------------------------------------------------------------------------------------------------- //
    public function store(Request $request)
    {
        $data = json_decode( $request->input('data') , true );
        // $file = $request->file->store('pdf', 'local');

        Validator::make($data, [
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'role_id' => 'required|numeric',
        ])->validate();
    }


    // ----------------------------------------------------------------------------------------------------- //
    // ? - U P D A T E
    // ----------------------------------------------------------------------------------------------------- //
    public function update(Request $request, File $file)
    {
        return $this->showOne( $file );
    }

}
