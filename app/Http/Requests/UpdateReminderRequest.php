<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateReminderRequest extends FormRequest
{
    public function authorize()
    {
        return $this->user()->can('update', $this->reminder);
    }

    public function rules()
    {
        return [
            'remind_at' => 'sometimes|date|after_or_equal:now',
            'channel' => 'sometimes|in:in_app,email',
            'is_sent' => 'sometimes|boolean',
        ];
    }
}