<?php

namespace App;

use App\Contact;
use App\Order;
use App\Field;
use App\Models\StatsData;
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

    public function getStatsAttribute()
    {
        return StatsData::getStatsOfUser($this);
    }

    // VALOR COTIZADO
    public function getValorTotalCotizadoAttribute()
    {
        $data = (object) [ 'value' => 0 ];

        $this->orders()->get()->each(function ( Order $order) use($data) {

            $order->records()->get()->each(function ( $record ) use($data) {

                $data->value = $data->value + $record->monto_total;

            });
        });
        return $data->value;
    }

    // VALOR VENDIDO
    public function getValorTotalVendidoAttribute()
    {
        $data = (object) [ 'value' => 0 ];

        $this->orders()->get()->each(function ( Order $order) use($data) {
            $data->value = $data->value + $order->monto_total;
        });
        return $data->value;
    }

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
