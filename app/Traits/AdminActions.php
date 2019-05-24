<?php

namespace App\Traits;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;
use Illuminate\Pagination\LengthAwarePaginator;

trait AdminActions
{
	public function before( $user, $ability ) {
        if ( $user->esAdministrador() ) {
            return true;
        }
    }
}
