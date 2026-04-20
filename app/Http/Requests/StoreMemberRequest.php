<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreMemberRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        $memberId = $this->route('member')?->id;

        return [
            'name'          => 'required|string|max:100',
            'email'         => 'nullable|email|unique:members,email,' . $memberId,
            'phone'         => 'nullable|string|max:20',
            'date_of_birth' => 'nullable|date|before:today',
            'gender'        => 'nullable|in:male,female,other',
            'blood_group'   => 'nullable|string|max:5',
            'address'       => 'nullable|string|max:500',
            'photo'         => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',

            'employee_id'   => 'nullable|string|max:50|unique:members,employee_id,' . $memberId,
            'designation'   => 'nullable|string|max:100',
            'department'    => 'nullable|string|max:100',
            'organization'  => 'nullable|string|max:150',
            'joining_date'  => 'nullable|date',

            'degree'        => 'nullable|string|max:100',
            'institution'   => 'nullable|string|max:150',
            'passing_year'  => 'nullable|digits:4|integer|min:1950|max:' . (date('Y') + 1),

            'status'        => 'required|in:active,inactive',
        ];
    }
}
