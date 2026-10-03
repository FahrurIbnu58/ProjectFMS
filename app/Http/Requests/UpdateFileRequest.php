<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateFileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'folder_id' => ['sometimes', 'integer', 'exists:folders,id'],
            'department_id' => ['sometimes', 'integer', 'exists:departments,id'],
            'title' => ['sometimes', 'string', 'max:255'],
            'file' => ['sometimes', 'file', 'max:20480'],
        ];
    }
}
