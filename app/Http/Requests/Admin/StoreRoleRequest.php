<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreRoleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->hasRole('admin') ?? false;
    }

    public function rules(): array
    {
        return [
            'name'        => ['required','string','max:255', Rule::unique('roles','name')->where(fn($q)=>$q->where('guard_name','web'))],
            'permissions' => ['nullable','array'],
            'permissions.*' => ['string','exists:permissions,name'],
            'guard_name'  => ['nullable','in:web'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'guard_name' => 'web',
        ]);
    }
}
