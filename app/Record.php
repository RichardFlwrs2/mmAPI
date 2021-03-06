<?php

namespace App;

use App\Order;
use App\Product;
use App\File;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Record extends Model
{
    use SoftDeletes;

    protected $dates = ['deleted_at', 'sended_at'];
    protected $hidden = [ 'deleted_at', ];
    protected $fillable = ['order_id', 'numero_cotizacion', 'monto_total', 'temporal'];

    const RECORD_TEMPORAL = '1';
    const RECORD_NO_TEMPORAL = '0';

    public function getTemporalAttribute($valor) {
        return $valor === '0' ? false : true;
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function pdf_file()
    {
        return $this->hasOne(File::class);
    }
}
