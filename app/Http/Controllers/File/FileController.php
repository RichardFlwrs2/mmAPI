<?php

namespace App\Http\Controllers\File;

use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;

use Validator;

use App\Type;
use App\File;
use App\Product;
use App\User;

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


        $reglas = [
            'type' => 'required|max:100',
            'id' => 'required|numeric',
            'file' => 'required|file',
        ];

        $this->validate($request, $reglas);

        $id = $request['id'];

        $entity = null;
        $filePath = null;
        $type_file = null;
        $fileName = $request->file->getClientOriginalName();


        switch ( $request['type'] ) {
            // case 'user':
            //     break;

            case 'product':
                $entity = Product::where('id', $id )->firstOrFail();
                $files_quantity = $entity->files()->count();

                if ( $files_quantity >= 3 ) return $this->errorResponse('El producto contiene ya 3 archivos adjuntos, debe borrar alguno para agregar otro', 403);

                $filePath = $request->file->store($entity->id, 'product_attachments');
                $type_file = Type::ARCHIVO_PRODUCT;
                break;

            default:
                return $this->errorResponse('Tipo de peticion: ' . $request['type'] . ' no es valido. Tipos válidos: product', 403);
        }

        $new_file = new File;
        $new_file->name = $fileName;
        $new_file->path = $filePath;
        $new_file->type_id = $type_file;
        if ( $request['type'] === 'product' ) $new_file->product_id = $id;
        $new_file->save();

        return $this->showOne( $new_file );
    }


    // ----------------------------------------------------------------------------------------------------- //
    // ? - U P D A T E
    // ----------------------------------------------------------------------------------------------------- //
    public function update(Request $request, File $file)
    {
        return $this->showOne( $file );
    }

}
