<?php

namespace App\Http\Controllers;

use App\Models\Grade;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class GradeController extends Controller
{
    public function index(Request $request)
    {
        $query = Grade::with(['student', 'subject']);

        if ($request->has('student_id')) {
            $query->where('student_id', $request->student_id);
        }

        if ($request->has('subject_id')) {
            $query->where('subject_id', $request->subject_id);
        }

        $grades = $query->latest()->paginate($request->per_page ?? 15);
        return response()->json(['success' => true, 'data' => $grades]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'student_id' => 'required|exists:students,id',
            'subject_id' => 'required|exists:subjects,id',
            'exam_type' => 'required|string',
            'marks_obtained' => 'required|numeric|min:0',
            'total_marks' => 'required|numeric|min:0',
            'exam_date' => 'required|date',
            'academic_year' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $data = $request->all();
        $data['percentage'] = ($data['marks_obtained'] / $data['total_marks']) * 100;
        $data['grade'] = Grade::calculateGrade($data['percentage']);

        $grade = Grade::create($data);
        return response()->json(['success' => true, 'message' => 'Grade created successfully', 'data' => $grade->load(['student', 'subject'])], 201);
    }

    public function show($id)
    {
        $grade = Grade::with(['student', 'subject'])->find($id);
        if (!$grade) {
            return response()->json(['success' => false, 'message' => 'Grade not found'], 404);
        }
        return response()->json(['success' => true, 'data' => $grade]);
    }

    public function update(Request $request, $id)
    {
        $grade = Grade::find($id);
        if (!$grade) {
            return response()->json(['success' => false, 'message' => 'Grade not found'], 404);
        }

        $validator = Validator::make($request->all(), [
            'marks_obtained' => 'sometimes|required|numeric|min:0',
            'total_marks' => 'sometimes|required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $data = $request->all();
        if (isset($data['marks_obtained']) && isset($data['total_marks'])) {
            $data['percentage'] = ($data['marks_obtained'] / $data['total_marks']) * 100;
            $data['grade'] = Grade::calculateGrade($data['percentage']);
        }

        $grade->update($data);
        return response()->json(['success' => true, 'message' => 'Grade updated successfully', 'data' => $grade]);
    }

    public function destroy($id)
    {
        $grade = Grade::find($id);
        if (!$grade) {
            return response()->json(['success' => false, 'message' => 'Grade not found'], 404);
        }
        $grade->delete();
        return response()->json(['success' => true, 'message' => 'Grade deleted successfully']);
    }
}
