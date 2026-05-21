<?php

namespace App\Http\Requests\AuthRequests;

use App\Traits\ResponseHelper\ResponseHelper;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class FormRequestAuth extends FormRequest
{
    use ResponseHelper;
    /**
     * Determine if the user is authorized to make this request.
     */
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
        return match ($this->method()) {
            'POST' => match ($this->route()->getActionMethod()) {
                'register' => $this->register(),
                'verify_code' => $this->verifyCode(),
                'resend_code' => $this->resendCode(),
                'login' => $this->login(),
                'logout' => [],
                default => []
            },
            default => []
        };
    }

    public function register(): array
    {
        return
        [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'mobile' => 'required|string|phone:US-SY,mobile,AUTO|unique:users,mobile',
            'password' => 'required|string|min:6|confirmed',

        ];
    }

    public function verifyCode(): array
    {
        return
        [
            'email' => 'required|email|exists:users,email',
            'code' => 'required|string|size:6',
        ];
    }

    public function resendCode(): array
    {
        return
        [
            'email' => 'required|email|exists:users,email',
        ];
    }


    protected function prepareForValidation()
    {

        if ($this->method() === 'POST'
        && $this->route()->getActionMethod() === 'register')
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

    public function login(): array
    {
        return [
            'email' => 'required|email',
            'password' => 'required|string|min:6',
    ];
    
    }
    public function logout(): array
    {
        return [];
    }
}
