<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\AuthenticatedRequest;
use App\Services\DashboardService;
use Illuminate\Http\JsonResponse;

class DashboardController extends Controller
{
    public function __invoke(AuthenticatedRequest $request, DashboardService $service): JsonResponse
    {
        return response()->json($service->forUser($request->user()));
    }
}
