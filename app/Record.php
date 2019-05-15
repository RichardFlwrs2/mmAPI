<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Record extends Model
{
    protected $fillable = ['order_id', 'numero_cotizacion', 'monto_total', 'temporal'];

    public function order()
    {
        return $this->belongsTo('App\Order');
    }

    public function products()
    {
        return $this->hasMany('App\Product')->where('active', 1);
    }
}
