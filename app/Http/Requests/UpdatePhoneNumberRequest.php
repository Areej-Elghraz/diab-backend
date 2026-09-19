<?php

namespace App\Http\Requests;

use App\Enums\PhoneNumberTypeEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class UpdatePhoneNumberRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'phone'  => 'sometimes|nullable|unique:phone_numbers,phone|phone:AUTO',
            'type'   => 'sometimes|nullable|array|min:1',
            'type.*' => ['required', new Enum(PhoneNumberTypeEnum::class)],
            'primary' => 'sometimes|boolean',
        ];
    }

    public function messages()
    {
        return [
            'phone.phone' => __('validation.invalid_value', ['attribute' => __('validation.attributes.phone')])
        ];
    }
}
