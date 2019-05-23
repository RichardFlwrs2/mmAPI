<?php

namespace App;

use App\User;
use Illuminate\Database\Eloquent\Model;

class Team extends Model
{
    protected $dates = ['deleted_at'];

    protected $fillable = ['name', 'owner_id'];
    protected $hidden = ['pivot'];

    public function user_leader(){
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function users_members(){
        return $this->belongsToMany(User::class);
    }
}
