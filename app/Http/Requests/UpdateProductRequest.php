<?php

namespace App\Http\Requests;

use App\Enums\ProductRelationEnum;
use App\Traits\HasIncludeRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateProductRequest extends FormRequest
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
            'name'        => 'sometimes|nullable|string|max:100',
            'description' => 'sometimes|nullable|string',
            'price'       => 'sometimes|numeric|min:0',
            'category_id' => 'sometimes|nullable|exists:categories,id',
            'include'     => $this->includeRule(ProductRelationEnum::values()),
        ];
    }
}
