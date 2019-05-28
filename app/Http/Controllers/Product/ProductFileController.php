<?php

namespace App\Http\Controllers\Product;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

use Validator;
use App\Type;
use App\File;
use App\Product;
use App\User;
use App\Http\Controllers\ApiController;

class ProductFileController extends ApiController
{

    public function __construct()
    {
        parent::__construct();
        $this->middleware('can:update,product')->only(['store']);
        $this->middleware('can:delete,product')->only(['destroy']);
    }

    // ----------------------------------------------------------------------------------------------------- //
    // ? - I N D E X
    // ----------------------------------------------------------------------------------------------------- //
    public function index( Product $product )
    {
        $files = $product->files;

        return $this->showAll($files);
    }

    // ----------------------------------------------------------------------------------------------------- //
    // ? - S T O R E
    // ----------------------------------------------------------------------------------------------------- //
    public function store(Request $request, Product $product)
    {
        $reglas = [
            'file' => 'required|file',
        ];

        $this->validate($request, $reglas);

        $fileName = $request->file->getClientOriginalName();
        $files_quantity = $product->files()->count();

        if ( $files_quantity >= 3 ) return $this->errorResponse('El producto contiene ya 3 archivos adjuntos, debe borrar alguno para agregar otro', 403);

        $filePath = $request->file->store($product->id, 'product_attachments');
        $type_file = Type::ARCHIVO_PRODUCT;

        $new_file = new File;
        $new_file->name = $fileName;
        $new_file->path = $filePath;
        $new_file->type_id = $type_file;
        $new_file->product_id = $product->id;
        $new_file->save();

        return $this->showOne( $new_file );
    }


    // ----------------------------------------------------------------------------------------------------- //
    // ? - D E S T R O Y
    // ----------------------------------------------------------------------------------------------------- //
    public function destroy(Request $request, Product $product, File $file)
    {

        // --------------------------------------
        // - VALIDATIONS
        // --------------------------------------
        if ( !isset($file->product_id) ) return $this->errorResponse('Este archivo no esta disponible', 403);

        if ( $file->product_id !== $product->id ) return $this->errorResponse('El archivo no pertenece a este producto', 403);


        // --------------------------------------
        // - DELETING FILE AND FIELD IN DB
        // --------------------------------------
        $file_path = storage_path() .'/app/products/'. $file->path;

        Storage::disk('product_attachments')->delete($file->path);

        $file->delete();

        return $this->showOne( $file );
    }

}
