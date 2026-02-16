<?php

namespace App\Http\Controllers;

use App\Models\Fee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class FeeController extends Controller
{
    public function index(Request $request)
    {
        $query = Fee::with(['student']);

        if ($request->has('student_id')) {
            $query->where('student_id', $request->student_id);
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('academic_year')) {
            $query->where('academic_year', $request->academic_year);
        }

        $fees = $query->latest()->paginate($request->per_page ?? 15);
        return response()->json(['success' => true, 'data' => $fees]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'student_id' => 'required|exists:students,id',
            'fee_type' => 'required|string',
            'amount' => 'required|numeric|min:0',
            'due_date' => 'required|date',
            'academic_year' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $data = $request->all();
        $data['paid_amount'] = $data['paid_amount'] ?? 0;
        $data['pending_amount'] = $data['amount'] - $data['paid_amount'];
        $data['status'] = $data['paid_amount'] >= $data['amount'] ? 'Paid' : 'Pending';

        $fee = Fee::create($data);
        return response()->json(['success' => true, 'message' => 'Fee created successfully', 'data' => $fee->load('student')], 201);
    }

    public function show($id)
    {
        $fee = Fee::with(['student'])->find($id);
        if (!$fee) {
            return response()->json(['success' => false, 'message' => 'Fee not found'], 404);
        }
        return response()->json(['success' => true, 'data' => $fee]);
    }

    public function update(Request $request, $id)
    {
        $fee = Fee::find($id);
        if (!$fee) {
            return response()->json(['success' => false, 'message' => 'Fee not found'], 404);
        }

        $validator = Validator::make($request->all(), [
            'amount' => 'sometimes|required|numeric|min:0',
            'paid_amount' => 'sometimes|required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $fee->update($request->all());
        return response()->json(['success' => true, 'message' => 'Fee updated successfully', 'data' => $fee]);
    }

    public function destroy($id)
    {
        $fee = Fee::find($id);
        if (!$fee) {
            return response()->json(['success' => false, 'message' => 'Fee not found'], 404);
        }
        $fee->delete();
        return response()->json(['success' => true, 'message' => 'Fee deleted successfully']);
    }

    // Record payment
    public function recordPayment(Request $request, $id)
    {
        $fee = Fee::find($id);
        if (!$fee) {
            return response()->json(['success' => false, 'message' => 'Fee not found'], 404);
        }

        $validator = Validator::make($request->all(), [
            'payment_amount' => 'required|numeric|min:0',
            'payment_method' => 'required|string',
            'transaction_id' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $fee->paid_amount += $request->payment_amount;
        $fee->payment_method = $request->payment_method;
        $fee->transaction_id = $request->transaction_id;
        $fee->paid_date = now();
        $fee->save();

        return response()->json(['success' => true, 'message' => 'Payment recorded successfully', 'data' => $fee]);
    }
}
