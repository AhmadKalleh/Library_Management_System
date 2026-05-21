<?php

namespace App\Repositories\Interfaces;

interface AuthRepositoryInterface
{
    public function register(array $data): array;
    public function verify_code(array $data): array;
    public function resend_code(array $data): array;
    public function login(array $data): array;
    public function logout($user): array;
}
