<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreAttendanceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'proof_photo' => ['required', 'image', 'max:5120'], // max 5MB
            'latitude'    => ['required', 'numeric', 'between:-90,90'],
            'longitude'   => ['required', 'numeric', 'between:-180,180'],
            'notes'       => ['nullable', 'string', 'max:500'],
        ];
    }

    public function messages(): array
    {
        return [
            'proof_photo.required' => 'Foto bukti kehadiran wajib diunggah.',
            'proof_photo.image'    => 'File harus berupa gambar.',
            'proof_photo.max'      => 'Ukuran foto maksimal 5MB.',
            'latitude.required'    => 'Data titik lokasi (latitude) wajib disertakan.',
            'longitude.required'   => 'Data titik lokasi (longitude) wajib disertakan.',
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
