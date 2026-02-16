<?php

namespace App\Http\Controllers;

use App\Models\Enrollment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class EnrollmentController extends Controller
{
    public function index(Request $request)
    {
        $query = Enrollment::with(['student', 'class']);

        if ($request->has('student_id')) {
            $query->where('student_id', $request->student_id);
        }

        if ($request->has('class_id')) {
            $query->where('class_id', $request->class_id);
        }

        if ($request->has('academic_year')) {
            $query->where('academic_year', $request->academic_year);
        }

        $enrollments = $query->latest()->paginate($request->per_page ?? 15);
        return response()->json(['success' => true, 'data' => $enrollments]);
    }

  public function store(Request $request)
{
    $validator = Validator::make($request->all(), [
        'student_id' => 'required|exists:students,id',
        'class_id' => 'required|exists:classes,id',
        'enrollment_date' => 'required|date',
        'academic_year' => 'required|string',
        'status' => 'required|in:Active,Completed,Dropped',
        'remarks' => 'nullable|string',
    ]);

    if ($validator->fails()) {
        return response()->json([
            'success' => false,
            'errors' => $validator->errors()
        ], 422);
    }

    $enrollment = Enrollment::create($validator->validated());

    return response()->json([
        'success' => true,
        'message' => 'Enrollment created successfully',
        'data' => $enrollment->load(['student', 'class'])
    ], 201);
}

    public function show($id)
    {
        $enrollment = Enrollment::with(['student', 'class'])->find($id);
        if (!$enrollment) {
            return response()->json(['success' => false, 'message' => 'Enrollment not found'], 404);
        }
        return response()->json(['success' => true, 'data' => $enrollment]);
    }

    public function update(Request $request, $id)
    {
        $enrollment = Enrollment::find($id);
        if (!$enrollment) {
            return response()->json(['success' => false, 'message' => 'Enrollment not found'], 404);
        }

        $validator = Validator::make($request->all(), [
            'status' => 'sometimes|required|in:Active,Completed,Dropped',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $enrollment->update($request->all());
        return response()->json(['success' => true, 'message' => 'Enrollment updated successfully', 'data' => $enrollment]);
    }

    public function destroy($id)
    {
        $enrollment = Enrollment::find($id);
        if (!$enrollment) {
            return response()->json(['success' => false, 'message' => 'Enrollment not found'], 404);
        }
        $enrollment->delete();
        return response()->json(['success' => true, 'message' => 'Enrollment deleted successfully']);
    }
}
