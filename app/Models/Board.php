<?php

namespace App\Models;

use App\Models\Card;
use App\Models\Workspace;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Board extends Model
{
    use  SoftDeletes;

    protected $fillable = [
        'workspace_id',
        'name',
        'description',
        'position',
        'color',
        'is_favorite'
    ];

    protected $casts = [
        'position' => 'integer',
        'is_favorite' => 'boolean'
    ];

    public function workspace()
    {
        return $this->belongsTo(Workspace::class);
    }

    public function cards()
    {
        return $this->hasMany(Card::class)->orderBy('position');
    }
}
