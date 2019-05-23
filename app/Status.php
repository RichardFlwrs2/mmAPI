<?php

namespace App;

use App\Order;
use Illuminate\Database\Eloquent\Model;

class Status extends Model
{
    protected $fillable = ['name', 'description'];

    const NUEVA = 1;
    const EN_PROCESOS = 2;
    const PRECOTIZADA = 3;
    const ENVIADA = 4;
    const APROBADA = 5;

    public function orders(){
        return $this->hasMany(Order::class);
    }
}
