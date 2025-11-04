<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'api_token',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token', 'api_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * Generate API token for the user
     */
    public function generateApiToken()
    {
        $this->api_token = Str::random(60);
        $this->save();
        return $this->api_token;
    }

    /**
     * Regenerate API token
     */
    public function regenerateApiToken()
    {
        $this->api_token = Str::random(60);
        $this->save();
        return $this->api_token;
    }

    /**
     * Revoke API token
     */
    public function revokeApiToken()
    {
        $this->api_token = null;
        $this->save();
    }

    /**
     * Check if user has valid API token
     */
    public function hasApiToken()
    {
        return !empty($this->api_token);
    }
}
