<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->hasRole('admin') ?? false;
    }

    public function rules(): array
    {
        $userId = $this->route('user')->id;

        return [
            'name'              => ['required','string','max:255'],
            'username'          => ['nullable','string','max:255', Rule::unique('users','username')->ignore($userId)],
            'email'             => ['required','email','max:255', Rule::unique('users','email')->ignore($userId)],
            'password'          => ['nullable','string','min:8','confirmed'],
            'roles'             => ['required','array','min:1'],
            'roles.*'           => ['string','exists:roles,name'],
            'email_verified_at' => ['nullable','boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'roles.required' => __('Debes asignar al menos un rol.'),
        ];
    }
}