<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\User;

class Role extends Model
{
    //
    const SUPER_ADMIN = 1;
    const ADMIN = 2;
    const COTIZADOR = 3;
    const VENDEDOR = 4;

    public function users()
    {
        return $this->hasMany(User::class);
    }
}
