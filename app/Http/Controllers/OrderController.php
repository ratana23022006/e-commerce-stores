<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class OrderController extends Controller
{
    public function index()
    {
        $orders = Order::with(['user', 'items.product', 'payment'])->get();

        return apiResponse($orders, 200, 'Get orders successfully...');
    }

    public function show($id)
    {
        $order = Order::with(['user', 'items.product', 'payment'])->findOrFail($id);

        return apiResponse($order, 200, 'Get order successfully...');
    }

    public function store(Request $req)
    {
        $validator = Validator::make($req->all(), [
            'user_id' => 'required|exists:users,id',
            'total_price' => 'required|numeric',
            'status' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return apiResponse($validator->errors(), 422, 'Validation failed.');
        }

        $data = $validator->validated();
        $order = Order::create($data);

        return apiResponse($order, 201, 'Add order successfully...');
    }

    public function update(Request $req, $id)
    {
        $order = Order::findOrFail($id);
        $validator = Validator::make($req->all(), [
            'user_id' => 'sometimes|required|exists:users,id',
            'total_price' => 'sometimes|required|numeric',
            'status' => 'sometimes|required|string',
        ]);

        if ($validator->fails()) {
            return apiResponse($validator->errors(), 422, 'Validation failed.');
        }

        $data = $validator->validated();
        $order->update($data);

        return apiResponse($order, 200, 'Update order successfully...');
    }

    public function destroy($id)
    {
        Order::findOrFail($id)->delete();

        return apiResponse(null, 200, 'Delete order successfully...');
    }
}
