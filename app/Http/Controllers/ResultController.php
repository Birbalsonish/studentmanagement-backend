<?php

namespace App\Http\Controllers;

use App\Models\Result;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ResultController extends Controller
{
    public function index(Request $request)
    {
        $query = Result::with(['student', 'class']);

        if ($request->has('student_id')) {
            $query->where('student_id', $request->student_id);
        }

        if ($request->has('class_id')) {
            $query->where('class_id', $request->class_id);
        }

        if ($request->has('academic_year')) {
            $query->where('academic_year', $request->academic_year);
        }

        $results = $query->latest()->paginate($request->per_page ?? 15);
        return response()->json(['success' => true, 'data' => $results]);
    }

   public function store(Request $request)
{
    $validator = Validator::make($request->all(), [
        'student_id' => 'required|exists:students,id',
        'class_id' => 'required|exists:classes,id',
        'exam_type' => 'required|string',
        'total_marks' => 'required|numeric|min:0',
        'obtained_marks' => 'required|numeric|min:0',
        'academic_year' => 'required|string',
    ]);

    if ($validator->fails()) {
        return response()->json([
            'success' => false,
            'errors' => $validator->errors()
        ], 422);
    }

    $data = $request->all();
    
    // Auto-calculate percentage
    $data['percentage'] = ($data['obtained_marks'] / $data['total_marks']) * 100;
    
    // Auto-calculate grade
    $data['grade'] = \App\Models\Grade::calculateGrade($data['percentage']);
    
    // Auto-calculate division
    $data['division'] = \App\Models\Result::calculateDivision($data['percentage']);
    
    // Auto-calculate result status
    $data['result_status'] = $data['percentage'] >= 40 ? 'Pass' : 'Fail';

    $result = Result::create($data);

    return response()->json([
        'success' => true,
        'message' => 'Result created successfully',
        'data' => $result->load(['student', 'class'])
    ], 201);
}
    public function show($id)
    {
        $result = Result::with(['student', 'class'])->find($id);
        if (!$result) {
            return response()->json(['success' => false, 'message' => 'Result not found'], 404);
        }
        return response()->json(['success' => true, 'data' => $result]);
    }

    public function update(Request $request, $id)
    {
        $result = Result::find($id);
        if (!$result) {
            return response()->json(['success' => false, 'message' => 'Result not found'], 404);
        }

        $data = $request->all();
        if (isset($data['obtained_marks']) && isset($data['total_marks'])) {
            $data['percentage'] = ($data['obtained_marks'] / $data['total_marks']) * 100;
            $data['grade'] = Grade::calculateGrade($data['percentage']);
            $data['division'] = Result::calculateDivision($data['percentage']);
            $data['result_status'] = $data['percentage'] >= 40 ? 'Pass' : 'Fail';
        }

        $result->update($data);
        return response()->json(['success' => true, 'message' => 'Result updated successfully', 'data' => $result]);
    }

    public function destroy($id)
    {
        $result = Result::find($id);
        if (!$result) {
            return response()->json(['success' => false, 'message' => 'Result not found'], 404);
        }
        $result->delete();
        return response()->json(['success' => true, 'message' => 'Result deleted successfully']);
    }
}
