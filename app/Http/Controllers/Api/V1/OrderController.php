<?php

namespace App\Http\Controllers\Api\V1;

use App\Commands\PlaceAnOrderCommand;
use App\Http\Controllers\Controller;
use App\Http\Requests\PlaceAnOrderValidation;
use App\Services\OrderService;
use App\Transformers\OrderDataTransformer;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class OrderController extends Controller
{
    public function __construct(
        private readonly OrderService $orderService
    ) {}

    /**
     * @throws \Exception
     */

    public function placeAnOrder(PlaceAnOrderValidation $request,OrderDataTransformer $transformer): JsonResponse
    {

        $validatedData = $request->validated();

        $command = new PlaceAnOrderCommand(
            $validatedData['type'],
            $validatedData['price'],
            $validatedData ['quantity']
        );

        $order = $this->orderService->placeAnOrder($command);

        return response()->json([
            'message' => 'Order placed successfully.',
            'order' => $transformer->transform($order)
        ], Response::HTTP_CREATED);
    }
}

