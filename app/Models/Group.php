<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'created_by'
    ];

    /**
     * Get the users that belong to the group.
     */
    public function users()
    {
        return $this->hasMany(User::class);
    }

    /**
     * Get the owner of the group.
     */
    public function owner()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
