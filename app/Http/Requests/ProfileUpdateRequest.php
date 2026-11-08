<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\UploadedFile;
use Illuminate\Validation\Rule;
use Closure;

class ProfileUpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                Rule::unique(User::class)->ignore($this->user()->id),
            ],
            'avatar' => [
                'nullable',
                function (string $attribute, mixed $value, Closure $fail): void {
                    if (!$value) {
                        return;
                    }

                    if (!$value instanceof UploadedFile || !$value->isValid()) {
                        $fail('Không thể tải ảnh lên. Vui lòng chọn lại tệp ảnh.');
                        return;
                    }

                    if ($value->getSize() > 2 * 1024 * 1024) {
                        $fail('Ảnh đại diện không được lớn hơn 2MB.');
                        return;
                    }

                    $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
                    $extension = strtolower($value->getClientOriginalExtension());
                    $imageInfo = @getimagesize($value->getRealPath());

                    if (!in_array($extension, $allowedExtensions, true) || $imageInfo === false) {
                        $fail('Ảnh đại diện phải là tệp JPG, PNG, GIF hoặc WEBP hợp lệ.');
                    }
                },
            ],
            'remove_avatar' => ['sometimes', 'boolean'],
            'academic_title' => ['nullable', 'string', 'max:100'],
            'bio' => ['nullable', 'string', 'max:2000'],
            'class_name' => ['nullable', 'string', 'max:100'],
            'study_year' => ['nullable', 'string', 'max:50'],
            'academic_year' => ['nullable', 'string', 'max:20', 'regex:/^\d{4}(-\d{4})?$/'],
        ];
    }
}
