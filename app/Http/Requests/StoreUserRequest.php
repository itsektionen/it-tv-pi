<?php

namespace App\Http\Requests;

use App\Enums\Permission;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class StoreUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $user = $this->route('user');

        return [
            'name' => ['required', 'string', 'max:255', Rule::unique('users')->ignore($user)],
            // Password::defaults() should be used here but we don't really care since the login will be unsafe anyways
            'password' => [$user ? 'nullable' : 'required', 'string', 'min:6'],
            'permission' => ['required', Rule::enum(Permission::class)],
        ];
    }
}
