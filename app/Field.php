<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Client;
use App\Contact;
use App\User;

class Field extends Model
{
    protected $dates = ['deleted_at'];

    protected $fillable = [
        'name',
        'data',
        'client_id',
        'user_id',
        'contact_id',
    ];

    protected $hidden = [
        'client_id', 'user_id', 'contact_id', 'created_at', 'updated_at', 'deleted_at',
    ];


    public function client(){
        return $this->belongsTo(Client::class);
    }

    public function contact(){
        return $this->belongsTo(Contact::class);
    }

    public function user(){
        return $this->belongsTo(User::class);
    }
}
