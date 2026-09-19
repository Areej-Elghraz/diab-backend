<?php

namespace App\Http\Requests;

use App\Enums\ProductImagePositionEnum;
use App\Enums\ProductRelationEnum;
use App\Traits\HasIncludeRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class StoreProductRequest extends FormRequest
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
            'name'        => 'required|string|max:100',
            'description' => 'sometimes|nullable|string',
            'price'       => 'required|numeric|min:0',
            'category_id' => 'required|integer|exists:categories,id',
            'images'      => 'required|array|min:2',
            'images.*'    => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'positions'   => 'required|array|size:' . count($this->images),
            'positions.*' => ['required', new Enum(ProductImagePositionEnum::class)],
            'include'     => $this->includeRule(ProductRelationEnum::values()),
        ];
    }
}
