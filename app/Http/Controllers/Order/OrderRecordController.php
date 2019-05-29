<?php

namespace App\Http\Controllers\Order;

use Validator;
use App\Order;
use App\Record;
use App\File;
use App\Type;
use App\Mail\PetitionToSend;

use Illuminate\Support\Facades\Mail;
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

        $cliente = $order->client()->first();
        $record = $record->with('products')->first();

        $data = array(
            'order' => $order,
            'record' => $record,
            'cliente' => $cliente,
        );

        return response()->json($data, 200);
    }


    // ----------------------------------------------------------------------------------------------------- //
    // ? - SAVE BEFORE SENDING DATA BY ADMIN
    // ----------------------------------------------------------------------------------------------------- //
    public function saveDataRecord(Request $request, $id_order, $id_record)
    {
        $order = Order::where('id', $id_order)->firstOrFail();
        $record = Record::where('id', $id_record)->firstOrFail();
        $user = auth()->user();

        $leader = $order->team_belonged()->user_leader;
        $status = $order->status;

        // * ------------------------------------------------ //
        // * - Validating Data
        // * ------------------------------------------------ //
        $reglas = [
            'data' => 'required',
            'file' => 'required|file',
        ];
        $this->validate($request, $reglas);

        $data = json_decode( $request->input('data') , true );
        // $file = $request->file->store('pdf', 'local');

        Validator::make($data, [
            'numero_cotizacion' => 'required',
            'monto_total' => 'required|numeric',
        ])->validate();

        if ( $record->order_id !== $order->id ) return $this->errorResponse('Ese registro no pertenece a la orden especificada', 400);

        // * ------------------------------------------------ //
        // * - Storing Data
        // * ------------------------------------------------ //

        if ( isset( $request->file ) ) {

            if ($request->file->getClientOriginalExtension() !== 'pdf') return $this->errorResponse('El archivo debe ser de tipo PDF', 400);


            if ( $record->has('pdf_file')->get()->isEmpty() ) {

                $record_pdf = new File;
                $record_pdf->name = $request->file->getClientOriginalName();
                $record_pdf->path = $request->file->store('pdf/' . $order->id , 'local');
                $record_pdf->type_id = Type::ARCHIVO_PDF;
                $record_pdf->record_id = $record->id;
                $record_pdf->save();

            } else {

                $record_pdf = $record->pdf_file;
                $record_pdf->name = $request->file->getClientOriginalName();
                $record_pdf->path = $request->file->store('pdf/' . $order->id , 'local');
                $record_pdf->save();

            }


        } else {
            return $this->errorResponse('No se ha encontrado el archivo PDF, por favor adjunte uno', 400);
        }

        $record->fill((array) $data);
        $record->save();

        Mail::to($leader)->send(new PetitionToSend($leader, $status, $order));

        return $this->showMessage('Se ha hecho una peticion para enviar la requisición, espere a que el administrador lo autorize');
        // return $this->showOne($record);
    }
}
