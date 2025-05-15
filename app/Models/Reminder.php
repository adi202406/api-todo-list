<?php

namespace App\Models;

use App\Models\CardUser;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Reminder extends Model
{
   use HasFactory, SoftDeletes;

    protected $fillable = [
        'card_id',
        'remind_at',
        'channel',
        'is_sent',
    ];

    protected $casts = [
        'remind_at' => 'datetime',
        'is_sent' => 'boolean',
    ];

    public const CHANNEL_IN_APP = 'in_app';
    public const CHANNEL_EMAIL = 'email';

    public static function channels(): array
    {
        return [
            self::CHANNEL_IN_APP,
            self::CHANNEL_EMAIL,
        ];
    }

    public function card()
    {
        return $this->belongsTo(Card::class);
    }

    public function users()
    {
        return $this->hasManyThrough(
            User::class,
            CardUser::class,
            'card_id', // Foreign key on CardUser table
            'id', // Foreign key on User table
            'card_id', // Local key on Reminder table
            'user_id' // Local key on CardUser table
        );
    }
}
