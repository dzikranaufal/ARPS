<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $id = $this->route('category');
        if ($id instanceof \App\Models\Category) {
            $id = $id->id;
        }

        return [
            'nama' => ['required', 'string', 'max:100', Rule::unique('categories', 'nama')->ignore($id)],
        ];
    }
}
