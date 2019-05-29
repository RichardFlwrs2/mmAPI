<?php

namespace App;

use App\User;
use App\Status;
use App\Record;
use App\Client;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class Order extends Model
{

    use SoftDeletes;

    protected $dates = ['deleted_at', 'sended_at'];
    protected $appends = ['index_records', 'status_name'];

    protected $fillable = [
        'created_by',
        'user_id',
        'status_id',
        'client_id',
        'folio',
        'numero_orden',
        'monto_total',
        'finished_at',
        'sended_at',
    ];
    protected $hidden = [ 'deleted_at', ];

    // ------------------------------------------------------- //
    // - Functions
    // ------------------------------------------------------- //
    public function team_belonged()
    {
        return $this->userAssigned->teams()->first();
    }

    // ------------------------------------------------------- //
    // * RELATIONS
    // ------------------------------------------------------- //

    public function owner()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function userAssigned()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function status()
    {
        return $this->belongsTo(Status::class, 'status_id');
    }

    public function client()
    {
        return $this->belongsTo(Client::class, 'client_id');
    }


    // ------------------------------------------------------- //
    // - Records
    // ------------------------------------------------------- //
    public function records()
    {
        return $this->hasMany(Record::class);
    }

    public function last_record()
    {
        return $this->hasOne(Record::class)->latest()->take(1);
    }

    // ------------------------------------------------------- //
    // - Appends
    // ------------------------------------------------------- //
    public function getLastestRecordAttribute() // lastest_record
    {
        $record = DB::table('records')->where('order_id', $this->id)->latest()->take(1)->first();
        return $record;
    }

    public function getStatusNameAttribute()
    {
        return $this->status()->first()->name;
    }

    public function getClientNameAttribute()
    {
        // $client = DB::table('clients')->where('id', $this->client_id)->first();
        // return $client->name;
        return $this->client()->first()->name;
    }

    public function getCreatedByNameAttribute()
    {
        return $this->owner()->first()->name;
    }

    public function getCotizadorNameAttribute()
    {
        return $this->userAssigned()->first()->name;
    }

    public function getIndexRecordsAttribute()
    {
        $data = [];
        $values = $this->records()->pluck('numero_cotizacion' , 'id');

        foreach ($values as $key => $value) {
            $index_record = (object) [
                'id' => $key,
                'numero_cotizacion' => $value,
            ];

            array_push( $data, $index_record );
        }

        return $data;
    }
}
