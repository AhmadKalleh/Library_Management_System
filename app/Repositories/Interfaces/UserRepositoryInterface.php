<?php

namespace App\Repositories\Interfaces;

interface UserRepositoryInterface {
    public function getAll(array $filters = []);
    public function findById(int $id): User;
    public function create(array $data): User;
    public function update(int $id, array $data): User;
    public function delete(int $id): bool;
    public function getDashboardStats(): array;
}