<?php

namespace App\Http\Controllers;

use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class OrderItemController extends Controller
{
    public function index()
    {
        return apiResponse(OrderItem::with(['order', 'product'])->get(), 200, 'Get order items successfully...');
    }

    public function show($id)
    {
        return apiResponse(OrderItem::with(['order', 'product'])->findOrFail($id), 200, 'Get order item successfully...');
    }

    public function store(Request $req)
    {
        $validator = Validator::make($req->all(), [
            'order_id' => 'required|exists:orders,id',
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer',
            'price' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return apiResponse($validator->errors(), 422, 'Validation failed.');
        }

        $data = $validator->validated();
        $orderItem = OrderItem::create($data);

        return apiResponse($orderItem, 201, 'Add order item successfully...');
    }

    public function update(Request $req, $id)
    {
        $orderItem = OrderItem::findOrFail($id);
        $validator = Validator::make($req->all(), [
            'order_id' => 'sometimes|required|exists:orders,id',
            'product_id' => 'sometimes|required|exists:products,id',
            'quantity' => 'sometimes|required|integer',
            'price' => 'sometimes|required|numeric',
        ]);

        if ($validator->fails()) {
            return apiResponse($validator->errors(), 422, 'Validation failed.');
        }

        $data = $validator->validated();
        $orderItem->update($data);

        return apiResponse($orderItem, 200, 'Update order item successfully...');
    }

    public function destroy($id)
    {
        OrderItem::findOrFail($id)->delete();

        return apiResponse(null, 200, 'Delete order item successfully...');
    }
}
