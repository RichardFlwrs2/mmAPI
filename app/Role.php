<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    //
    const SUPER_ADMIN = 1;
    const ADMIN = 2;
    const COTIZADOR = 3;
    const VENDEDOR = 4;
}
