<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Requests\Order\PlaceOrderValidation;
use App\Services\OrderService;
use App\Services\OrderValidationService;
use App\Commands\PlaceOrderCommand;
use App\Transformers\OrderDataTransformer;
use Illuminate\Http\JsonResponse;

class OrderController extends Controller
{
    public function __construct(
        private readonly OrderService $orderService
    ) {}

    public function placeOrder(PlaceOrderValidation $request, OrderDataTransformer $transformer): JsonResponse
    {
        $validatedData = $request->validationData();

        $command = new PlaceOrderCommand(
            $validatedData['type'],
            $validatedData['price'],
            $validatedData ['quantity']
        );

        $order = $this->orderService->placeAnOrder($command);

        return response()->json([
            'message' => 'Order placed successfully.',
            'order' => $transformer->transform($order)
        ], 201);
    }
}

