<?php

namespace App;

use App\Product;
use Illuminate\Database\Eloquent\Model;

class Type extends Model
{
    protected $fillable = ['name', 'description'];

    public function products(){
        return $this->hasMany(Product::class);
    }
}
