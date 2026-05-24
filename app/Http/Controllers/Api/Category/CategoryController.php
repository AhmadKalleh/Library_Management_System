<?php

namespace App\Http\Controllers\Api\Category;

use App\Http\Controllers\Controller;
use App\Http\Requests\CategoryRequests\FormRequestCategory;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
use App\Services\Category\CategoryService;
use App\Traits\ResponseHelper\ResponseHelper;
use Illuminate\Http\JsonResponse;
use Throwable;

class CategoryController extends Controller
{
    use ResponseHelper;

    public function __construct(
        protected CategoryService $_categoryService
    ) {}

    public function index(): JsonResponse
    {
        $data = [];

        try {
            $this->authorize('viewAny', Category::class);

            $raw = $this->_categoryService->get_all_categories();

            $data = [
                'categories' => CategoryResource::collection($raw['data']['categories']),
            ];

            return $this->Success($data, $raw['message'], $raw['code']);

        } catch (Throwable $e) {
            return $this->Error($data, $e->getMessage());
        }
    }

    public function search_category(FormRequestCategory $request): JsonResponse
    {
        $data = [];

        try {
            $this->authorize('view', Category::class);

            $raw = $this->_categoryService->search_categories($request->validated());

            $data = [
                'categories' => CategoryResource::collection($raw['data']['categories']),
            ];

            return $this->Success($data, $raw['message'], $raw['code']);

        } catch (Throwable $e) {
            return $this->Error($data, $e->getMessage());
        }
    }

    public function create_category(FormRequestCategory $request): JsonResponse
    {
        $data = [];

        try {
            $this->authorize('create', Category::class);

            $raw = $this->_categoryService->create_category($request->validated());

            $data = [
                'category' => new CategoryResource($raw['data']['category']),
            ];

            return $this->Success($data, $raw['message'], $raw['code']);

        } catch (\Illuminate\Auth\Access\AuthorizationException) {
            return $this->Error($data, 'Unauthorized', 403);

        } catch (Throwable $e) {
            return $this->Error($data, $e->getMessage());
        }
    }

    public function update_category(FormRequestCategory $request): JsonResponse
    {
        $data = [];

        try {
            $this->authorize('update', Category::class);

            $raw = $this->_categoryService->update_category($request->validated());

            $data = [
                'category' => new CategoryResource($raw['data']['category']),
            ];

            return $this->Success($data, $raw['message'], $raw['code']);

        } catch (\Illuminate\Auth\Access\AuthorizationException) {
            return $this->Error($data, 'Unauthorized', 403);

        } catch (Throwable $e) {
            return $this->Error($data, $e->getMessage());
        }
    }

    public function delete_category(FormRequestCategory $request): JsonResponse
    {
        $data = [];

        try {
            $this->authorize('delete', Category::class);

            $raw = $this->_categoryService->delete_category($request->validated());

            return $this->Success($data, $raw['message'], $raw['code']);

        } catch (\Illuminate\Auth\Access\AuthorizationException) {
            return $this->Error($data, 'Unauthorized', 403);

        } catch (Throwable $e) {
            return $this->Error($data, $e->getMessage());
        }
    }
}