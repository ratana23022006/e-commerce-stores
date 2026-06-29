<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::with(['category', 'user'])->get();

        return apiResponse($products, 200, 'Get products successfully...');
    }

    public function show($id)
    {
        $product = Product::with(['category', 'user'])->findOrFail($id);

        return apiResponse($product, 200, 'Get product successfully...');
    }

    public function store(Request $req)
    {
        $data = $req->all();

        if ($req->filled('pro_name') && ! $req->filled('name')) {
            $data['name'] = $req->input('pro_name');
        }

        if ($req->filled('cat_id') && ! $req->filled('category_id')) {
            $data['category_id'] = $req->input('cat_id');
        }

        $validator = Validator::make($data, [
            'category_id' => 'required|exists:categories,id',
            'user_id' => 'required|exists:users,id',
            'name' => 'required',
            'description' => 'nullable',
            'price' => 'required|numeric',
            'stock' => 'required|integer',
            'image' => 'nullable|image|max:2048',
        ], [
            'category_id.exists' => 'The selected category does not exist. Create a category first or use an existing category id.',
            'user_id.exists' => 'The selected user does not exist. Register a user first or use an existing user id.',
        ]);

        if ($validator->fails()) {
            return apiResponse($validator->errors(), 422, 'Validation failed.');
        }

        $product = $validator->validated();

        if ($req->hasFile('image')) {
            $product['image'] = $this->saveImage($req);
        }

        $product = Product::create($product);

        return apiResponse($product,201,'Add product successfully...');
    }

    public function addProduct(Request $req)
    {
        return $this->store($req);
    }

    public function update(Request $req, $id)
    {
        $product = Product::findOrFail($id);

        $data = $req->all();

        if ($req->filled('pro_name') && ! $req->filled('name')) {
            $data['name'] = $req->input('pro_name');
        }

        if ($req->filled('cat_id') && ! $req->filled('category_id')) {
            $data['category_id'] = $req->input('cat_id');
        }

        $validator = Validator::make($data, [
            'category_id' => 'sometimes|required|exists:categories,id',
            'user_id' => 'sometimes|required|exists:users,id',
            'name' => 'sometimes|required',
            'description' => 'nullable',
            'price' => 'sometimes|required|numeric',
            'stock' => 'sometimes|required|integer',
            'image' => 'nullable|image|max:2048',
        ], [
            'category_id.exists' => 'The selected category does not exist. Create a category first or use an existing category id.',
            'user_id.exists' => 'The selected user does not exist. Register a user first or use an existing user id.',
        ]);

        if ($validator->fails()) {
            return apiResponse($validator->errors(), 422, 'Validation failed.');
        }

        $data = $validator->validated();

        if ($req->hasFile('image')) {
            if ($product->image && File::exists(public_path($product->image))) {
                File::delete(public_path($product->image));
            }

            $data['image'] = $this->saveImage($req);
        }

        $product->update($data);

        return apiResponse($product, 200, 'Update product successfully...');
    }

    public function destroy($id)
    {
        $product = Product::findOrFail($id);

        if ($product->image && File::exists(public_path($product->image))) {
            File::delete(public_path($product->image));
        }

        $product->delete();

        return apiResponse(null, 200, 'Delete product successfully...');
    }

    private function saveImage(Request $req): string
    {
        $file = $req->file('image');
        $filename = time().'-'.$file->getClientOriginalName();
        $file->move(public_path('image'), $filename);

        return 'image/'.$filename;
    }
}
