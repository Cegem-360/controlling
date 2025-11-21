<?php

declare(strict_types=1);

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

final class UserSyncRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, array<string>>
     */
    public function rules(): array
    {
        return [
            'email' => ['required', 'email', 'exists:users,email'],
            'new_email' => ['sometimes', 'email', 'unique:users,email'],
            'password_hash' => ['sometimes', 'string'],
            'role' => ['sometimes', 'string', 'in:subscriber,manager'],
        ];
    }
}
