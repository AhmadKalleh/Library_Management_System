<?php

namespace App\Repositories\Interfaces;

interface UserRepositoryInterface
{
    public function index(array $data = []): array;
    public function show_user(int $user_id): array;
    public function create(array $data): array;
    public function update(int $user_id, array $data): array;
    public function delete(int $user_id): array;
    public function getDashboardStats(): array;
    public function getHomepageStats(int $user_id):array;
    public function show_profile(): array;
    public function active_user(int $user_id): array;
    public function inactive_user(int $user_id): array;
}
