<?php

namespace App;

use App\User;
use Illuminate\Database\Eloquent\Model;

class Team extends Model
{
    protected $dates = ['deleted_at'];

    protected $fillable = ['name', 'owner_id'];

    public function user_owner(){
        return $this->hasMany(User::class);
    }

    public function users_members(){
        return $this->belongsToMany(User::class);
    }
}
