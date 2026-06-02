<?php

namespace App\Http\Requests\UserRequests;

use Illuminate\Foundation\Http\FormRequest;
use App\Traits\ResponseHelper\ResponseHelper;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

class FormRequestUser extends FormRequest
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
                'index'     => $this->index(),
                'show_user' => $this->showUser(),
            },
            'POST' => match ($this->route()->getActionMethod())
            {
                'create_user' => $this->createUser(),
                'update_user' => $this->updateUser(),
            },
            'DELETE' => match ($this->route()->getActionMethod())
            {
                'delete_user' => $this->deleteUser(),
            },
            default => []
        };
    }

    public function index(): array
    {
        return [
            'role'   => 'sometimes|string|in:admin,user',
            'status' => 'sometimes|string|in:active,inactive',
            'search' => 'sometimes|string|max:255',
        ];
    }

    public function showUser(): array
    {
        return [
            'user_id' => 'required|integer|exists:users,id',
        ];
    }

    public function createUser(): array
    {
        return [
            'name'                  => 'required|string|max:255',
            'email'                 => 'required|email|unique:users,email',
            'password'              => 'required|string|min:8|confirmed',
            'role'                  => 'required|string|in:admin,user',
            'status'                => 'sometimes|string|in:active,inactive',
        ];
    }

    public function updateUser(): array
    {
        $userId = $this->input('user_id');

        return [
            'user_id'               => 'required|integer|exists:users,id',
            'name'                  => 'sometimes|string|max:255',
            'email'                 => [
                'sometimes',
                'email',
                Rule::unique('users', 'email')->ignore($userId),
            ],
            'password'              => 'sometimes|string|min:8|confirmed',
            'role'                  => 'sometimes|string|in:admin,user',
            'status'                => 'sometimes|string|in:active,inactive',
        ];
    }

    public function deleteUser(): array
    {
        return [
            'user_id' => 'required|integer|exists:users,id',
        ];
    }

    public function messages(): array
    {
        return [
            'user_id.exists'   => 'The selected user does not exist.',
            'role.in'          => 'Role must be either admin or user.',
            'status.in'        => 'Status must be either active or inactive.',
            'email.unique'     => 'This email is already taken.',
            'password.min'     => 'Password must be at least 8 characters.',
            'password.confirmed' => 'Password confirmation does not match.',
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