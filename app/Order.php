<?php

namespace App;

use App\User;
use App\Status;
use App\Record;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{

    protected $dates = ['deleted_at'];

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

    public function records()
    {
        return $this->hasMany(Record::class);
    }
}
