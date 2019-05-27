<?php

namespace App\Http\Controllers\Order;

use Validator;
use App\Order;
use App\Record;
use App\File;
use App\Type;
use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;

class OrderRecordController extends ApiController
{
    public function __construct()
    {
        parent::__construct();
        $this->middleware('can:view,order' , ['except' => ['saveDataRecord']]);
    }

    // ----------------------------------------------------------------------------------------------------- //
    // ? - I N D E X
    // ----------------------------------------------------------------------------------------------------- //
    public function index( Order $order )
    {
        $records = $order->records;
        return $this->showAll($records);
    }



    // ----------------------------------------------------------------------------------------------------- //
    // ? - S H O W
    // ----------------------------------------------------------------------------------------------------- //
    public function show(Order $order, Record $record)
    {
        $orderData = Order::with([
            'records' => function ($query) use($record) {
                $query->where('id', '=', $record->id)->with(['products']);
            },
            'status',
            'client'
        ])
        ->where('id', $order->id)
        ->firstOrFail();

        return $this->showOne($orderData);
    }

    // ----------------------------------------------------------------------------------------------------- //
    // ? - SAVE BEFORE SENDING DATA BY ADMIN
    // ----------------------------------------------------------------------------------------------------- //
    public function saveDataRecord(Request $request, $id_order, $id_record)
    {
        $order = Order::where('id', $id_order)->firstOrFail();
        $record = Record::where('id', $id_record)->firstOrFail();
        $user = auth()->user();

        // * ------------------------------------------------ //
        // * - Validating Data
        // * ------------------------------------------------ //

        $data = json_decode( $request->input('data') , true );
        // $file = $request->file->store('pdf', 'local');

        Validator::make($data, [
            'numero_cotizacion' => 'required',
            'monto_total' => 'required|numeric',
        ])->validate();

        if ( $user->id !== $order->user_id ) if ( !$user->esAdministrador() ) return $this->errorResponse('No posee permisos para ejecutar esta acción', 400);
        if ( $record->order_id !== $order->id ) return $this->errorResponse('Ese registro no pertenece a la orden especificada', 400);

        // * ------------------------------------------------ //
        // * - Storing Data
        // * ------------------------------------------------ //

        if ( isset( $request->file ) ) {

            if ($request->file->getClientOriginalExtension() !== 'pdf') return $this->errorResponse('El archivo debe ser de tipo PDF', 400);

            $fileName = $request->file->getClientOriginalName();
            $filePath = $request->file->store('pdf', 'local');

            $record_pdf = new File;
            $record_pdf->name = $fileName;
            $record_pdf->path = $filePath;
            $record_pdf->type_id = Type::ARCHIVO_PDF;
            $record_pdf->record_id = $record->id;
            $record_pdf->save();

        } else {
            return $this->errorResponse('No se ha encontrado el archivo PDF, por favor adjunte uno', 400);
        }

        $record->fill((array) $request->all());
        $record->save();

        return $this->showOne($record);
    }
}
