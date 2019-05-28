<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use App\User;
use App\Client;
use App\Order;
use App\Record;
use App\Product;
use App\Type;

class File extends Model
{
    use SoftDeletes;

    protected $dates = ['deleted_at'];

    protected $fillable = [
        'name',
        'path',
        'type_id',
        'user_id',
        'client_id',
        'product_id',
        'record_id',
        'order_id',
    ];

    protected $hidden = ['type_id', 'order_id', 'client_id', 'record_id', 'product_id', 'type_id'];

    public function order(){
        return $this->belongsTo(Order::class);
    }

    public function type(){
        return $this->belongsTo(Type::class);
    }

    public function product(){
        return $this->belongsTo(Product::class);
    }

    public function record(){
      return $this->belongsTo(Record::class);
    }

    public function user(){
        return $this->belongsTo(User::class);
    }

    public function client(){
        return $this->belongsTo(Client::class);
    }
}
