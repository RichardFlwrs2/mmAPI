<?php

namespace App;

use App\Client;
use App\Field;
use Illuminate\Database\Eloquent\Model;

class Contact extends Model
{
    protected $dates = ['deleted_at'];

    protected $fillable = [
        'client_id',
        'name',
        'area',
        'puesto',
        'email',
        'phone',
    ];

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function fields()
    {
        return $this->hasMany(Field::class);
    }
}
