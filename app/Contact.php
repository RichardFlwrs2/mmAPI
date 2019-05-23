<?php

namespace App;

use App\Client;
use App\Field;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Contact extends Model
{
    use SoftDeletes;

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
