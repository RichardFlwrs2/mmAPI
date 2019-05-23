<?php

namespace App;

use App\User;
use App\Models\StatsData;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Team extends Model
{
    use SoftDeletes;

    protected $dates = ['deleted_at'];

    protected $fillable = ['name', 'owner_id'];
    protected $hidden = ['pivot'];

    // ----------------------------------------------------------------------------------------------------- //
    // ? - APPENDS
    // ----------------------------------------------------------------------------------------------------- //
    public function getStatsAttribute()
    {
        return StatsData::getStatsOfTeam($this);
    }

    // ----------------------------------------------------------------------------------------------------- //
    // ? - RELATIONS
    // ----------------------------------------------------------------------------------------------------- //
    public function user_leader(){
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function users_members(){
        return $this->belongsToMany(User::class);
    }
}
