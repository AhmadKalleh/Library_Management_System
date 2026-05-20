<?php

namespace App\Http\Controllers\Api\Auth;


use App\Http\Requests\AuthRequests\FormRequestAuth;
use App\Services\Auth\AuthService;
use Illuminate\Http\JsonResponse;

use App\Traits\ResponseHelper\ResponseHelper;
use Throwable;

class AuthController
{
    use ResponseHelper;

    public function __construct(
        protected AuthService $_authService
    ) {}

    public function register(FormRequestAuth $request): JsonResponse
    {
        $data=[];

        try
        {
            //$this->authorize('create', Book::class);
            $data = $this->_authService->register($request->validated());
            return $this->Success($data['data'],$data['message'],$data['code']);
        }
        catch(Throwable $e)
        {
            $message = $e->getMessage();
            return $this->Error($data,$message);
        }
    }

    public function verify_code(FormRequestAuth $request): JsonResponse
    {
        $data=[];

        try
        {
            $data = $this->_authService->verify_code($request->validated());
            return $this->Success($data['data'],$data['message'],$data['code']);
        }
        catch(Throwable $e)
        {
            $message = $e->getMessage();
            return $this->Error($data,$message);
        }
    }

    public function resend_code(FormRequestAuth $request): JsonResponse
    {
        $data = [];

        try {
            $raw = $this->_authService->resend_code($request->validated());
            return $this->Success($raw['data'], $raw['message'], $raw['code']);

        } catch (Throwable $e) {
            return $this->Error($data, $e->getMessage());
        }
    }

}
