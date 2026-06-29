<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function index()
    {
        return apiResponse(Cart::with(['user', 'product'])->get(), 200, 'Get carts successfully...');
    }

    public function show($id)
    {
        return apiResponse(Cart::with(['user', 'product'])->findOrFail($id), 200, 'Get cart successfully...');
    }

    public function store(Request $req)
    {
        $data = $req->validate([
            'user_id' => 'required|exists:users,id',
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $cart = Cart::create($data);

        return apiResponse($cart, 201, 'Add cart successfully...');
    }

    public function update(Request $req, $id)
    {
        $cart = Cart::findOrFail($id);
        $data = $req->validate([
            'user_id' => 'sometimes|required|exists:users,id',
            'product_id' => 'sometimes|required|exists:products,id',
            'quantity' => 'sometimes|required|integer|min:1',
        ]);

        $cart->update($data);

        return apiResponse($cart, 200, 'Update cart successfully...');
    }

    public function destroy($id)
    {
        Cart::findOrFail($id)->delete();

        return apiResponse(null, 200, 'Delete cart successfully...');
    }
}
