<?php

namespace App\Models;

use App\Models\User;
use App\Models\Checklist;
use Illuminate\Database\Eloquent\Model;

class ChecklistItem extends Model
{
    protected $fillable = [
        'checklist_id',
        'content',
        'is_completed',
        'position',
        'completed_by',
        'completed_at'
    ];

    protected $casts = [
        'is_completed' => 'boolean',
        'position' => 'integer'
    ];

    public function checklist()
    {
        return $this->belongsTo(Checklist::class);
    }

    public function completedBy()
    {
        return $this->belongsTo(User::class, 'completed_by');
    }
}
