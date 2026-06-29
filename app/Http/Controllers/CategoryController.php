<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\File;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::all()->map(function ($category) {
            return $this->categoryResponse($category);
        });

        return apiResponse($categories, 200, 'Get categories successfully...');
    }

    public function show($id)
    {
        $category = Category::findOrFail($id);
        return apiResponse($this->categoryResponse($category), 200, 'Get category successfully...');
    }

    public function store(Request $req)
    {
        $this->normalizeDescriptionField($req);

        $validator = Validator::make($req->all(), [
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string',
            'image'       => 'nullable|image|max:2048',
        ]);

        if ($validator->fails()) {
            return apiResponse($validator->errors(), 422, 'Validation failed.');
        }

        $data = $validator->validated();
        $data['description'] = $this->descriptionInput($req);

        if ($req->hasFile('image')) {
            $data['image'] = $this->saveImage($req);
        }

        $category = Category::create($data);
        return apiResponse($this->categoryResponse($category), 201, 'Add category successfully...');
    }

    public function update(Request $req, $id)
    {
        $category = Category::findOrFail($id);

        $this->normalizeDescriptionField($req);

        $validator = Validator::make($req->all(), [
            'name'        => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'image'       => 'nullable|image|max:2048',
        ]);

        if ($validator->fails()) {
            return apiResponse($validator->errors(), 422, 'Validation failed.');
        }

        $data = $validator->validated();

        if ($this->hasDescriptionInput($req)) {
            $data['description'] = $this->descriptionInput($req);
        }

        if ($req->hasFile('image')) {
            // Optional: Delete old physical image file before saving new one
            if ($category->image && File::exists(public_path($category->image))) {
                File::delete(public_path($category->image));
            }
            $data['image'] = $this->saveImage($req);
        }

        $category->update($data);
        return apiResponse($this->categoryResponse($category), 200, 'Update category successfully...');
    }

    public function destroy($id)
    {
        $category = Category::findOrFail($id);

        // Remove the linked image file from public storage directory
        if ($category->image && File::exists(public_path($category->image))) {
            File::delete(public_path($category->image));
        }

        $category->delete();
        return apiResponse(null, 200, 'Delete category successfully...');
    }

    private function saveImage(Request $req): string
    {
        $file     = $req->file('image');
        $filename = time() . '-' . $file->getClientOriginalName();
        $file->move(public_path('image'), $filename);

        return 'image/' . $filename;
    }

    private function normalizeDescriptionField(Request $req): void
    {
        if (!$req->has('description') && $this->hasDescriptionInput($req)) {
            $req->merge([
                'description' => $this->descriptionInput($req),
            ]);
        }
    }

    private function hasDescriptionInput(Request $req): bool
    {
        foreach ($this->descriptionKeys() as $key) {
            if ($req->has($key)) {
                return true;
            }
        }

        return false;
    }

    private function descriptionInput(Request $req): ?string
    {
        foreach ($this->descriptionKeys() as $key) {
            if ($req->has($key)) {
                return $req->input($key);
            }
        }

        return null;
    }

    private function descriptionKeys(): array
    {
        return [
            'descriptin',
        ];
    }

    private function categoryResponse(Category $category): array
    {
        return [
            'id'          => $category->id,
            'name'        => $category->name,
            'description' => $category->description,
            'image'       => $category->image ? asset($category->image) : null,
            'created_at'  => $category->created_at,
            'updated_at'  => $category->updated_at,
        ];
    }
}
