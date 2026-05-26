<?php

namespace App\Http\Requests\BorrowRequests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use App\Traits\ResponseHelper\ResponseHelper;

class FormRequestBorrow extends FormRequest
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
            },
            'POST' => match ($this->route()->getActionMethod())
            {
                'borrow_request' => $this->borrowRequest(),
                'confirm_receive' => $this->confirmReceive(),
                'return_book' => $this->returnBook(),
            },
            default => []
        };
    }

    public function index(): array
    {
        return [
            'status' => 'required|string|in:pending,borrowed,cancelled,returned,overdue,all',
        ];
    }

    public function confirmReceive(): array
    {
        return [
            'borrow_id' => 'required|integer|exists:borrows,id',
        ];
    }

    public function returnBook(): array
    {
        return [
            'borrow_id' => 'required|integer|exists:borrows,id',
        ];
    }

    public function borrowRequest(): array
    {
        return [
            'book_id' => 'required|integer|exists:books,id',
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
