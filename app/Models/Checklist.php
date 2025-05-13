<?php

namespace App\Models;

use App\Models\Card;
use App\Models\ChecklistItem;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Checklist extends Model
{
      use SoftDeletes;

    protected $fillable = [
        'card_id',
        'title',
        'position',
        'is_completed',
        'completed_items',
        'total_items'
    ];

    protected $casts = [
        'is_completed' => 'boolean',
        'position' => 'integer'
    ];

    public function card()
    {
        return $this->belongsTo(Card::class);
    }

    public function items()
    {
        return $this->hasMany(ChecklistItem::class)->orderBy('position');
    }
}
