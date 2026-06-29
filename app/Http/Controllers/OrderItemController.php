<?php

namespace App\Http\Controllers;

use App\Models\OrderItem;
use Illuminate\Http\Request;

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
        $data = $req->validate([
            'order_id' => 'required|exists:orders,id',
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer',
            'price' => 'required|numeric',
        ]);

        $orderItem = OrderItem::create($data);

        return apiResponse($orderItem, 201, 'Add order item successfully...');
    }

    public function update(Request $req, $id)
    {
        $orderItem = OrderItem::findOrFail($id);
        $data = $req->validate([
            'order_id' => 'sometimes|required|exists:orders,id',
            'product_id' => 'sometimes|required|exists:products,id',
            'quantity' => 'sometimes|required|integer',
            'price' => 'sometimes|required|numeric',
        ]);

        $orderItem->update($data);

        return apiResponse($orderItem, 200, 'Update order item successfully...');
    }

    public function destroy($id)
    {
        OrderItem::findOrFail($id)->delete();

        return apiResponse(null, 200, 'Delete order item successfully...');
    }
}
