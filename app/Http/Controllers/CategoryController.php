<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
    {
        return apiResponse(Category::all(), 200, 'Get categories successfully...');
    }

    public function show($id)
    {
        return apiResponse(Category::findOrFail($id), 200, 'Get category successfully...');
    }

    public function store(Request $req)
    {
        $data = $req->validate([
            'name' => 'required',
            'slug' => 'required|unique:categories,slug',
        ]);

        $category = Category::create($data);

        return apiResponse($category, 201, 'Add category successfully...');
    }

    public function update(Request $req, $id)
    {
        $category = Category::findOrFail($id);
        $data = $req->validate([
            'name' => 'sometimes|required',
            'slug' => 'sometimes|required|unique:categories,slug,'.$category->id,
        ]);

        $category->update($data);

        return apiResponse($category, 200, 'Update category successfully...');
    }

    public function destroy($id)
    {
        Category::findOrFail($id)->delete();

        return apiResponse(null, 200, 'Delete category successfully...');
    }
}
