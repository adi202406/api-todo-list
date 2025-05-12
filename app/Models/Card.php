<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Card extends Model
{
     use HasFactory, SoftDeletes;

    protected $fillable = [
        'board_id',
        'title',
        'description',
        'due_date',
        'position'
    ];

    protected $casts = [
        'due_date' => 'date',
        'position' => 'integer'
    ];

    public function board()
    {
        return $this->belongsTo(Board::class);
    }
}
