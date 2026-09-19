<?php

namespace App\Http\Requests;

use App\Enums\PhoneNumberTypeEnum;
use App\Traits\HasIncludeRule;
use Illuminate\Foundation\Http\FormRequest;

class IndexPhoneNumberRequest extends FormRequest
{
    use HasIncludeRule;
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
            'type' => $this->includeRule(PhoneNumberTypeEnum::values()),
            'primary' => 'sometimes|boolean',
        ];
    }
}
