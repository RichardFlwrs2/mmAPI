<?php

namespace App;

use App\Order;
use Illuminate\Database\Eloquent\Model;

class Status extends Model
{
    protected $fillable = ['name', 'description'];

    public function orders(){
        return $this->hasMany(Order::class);
    }
}
