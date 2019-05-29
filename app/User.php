<?php

namespace App;

use App\Order;
use App\Role;
use App\Team;
use App\File;
use App\Models\StatsData;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use Notifiable, SoftDeletes;

    const USUARIO_VERIFICADO = '1';
    const USUARIO_NO_VERIFICADO = '0';

    const USUARIO_ADMINISTRADOR = 1;
    const USUARIO_REGULAR = 0;

    protected $table = 'users';
    protected $dates = ['deleted_at'];
    // protected $appends = ['stats'];

    protected $fillable = [
        'name', 'email', 'password', 'role_id', 'admin', 'verification_token', 'verified',
        'phone', 'birthdayDate', 'puesto', 'address',
    ];

    protected $hidden = [
        'password', 'remember_token', 'verification_token', 'verified', 'pivot',
    ];


    // ----------------------------------------------------------------------------------------------------- //
    // ? - APPENDS
    // ----------------------------------------------------------------------------------------------------- //
    public function getStatsAttribute()
    {
        return StatsData::getStatsOfUser($this);
    }

    public function getRoleNameAttribute()
    {
        return $this->role()->first()->name;
    }

    public function getTeamsIndexAttribute()
    {
        return $this->teams()->pluck('name' , 'id');
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

    // ----------------------------------------------------------------------------------------------------- //
    // ? - RELATIONS
    // ----------------------------------------------------------------------------------------------------- //
    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function teams()
    {
        return $this->belongsToMany(Team::class);
    }

    public function team_leader()
    {
        return $this->belongsToMany(Team::class);
    }

    public function avatar()
    {
        return $this->hasOne(File::class);
    }

    // ----------------------------------------------------------------------------------------------------- //
    // ? - Mutators
    // ----------------------------------------------------------------------------------------------------- //
    public function getAdminAttribute($valor) {
        return $valor === 0 ? false : true;
    }

    public function setNameAttribute($valor)
    {
        $this->attributes['name'] = strtolower($valor);
    }

    public function getNameAttribute($valor)
    {
        return ucwords($valor);
    }

    public function setEmailAttribute($valor)
    {
        $this->attributes['email'] = strtolower($valor);
    }

    public function esVerificado()
    {
        return $this->verified == User::USUARIO_VERIFICADO;
    }

    public function esAdministrador()
    {
        return $this->admin == User::USUARIO_ADMINISTRADOR;
    }

    public static function generarVerificationToken()
    {
        return str_random(40);
    }

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }
    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }

}
