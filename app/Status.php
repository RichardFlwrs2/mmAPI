<?php

namespace App;

use App\Order;
use Illuminate\Database\Eloquent\Model;

class Status extends Model
{
    protected $fillable = ['name', 'description'];

    public function products(){
        return $this->hasMany(Order::class);
    }
}
