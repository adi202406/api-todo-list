<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class WorkspaceUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'user_id' => 'required|exists:users,id',
            'workspace_id' => 'required|exists:workspaces,id',
            'role' => 'nullable|string|in:owner,viewer,editor',
            'status' => 'required|string|in:active,pending,removed',
            'invited_by' => 'nullable|exists:users,id',
            'joined_at' => 'nullable|date',
        ];
    }
}
