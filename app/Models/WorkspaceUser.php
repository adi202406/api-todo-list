<?php

namespace App\Models;

use App\Models\User;
use App\Models\Workspace;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class WorkspaceUser extends Model
{
    use SoftDeletes;
    
    protected $table = 'workspace_user';

    protected $fillable = [
        'workspace_id',
        'user_id',
        'role',
        'status',
        'invited_by',
        'joined_at',
    ];

    public function workspace()
    {
        return $this->belongsTo(Workspace::class); // Relasi many-to-one, WorkspaceUser -> Workspace
    }

    public function user()
    {
        return $this->belongsTo(User::class); // Relasi many-to-one, WorkspaceUser -> User
    }
}
