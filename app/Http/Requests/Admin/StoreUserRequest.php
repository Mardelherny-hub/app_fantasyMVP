<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->hasRole('admin') ?? false;
    }

    public function rules(): array
    {
        return [
            'name'              => ['required','string','max:255'],
            'username'          => ['nullable','string','max:255','unique:users,username'],
            'email'             => ['required','email','max:255','unique:users,email'],
            'password'          => ['required','string','min:8','confirmed'],
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
