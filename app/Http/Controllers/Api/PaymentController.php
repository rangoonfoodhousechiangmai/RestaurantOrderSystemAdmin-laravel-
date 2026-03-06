<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\FileService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class PaymentController extends Controller
{
    protected $fileService;

    public function __construct(FileService $fileService)
    {
        $this->fileService = $fileService;
    }

    /**
     * Store a newly uploaded payment proof.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:5120',
            'orderCode' => 'required|string|exists:orders,order_code',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        DB::beginTransaction();
        try {
            $order = Order::where('order_code', $request->orderCode)->first();

            if (!$order) {
                return response()->json(['error' => 'Order not found'], 404);
            }

            // Check if payment already exists
            // if ($order->payment_status) {
            //     return response()->json(['error' => 'Payment already exists for this order'], 400);
            // }

            // Delete existing payment image if it exists
            if ($order->payment_image_path) {
                $this->fileService->deleteImagePrivate($order->payment_image_path);
            }

            // Store the payment image with custom path: payments/{date}/{order_code}
            $datePath = now()->format('Y-m-d');
            $customPath = 'payments/' . $datePath;
            $imagePath = $this->fileService->storeImagePrivate($request->file('image'), $customPath);

            // Update order with payment information
            $order->update([
'payment_type' => 'prompt_pay',
                'payment_status' => true,
                'payment_image_path' => $imagePath,
            ]);

            DB::commit();

            return response()->json([
                'message' => 'Payment proof uploaded successfully',
            ], 200);
        } catch (\Exception $e) {
            logger($e->getMessage());
            DB::rollBack();
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
