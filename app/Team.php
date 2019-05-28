<?php

namespace App;

use App\User;
use App\Models\StatsData;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

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

    public function getLeaderNameAttribute()
    {
        $user_name = DB::table('users')->where('id', $this->owner_id)->first();
        return $user_name->name;
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
