<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class RespondSwapRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'action'             => ['required', 'in:accept,cancel'],
            'target_schedule_id' => ['required_if:action,accept', 'integer', 'exists:schedules,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'action.required'              => 'Aksi wajib ditentukan (accept/cancel).',
            'action.in'                    => 'Aksi harus berupa accept atau cancel.',
            'target_schedule_id.required_if' => 'Jadwal pengganti wajib dipilih untuk menerima swap.',
        ];
    }

    protected function failedValidation(Validator $validator): void
    {
        throw new HttpResponseException(response()->json([
            'success' => false,
            'data'    => null,
            'message' => 'Validasi gagal',
            'errors'  => $validator->errors(),
        ], 422));
    }
}
