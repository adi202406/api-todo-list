<?php

namespace App\Models;

use App\Models\User;
use App\Models\WorkspaceUser;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Workspace extends Model
{
    use SoftDeletes;
    
    protected $fillable = [
        'title',
        'slug',
        'visibility',
        'owner_id',
    ];

    public function getRouteKeyName()
    {
        return 'slug';
    }
    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function workspaceUsers()
    {
        return $this->hasMany(WorkspaceUser::class); // Relasi one-to-many, Workspace -> WorkspaceUser (pivot table)
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'workspace_user')
            ->withPivot(['role', 'status', 'invited_by', 'joined_at']) // ambil kolom tambahan di pivot
            ->withTimestamps();  // Relasi many-to-many, Workspace -> User melalui pivot
    }
    
}

