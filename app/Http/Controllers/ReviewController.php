<?php

namespace App\Http\Controllers;

use App\Models\Review;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function index()
    {
        return apiResponse(Review::with(['user', 'product'])->get(), 200, 'Get reviews successfully...');
    }

    public function show($id)
    {
        return apiResponse(Review::with(['user', 'product'])->findOrFail($id), 200, 'Get review successfully...');
    }

    public function store(Request $req)
    {
        $data = $req->validate([
            'user_id' => 'required|exists:users,id',
            'product_id' => 'required|exists:products,id',
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string',
        ]);

        $review = Review::create($data);

        return apiResponse($review, 201, 'Add review successfully...');
    }

    public function update(Request $req, $id)
    {
        $review = Review::findOrFail($id);
        $data = $req->validate([
            'user_id' => 'sometimes|required|exists:users,id',
            'product_id' => 'sometimes|required|exists:products,id',
            'rating' => 'sometimes|required|integer|min:1|max:5',
            'comment' => 'nullable|string',
        ]);

        $review->update($data);

        return apiResponse($review, 200, 'Update review successfully...');
    }

    public function destroy($id)
    {
        Review::findOrFail($id)->delete();

        return apiResponse(null, 200, 'Delete review successfully...');
    }
}
