<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

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
        $validator = Validator::make($req->all(), [
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
        ]);

        if ($validator->fails()) {
            return apiResponse($validator->errors(), 422, 'Validation failed.');
        }

        $data = $validator->validated();
        $data['user_id'] = $req->user()->id;
        $cart = Cart::create($data);

        return apiResponse($cart, 201, 'Add cart successfully...');
    }

    public function update(Request $req, $id)
    {
        $cart = Cart::findOrFail($id);
        $validator = Validator::make($req->all(), [
            'user_id' => 'sometimes|required|exists:users,id',
            'product_id' => 'sometimes|required|exists:products,id',
            'quantity' => 'sometimes|required|integer|min:1',
        ]);

        if ($validator->fails()) {
            return apiResponse($validator->errors(), 422, 'Validation failed.');
        }

        $data = $validator->validated();
        $cart->update($data);

        return apiResponse($cart, 200, 'Update cart successfully...');
    }

    public function destroy($id)
    {
        Cart::findOrFail($id)->delete();

        return apiResponse(null, 200, 'Delete cart successfully...');
    }
}
