<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Junges\ACL\Traits\UsersTrait;

class User extends Authenticatable
{
    use Notifiable;
    use UsersTrait;
    use SoftDeletes;

    protected $fillable = [
        'name', 'email', 'password',
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function hostedGame()
    {
        return $this->hasOne(Game::class,'host_user_id');
    }

    public function players()
    {
        return $this->hasMany(Player::class, 'user_id');
    }

    public function games()
    {
        return $this->belongsToMany(Game::class, 'players', 'user_id','game_id');
    }

    public function game()
    {
        return $this->games->first();
    }

    public function player()
    {
        return $this->players->first();
    }
}
