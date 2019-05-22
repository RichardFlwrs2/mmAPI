<?php

namespace App;

use App\Product;
use Illuminate\Database\Eloquent\Model;

class Type extends Model
{
    protected $fillable = ['name', 'description'];


    const ARCHIVO_AVATAR = 8;
    const ARCHIVO_IMAGE = 9;
    const ARCHIVO_PDF = 10;

    public function products(){
        return $this->hasMany(Product::class);
    }
}
