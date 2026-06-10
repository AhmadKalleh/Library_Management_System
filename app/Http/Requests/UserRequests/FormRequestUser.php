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
                'activate'    => $this->showUser(),
                'ban'         => $this->showUser(),
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
            'status' => 'sometimes|string|in:active,banned,all',
            'search' => 'sometimes|nullable|string',
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
            'mobile'                => 'required|string|unique:users,mobile',
            'password'              => 'required|string|min:8|confirmed',
            'role'                  => 'required|string|in:admin,user',
            'image'                 => 'required|file|mimes:jpeg,png,jpg,gif,svg,ico',
        ];
    }

    public function updateUser(): array
    {
        $userId = $this->input('user_id');

        return [
            'name'                  => 'sometimes|string|max:255',
            'mobile'                => [
                'sometimes',
                'string',
                Rule::unique('users', 'mobile')->ignore($userId),
                'phone:US-SY,mobile,AUTO'
            ],
            'password'              => 'sometimes|string|min:8|confirmed',
            'role'                  => 'sometimes|string|in:admin,user',
            'image'                 => 'sometimes|file|mimes:jpeg,png,jpg,gif,svg,ico',
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
            'status.in'        => 'Status must be either active or banned.',
            'email.unique'     => 'This email is already taken.',
            'password.min'     => 'Password must be at least 8 characters.',
            'password.confirmed' => 'Password confirmation does not match.',
            'mobile.unique'    => 'This mobile number is already taken.',
            'mobile.phone'     => 'The mobile number format is invalid.',
        ];
    }

    protected function prepareForValidation()
    {

        if ($this->method() === 'POST'
        && ($this->route()->getActionMethod() === 'create_user'
        || $this->route()->getActionMethod() === 'update_user'))
        {
                $this->merge([
                'name' => trim($this->name),
                'password' => trim($this->password),
                'mobile' => $this->normalizePhone($this->mobile),
            ]);
        }
    }

    private function normalizePhone(string $mobile): string
    {

        $cleanPhone = preg_replace('/[^0-9]/', '', $mobile);

        if (str_starts_with($cleanPhone, '0')) {
            $cleanPhone = substr($cleanPhone, 1);
        }

        $fullPhone = '+963' . $cleanPhone;

        return $fullPhone;
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
