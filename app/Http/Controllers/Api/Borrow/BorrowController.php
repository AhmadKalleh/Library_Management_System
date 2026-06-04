<?php

namespace App\Http\Controllers\Api\Borrow;

use App\Http\Controllers\Controller;
use App\Http\Requests\BorrowRequests\FormRequestBorrow;
use App\Traits\ResponseHelper\ResponseHelper;
use Illuminate\Support\Facades\Auth;
use App\Services\Borrow\BorrowService;
use Illuminate\Http\JsonResponse;
use App\Http\Resources\BorrowResource;
use App\Models\Borrow;
use Throwable;

class BorrowController extends Controller
{
    use ResponseHelper;

    public function __construct(
        protected BorrowService $_borrowService
    ) {}

    public function index(FormRequestBorrow $request): JsonResponse
    {
        $data = [];

        try {
            $user     = Auth::user();
            $is_admin = $user->role === 'admin';

            // تحويل الـ status لـ array
            $statuses = [];
            if (!empty($request->validated()['status'])) {
                $statuses = explode(',', $request->validated()['status']);
            }

            $raw  = $this->_borrowService->get_borrows($statuses, $is_admin, $user->id);
            $data = [
                'borrows'    => BorrowResource::collection($raw['data']['borrows']),
                'pagination' => $raw['data']['pagination'],
            ];

            return $this->Success($data, $raw['message'], $raw['code']);

        } catch (Throwable $e) {
            return $this->Error($data, $e->getMessage());
        }
    }

    public function borrow_request(FormRequestBorrow $request): JsonResponse
    {
        $data = [];

        try {
            $this->authorize('borrow_request', Borrow::class);

            $user = Auth::user();
            $raw  = $this->_borrowService->request_borrow(
                $request->validated()['book_id'],
                $user->id
            );

            if ($raw['code'] !== 201) {
                return $this->Error($raw['data'], $raw['message'], $raw['code']);
            }

            $data = [
                'borrow' => new BorrowResource($raw['data']),
            ];

            return $this->Success($data, $raw['message'], $raw['code']);

        }
        catch (Throwable $e) {
            return $this->Error($data, $e->getMessage());
        }
    }

    public function confirm_receive(FormRequestBorrow $request): JsonResponse
    {
        $data = [];

        try {
            $this->authorize('manage', Borrow::class);

            $raw = $this->_borrowService->confirm_receive($request->validated()['borrow_id']);

            if ($raw['code'] !== 200) {
                return $this->Error($raw['data'], $raw['message'], $raw['code']);
            }

            return $this->Success([], $raw['message'], $raw['code']);

        } catch (Throwable $e) {
            return $this->Error($data, $e->getMessage());
        }
    }

    public function return_book(FormRequestBorrow $request): JsonResponse
    {
        $data = [];

        try {
            $this->authorize('manage', Borrow::class);

            $raw = $this->_borrowService->return_book($request->validated()['borrow_id']);

            if ($raw['code'] !== 200) {
                return $this->Error($raw['data'], $raw['message'], $raw['code']);
            }

            return $this->Success([], $raw['message'], $raw['code']);

        }  catch (Throwable $e) {
            return $this->Error($data, $e->getMessage());
        }
    }
}
