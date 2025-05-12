<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Label extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'color'
    ];

    public function cards()
    {
        return $this->belongsToMany(Card::class, 'card_label')
            ->withTimestamps(); // Relasi many-to-many, Label -> Card melalui pivot
    }
}
