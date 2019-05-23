<?php

namespace App;

use App\Order;
use App\Product;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Record extends Model
{
    use SoftDeletes;

    protected $hidden = [ 'deleted_at', ];
    protected $fillable = ['order_id', 'numero_cotizacion', 'monto_total', 'temporal'];

    const RECORD_TEMPORAL = '1';
    const RECORD_NO_TEMPORAL = '0';

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }
}
