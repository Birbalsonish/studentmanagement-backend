<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AttendanceController extends Controller
{
    public function index(Request $request)
    {
        $query = Attendance::with(['student', 'class', 'subject']);

        if ($request->has('student_id')) {
            $query->where('student_id', $request->student_id);
        }

        if ($request->has('class_id')) {
            $query->where('class_id', $request->class_id);
        }

        if ($request->has('date')) {
            $query->whereDate('date', $request->date);
        }

        $attendance = $query->latest()->paginate($request->per_page ?? 15);
        return response()->json(['success' => true, 'data' => $attendance]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'student_id' => 'required|exists:students,id',
            'class_id' => 'required|exists:classes,id',
            'subject_id' => 'nullable|exists:subjects,id',
            'date' => 'required|date',
            'status' => 'required|in:Present,Absent,Late,Excused',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $attendance = Attendance::create($request->all());
        return response()->json(['success' => true, 'message' => 'Attendance marked successfully', 'data' => $attendance->load(['student', 'class', 'subject'])], 201);
    }

    public function show($id)
    {
        $attendance = Attendance::with(['student', 'class', 'subject'])->find($id);
        if (!$attendance) {
            return response()->json(['success' => false, 'message' => 'Attendance not found'], 404);
        }
        return response()->json(['success' => true, 'data' => $attendance]);
    }

    public function update(Request $request, $id)
    {
        $attendance = Attendance::find($id);
        if (!$attendance) {
            return response()->json(['success' => false, 'message' => 'Attendance not found'], 404);
        }

        $validator = Validator::make($request->all(), [
            'status' => 'sometimes|required|in:Present,Absent,Late,Excused',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $attendance->update($request->all());
        return response()->json(['success' => true, 'message' => 'Attendance updated successfully', 'data' => $attendance]);
    }

    public function destroy($id)
    {
        $attendance = Attendance::find($id);
        if (!$attendance) {
            return response()->json(['success' => false, 'message' => 'Attendance not found'], 404);
        }
        $attendance->delete();
        return response()->json(['success' => true, 'message' => 'Attendance deleted successfully']);
    }

    // Bulk attendance marking
    public function bulkStore(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'class_id' => 'required|exists:classes,id',
            'subject_id' => 'nullable|exists:subjects,id',
            'date' => 'required|date',
            'attendances' => 'required|array',
            'attendances.*.student_id' => 'required|exists:students,id',
            'attendances.*.status' => 'required|in:Present,Absent,Late,Excused',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $attendances = [];
        foreach ($request->attendances as $item) {
            $attendances[] = Attendance::create([
                'student_id' => $item['student_id'],
                'class_id' => $request->class_id,
                'subject_id' => $request->subject_id,
                'date' => $request->date,
                'status' => $item['status'],
            ]);
        }

        return response()->json(['success' => true, 'message' => 'Bulk attendance marked successfully', 'data' => $attendances], 201);
    }
}
