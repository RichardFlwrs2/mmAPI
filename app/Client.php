<?php

namespace App;

use App\Contact;
use App\Order;
use App\Field;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    use SoftDeletes;

    protected $dates = ['deleted_at'];

    protected $fillable = [
        'name',
        'created_by',
        'phone',
        'address',
        'ciudad',
        'estado',
        'pais',
        'codigo_postal',
        'rfc'
    ];

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function contacts()
    {
        return $this->hasMany(Contact::class);
    }

    public function fields()
    {
        return $this->hasMany(Field::class);
    }
}
