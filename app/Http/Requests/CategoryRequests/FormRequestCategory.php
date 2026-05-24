<?php

namespace App\Http\Requests\CategoryRequests;

use Illuminate\Foundation\Http\FormRequest;
use App\Traits\ResponseHelper\ResponseHelper;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

class FormRequestCategory extends FormRequest
{
    use ResponseHelper;

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return match ($this->method())
        {
            'GET' => match ($this->route()->getActionMethod())
            {
                'search_category' => $this->searchCategory(),
            },

            'POST' => match ($this->route()->getActionMethod())
            {
                'create_category' => $this->createCategory(),
                'update_category' => $this->updateCategory(),
            },

            'DELETE' => match ($this->route()->getActionMethod())
            {
                'delete_category' => $this->deleteCategory(),
            },

            default => []
        };
    }

    /* ================= CREATE ================= */
    public function createCategory(): array
    {
        return [
            'name' => 'required|string|max:255|unique:categories,name',
            'description' => 'nullable|string|max:1000',
        ];
    }

    /* ================= UPDATE ================= */
    public function updateCategory(): array
    {
        $categoryId = $this->input('category_id');

        return [
            'category_id' => 'required|integer|exists:categories,id',

            'name' => [
                'sometimes',
                'string',
                'max:255',
                Rule::unique('categories', 'name')->ignore($categoryId),
            ],

            'description' => 'sometimes|nullable|string|max:1000',
        ];
    }

    /* ================= DELETE ================= */
    public function deleteCategory(): array
    {
        return [
            'category_id' => 'required|integer|exists:categories,id',
        ];
    }

    /* ================= SEARCH ================= */
    public function searchCategory(): array
    {
        return [
            'value' => 'required|string|max:255',
        ];
    }

    /* ================= CUSTOM MESSAGES ================= */
    public function messages(): array
    {
        return [
            'name.unique' => 'Category name already exists.',
            'category_id.exists' => 'This category does not exist.',
        ];
    }

    /* ================= ERROR RESPONSE ================= */
    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            $this->Validation(
                [],
                $validator->errors()->first(),
                422
            )
        );
    }
}