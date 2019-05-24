<?php

namespace App\Http\Controllers\Order;

use Illuminate\Http\Request;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\Mail;

use App\Http\Controllers\ApiController;
use App\Mail\StatusChanged;
use App\Mail\PetitionToSend;
use App\Mail\PetitionToFinish;
use App\Mail\PetitionToDelete;
use App\Order;
use App\Record;
use App\Product;
use App\Role;


class OrderController extends ApiController
{

    public function __construct()
    {
        parent::__construct();
        $this->middleware('can:view,order')->only(['show', 'update']);
    }

    // ---------------------------------------------------------------------------------
    /** --------------------------------------------------------------------------------
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $orders = Order::all();

        return $this->showAll($orders);
    }

    // ---------------------------------------------------------------------------------
    /** --------------------------------------------------------------------------------
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
            // Order
            'user_id' => 'required|numeric',
            'status_id' => 'required|numeric',
            'client_id' => 'required|numeric',
            'folio' => 'present|max:250',
            'numero_orden' => 'present|max:150|nullable',
            'monto_total' => 'present|numeric|nullable',

            // Record
            'record' => 'required',
            'record.numero_cotizacion' => 'present|nullable',
            'record.monto_total' => 'present|numeric|nullable',

            // Products
            'record.products' => 'present|array',
            'record.products.*.quantity' => 'required|numeric',
            'record.products.*.brand' => 'required',
            'record.products.*.model_number' => 'required',
            'record.products.*.serial_number' => 'present',
            'record.products.*.details' => 'present|max:250',
            'record.products.*.description' => 'required|max:250',
            'record.products.*.type_id' => 'required|numeric',
            'record.products.*.condition_id' => 'required|numeric',
            'record.products.*.costo_u' => 'present|numeric|nullable',
            'record.products.*.costo_t' => 'present|numeric|nullable',
        ];

        $this->validate($request, $reglas);

        $user = auth()->user();

        if ($user->role_id === Role::COTIZADOR ) {
            throw new AuthorizationException('Esta acción no te es permitida');
        }

        // * ------------------------------------------------ //
        // * - Storing Data
        // * ------------------------------------------------ //

        // ----| Order |------->
        $order_campos = $request->all();
        unset($order_campos['record']);
        $order_campos['created_by'] = $user->id;

        $order = Order::create($order_campos);

        // ----| Record |------->
        $record_campos = $request->all()['record'];
        unset($record_campos['products']);
        $record_campos['order_id'] = $order->id;
        $record_campos['temporal'] = isset( $record_campos['temporal'] ) ? $record_campos['temporal'] : Record::RECORD_NO_TEMPORAL;

        $record = Record::create($record_campos);

        // ----| Products Array |------->
        $products_campos = $request->all()['record']['products'];

        foreach ($products_campos as $key => $product_value) {

            $product_value['record_id'] = $record->id;
            $product = Product::create($product_value);

        }


        $orderData = Order::with(['last_record.products', 'status', 'client'])
            ->where('id', $order->id)
            ->firstOrFail();

        $leader = $orderData->team_belonged()->user_leader;
        $status = $orderData->status;

        Mail::to($leader)->send(new StatusChanged($leader, $status, $orderData));

        return $this->showOne($orderData);

    }

    // ---------------------------------------------------------------------------------
    /** --------------------------------------------------------------------------------
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Order $order)
    {
        $orderData = Order::with(['last_record.products', 'status', 'client'])
                        ->where('id', $order->id)
                        ->firstOrFail();

        return $this->showOne($orderData);
    }

    public function petition( Request $request, $id)
    {
        $order = Order::where('id', $id)->firstOrFail();
        $leader = $order->team_belonged()->user_leader;

        // * ------------------------------------------------ //
        // * - Validating Data
        // * ------------------------------------------------ //
        $reglas = [
            // Order
            'type_petition' => 'required',
        ];

        $this->validate($request, $reglas);

        // if ( $order->userAssigned()->first()->esAdministrador() ) {
        //     return $this->errorResponse('No puedes enviar peticiones si tu eres el admin', 400);
        // }

        // * ------------------------------------------------ //
        // * - Sending Data
        // * ------------------------------------------------ //
        $leader = $order->team_belonged()->user_leader;
        $status = $order->status;

        switch ($request['type_petition']) {
            case 'send':
                Mail::to($leader)->send(new PetitionToSend($leader, $status, $order));
                return $this->showMessage('Se ha hecho una peticion para enviar la requisición, espere a que el administrador lo autorize');

            case 'finish':
                Mail::to($leader)->send(new PetitionToFinish($leader, $status, $order));
                return $this->showMessage('Se ha hecho una peticion para finalizar la requisición, espere a que el administrador lo autorize');

            case 'delete':
                Mail::to($leader)->send(new PetitionToDelete($leader, $status, $order));
                return $this->showMessage('Se ha hecho una peticion para borrar la requisición, espere a que el administrador lo autorize');

            default:
                return $this->errorResponse('Tipo de peticion: ' . $request['type_petition'] . ' no es valido. Tipos válidos: send, finish, delete', 403);
        }

        return $this->showOne($order);
    }


    // ---------------------------------------------------------------------------------
    /** --------------------------------------------------------------------------------
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Order $order)
    {
        // * ------------------------------------------------ //
        // * - Validating Data
        // * ------------------------------------------------ //
        $reglas = [
            // Order
            'id' => 'required|numeric',
            'created_by' => 'required|numeric',
            'user_id' => 'required|numeric',
            'status_id' => 'required|numeric',
            'client_id' => 'required|numeric',
            'folio' => 'present|max:250',
            'numero_orden' => 'present|max:150|nullable',
            'monto_total' => 'present|numeric|nullable',

            // Record
            'record' => 'required',
            'record.id' => 'required|numeric',
            'record.order_id' => 'required|numeric',
            'record.numero_cotizacion' => 'present|nullable',
            'record.monto_total' => 'present|numeric|nullable',

            // Products
            'record.products' => 'present|array',
            'record.products.*.id' => 'present|numeric|nullable',
            'record.products.*.record_id' => 'required|numeric',
            'record.products.*.quantity' => 'required|numeric',
            'record.products.*.brand' => 'required',
            'record.products.*.model_number' => 'required',
            'record.products.*.serial_number' => 'present',
            'record.products.*.details' => 'present|max:250',
            'record.products.*.description' => 'required|max:250',
            'record.products.*.type_id' => 'required|numeric',
            'record.products.*.condition_id' => 'required|numeric',
            'record.products.*.costo_u' => 'present|numeric|nullable',
            'record.products.*.costo_t' => 'present|numeric|nullable',

            // * ----| Entities to Delete |-----
            'to_delete' => 'required',
            'to_delete.products' => 'present|array',
        ];

        $this->validate($request, $reglas);

        $user = auth()->user();

        if ( $request['status_id'] === 4 && !$user->esAdministrador() ) {
            $leader = $order->team_belonged()->user_leader;
            $status = $order->status;

            Mail::to($leader)->send(new PetitionToSend($leader, $status, $order));
            return $this->errorResponse('No puedes enviar la requisicón, se le ha notificado a tu administrador una petición para esta acción', 403);

        }

        // * ------------------------------------------------ //
        // * - Updating Data
        // * ------------------------------------------------ //

        // ----| Order |------->
        $order_campos = $request->all();
        unset($order_campos['record']);

        $order->fill((array) $order_campos);
        $order->save();


        // ----| Record |------->
        $record_campos = $request->all()['record'];
        unset($record_campos['products']);

        $record = Record::where('id', $record_campos['id'] )->firstOrFail();
        $record->fill((array) $record_campos);
        $record->save();


        // ----| Products Array |------->
        $products_campos = $request->all()['record']['products'];

        foreach ($products_campos as $key => $product_value) {

            if ( isset( $product_value['id'] ) ) {

                $product = Product::where('id', $product_value['id'] )->firstOrFail();
                $product->fill((array) $product_value);
                $product->save();

            } else {

                // $product_value['record_id'] = $record->id; // it should be coming from front
                $product = Product::create($product_value);

            }

        }


        // ----| Entities to Delete |------->
        $products_to_delete = $request->to_delete['products'];
        foreach ($products_to_delete as $key => $product_id) {

            $data = Product::where('id', $product_id)->firstOrFail();
            $data->delete();

        }



        $orderData = Order::with(['last_record.products', 'status', 'client'])
                        ->where('id', $order->id)
                        ->firstOrFail();
        return $this->showOne($orderData);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Order $order)
    {
        $this->allowedAdminAction();

        $order->delete();

        return $this->showOne($order);
    }
}
