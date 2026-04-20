<?php
// app/Http/Requests/StoreCircularRequest.php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCircularRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'title'            => ['required', 'string', 'max:255'],
            'reference_number' => ['nullable', 'string', 'max:100', 'unique:circulars,reference_number'],
            'body'             => ['required', 'string'],
            'type'             => ['required', 'in:notice,circular,memo'],
            'status'           => ['required', 'in:draft,published'],
            'issued_date'      => ['required', 'date'],
            'attachments'      => ['nullable', 'array', 'max:5'],
            'attachments.*'    => ['file', 'max:10240', 'mimes:pdf,doc,docx,xls,xlsx,jpg,jpeg,png'],
        ];
    }
}
