<?php

namespace App\Http\Requests\BookRequests;

use Illuminate\Foundation\Http\FormRequest;
use App\Traits\ResponseHelper\ResponseHelper;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

class FormRequestBook extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    use ResponseHelper;
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
        return match ($this->method())
        {
            'GET' => match ($this->route()->getActionMethod())
            {
                'index' => $this->index(),
                'show_book' => $this->showBook(),
                'global_search' => $this->globalSearch(),
                'filter' => $this->filter(),
            },
            'POST' => match ($this->route()->getActionMethod())
            {
                'create_book' => $this->createBook(),
                'update_book' => $this->updateBook(),
            },
            'DELETE' => match ($this->route()->getActionMethod())
            {
                'delete_book' => $this->deleteBook(),
            },
            default => []
        };
    }

    public function filter(): array
    {
        return [
            'category_id' => 'nullable|integer|exists:categories,id',
            'status' => 'sometimes|string|in:available,unavailable,all',
        ];
    }

    public function index():array
    {
        return [
            'category_name' => 'required|string|max:255',
        ];
    }

    public function showBook(): array
    {
        return [
            'book_id' => 'required|integer|exists:books,id',
        ];
    }

    public function createBook(): array
    {
        return [
            'title' => 'required|string|max:255|unique:books,title',
            'author' => 'required|string|max:255',
            'category_id' => 'required|integer|exists:categories,id',
            'available_copies' => 'required|integer|min:1',
            'image' => 'required|file|mimes:jpeg,png,jpg,gif,svg,ico',
        ];
    }

    public function updateBook(): array
    {
        $bookId = $this->input('book_id');

        return [
            'book_id' => 'required|integer|exists:books,id',
            'title' => [
                'sometimes',
                'string',
                'max:255',
                Rule::unique('books', 'title')->ignore($bookId),
            ],
            'author' => 'sometimes|string|max:255',
            'available_copies' => 'sometimes|integer',
            'image' => 'sometimes|file|mimes:jpeg,png,jpg,gif,svg,ico',
        ];
    }

    public function deleteBook(): array
    {
        return [
            'book_id' => 'required|integer|exists:books,id',
        ];
    }

    public function globalSearch(): array
    {
        return [
            'value' => 'required|string|max:255',
        ];
    }

    public function messages(): array
    {
        return [
            'category_id.exists' => 'The selected category does not exist. Please insert this category first before creating a book.',
        ];
    }

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
