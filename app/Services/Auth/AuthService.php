<?php

namespace App\Services\Auth;

use App\Jobs\SendVerificationCodeJob;
use App\Repositories\Interfaces\AuthRepositoryInterface;

class AuthService
{
    public function __construct(
        protected AuthRepositoryInterface $_authRepository
    ) {}


    public function register(array $data): array
    {
        $newUser = $this->_authRepository->register($data);

        if ($newUser)
        {
            SendVerificationCodeJob::dispatch(
                $newUser['user']->email,
                $newUser['user']->verification_code
            );
            $result = [
                'name' => $newUser['user']->name,
                'email' => $newUser['user']->email,
                'mobile' => $newUser['user']->mobile,
            ];

        } else {
            return [
                'data'    => null,
                'message' => 'Failed to register user',
                'code'    => 500,
            ];
        }

        return [
            'data'    => $result,
            'message' => 'Verification code sent to your email',
            'code'    => 201,
        ];

    }

    public function verify_code(array $data): array
    {
        $result = $this->_authRepository->verify_code($data);

        return match($result['status']) {
            'max_attempts' => [
                'data'    => [],
                'message' => 'You have exceeded the maximum number of attempts. Please resend the code.',
                'code'    => 429,
            ],
            'expired' => [
                'data'    => [],
                'message' => 'The verification code has expired. Please resend the code.',
                'code'    => 410,
            ],

            'wrong_code' => $this->wrongCodeResponse($result),

            'verified' => [
                'data'    => [
                    'name' => $result['user']->name,
                    'email' => $result['user']->email,
                    'mobile' => $result['user']->mobile,
                    'token' => $result['token'],
                ],
                'message' => 'Verification completed successfully.',
                'code'    => 200,
            ],
        };
    }

    private function wrongCodeResponse(array $result): array
    {
        $remaining = $result['remaining'];

        $message = match (true) {
            $remaining == 0 => 'Incorrect code. This is your last attempt.',
            default => "Incorrect code. You have {$remaining} attempts remaining.",
        };

        return [
            'data'    => [],
            'message' => $message,
            'code'    => 400,
        ];
    }

    public function resend_code(array $data): array
    {
        $result = $this->_authRepository->resend_code($data);

        SendVerificationCodeJob::dispatch(
            $result['user']->email,
            $result['user']->verification_code
        );

        return [
            'data'    => [],
            'message' => 'A new verification code has been sent to your email.',
            'code'    => 200,
        ];
    }
}
