<?php

namespace App;

use App\User;
use App\Status;
use App\Record;
use App\Client;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{

    use SoftDeletes;

    protected $dates = ['deleted_at', 'sended_at'];
    protected $appends = ['index_records'];

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
        return $this->hasMany(Record::class)->latest()->take(1);
    }

    public function getIndexRecordsAttribute()
    {
        return $this->records()->pluck('numero_cotizacion' , 'id');
    }
}
