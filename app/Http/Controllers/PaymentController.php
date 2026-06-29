<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PaymentController extends Controller
{
    public function index()
    {
        return apiResponse(Payment::with('order')->get(), 200, 'Get payments successfully...');
    }

    public function show($id)
    {
        return apiResponse(Payment::with('order')->findOrFail($id), 200, 'Get payment successfully...');
    }

    public function store(Request $req)
    {
        $validator = Validator::make($req->all(), [
            'order_id' => 'required|exists:orders,id',
            'payment_method' => 'required|string',
            'payment_status' => 'nullable|string',
            'transaction_id' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return apiResponse($validator->errors(), 422, 'Validation failed.');
        }

        $data = $validator->validated();
        $payment = Payment::create($data);

        return apiResponse($payment, 201, 'Add payment successfully...');
    }

    public function update(Request $req, $id)
    {
        $payment = Payment::findOrFail($id);
        $validator = Validator::make($req->all(), [
            'order_id' => 'sometimes|required|exists:orders,id',
            'payment_method' => 'sometimes|required|string',
            'payment_status' => 'sometimes|required|string',
            'transaction_id' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return apiResponse($validator->errors(), 422, 'Validation failed.');
        }

        $data = $validator->validated();
        $payment->update($data);

        return apiResponse($payment, 200, 'Update payment successfully...');
    }

    public function destroy($id)
    {
        Payment::findOrFail($id)->delete();

        return apiResponse(null, 200, 'Delete payment successfully...');
    }
}
