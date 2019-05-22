<?php

namespace App;

use App\Record;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $dates = ['deleted_at'];

    protected $fillable = [
        'record_id',
        'quantity',
        'brand',
        'model_number',
        'serial_number',
        'details',
        'description',
        'type_id',
        'condition_id',
        'costo_u',
        'costo_t',

    ];
    protected $hidden = [ 'deleted_at', ];

    public function record()
    {
        return $this->belongsTo(Record::class);
    }
}
