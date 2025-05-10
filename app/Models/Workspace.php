<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class Workspace extends Model
{
    protected $fillable = [
        'title',
        'slug',
        'visibility',
        'owner_id',
    ];

    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function getRouteKeyName()
    {
        return 'slug';
    }
}
