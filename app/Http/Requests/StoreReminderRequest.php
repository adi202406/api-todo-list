<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreReminderRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'remind_at' => 'required|date|after_or_equal:now',
            'channel' => 'required|in:in_app,email',
        ];
    }
}