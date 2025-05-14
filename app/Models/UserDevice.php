<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class UserDevice extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'device_token', 'device_type'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
