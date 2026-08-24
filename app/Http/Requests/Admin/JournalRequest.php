<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class JournalRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $id = $this->route('journal');
        if ($id instanceof \App\Models\Journal) {
            $id = $id->id;
        }

        return [
            'nama' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:100', Rule::unique('journals', 'slug')->ignore($id)],
            'deskripsi' => ['nullable', 'string'],
            'e_issn' => ['nullable', 'string', 'max:20'],
            'cover' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],
            'link_eksternal' => ['required', 'url', 'max:255'],
            'status' => ['required', 'in:aktif,arsip'],
        ];
    }
}
