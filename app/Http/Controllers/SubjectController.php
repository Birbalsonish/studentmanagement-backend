<?php

namespace App\Http\Controllers;

use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SubjectController extends Controller
{
    public function index(Request $request)
    {
        $query = Subject::with(['teacher', 'class']);

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%");
            });
        }

        if ($request->has('class_id')) {
            $query->where('class_id', $request->class_id);
        }

        $subjects = $query->latest()->paginate($request->per_page ?? 15);

        return response()->json(['success' => true, 'data' => $subjects]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'code' => 'required|string|unique:subjects,code',
            'teacher_id' => 'nullable|exists:teachers,id',
            'class_id' => 'nullable|exists:classes,id',
            'credits' => 'required|integer|min:1',
            'type' => 'required|in:Theory,Practical,Both',
            'status' => 'required|in:Active,Inactive',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $subject = Subject::create($request->all());
        return response()->json(['success' => true, 'message' => 'Subject created successfully', 'data' => $subject->load(['teacher', 'class'])], 201);
    }

    public function show($id)
    {
        $subject = Subject::with(['teacher', 'class', 'grades'])->find($id);
        if (!$subject) {
            return response()->json(['success' => false, 'message' => 'Subject not found'], 404);
        }
        return response()->json(['success' => true, 'data' => $subject]);
    }

    public function update(Request $request, $id)
    {
        $subject = Subject::find($id);
        if (!$subject) {
            return response()->json(['success' => false, 'message' => 'Subject not found'], 404);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required|string|max:255',
            'code' => 'sometimes|required|string|unique:subjects,code,' . $id,
            'teacher_id' => 'nullable|exists:teachers,id',
            'class_id' => 'nullable|exists:classes,id',
            'credits' => 'sometimes|required|integer|min:1',
            'type' => 'sometimes|required|in:Theory,Practical,Both',
            'status' => 'sometimes|required|in:Active,Inactive',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $subject->update($request->all());
        return response()->json(['success' => true, 'message' => 'Subject updated successfully', 'data' => $subject->load(['teacher', 'class'])]);
    }

    public function destroy($id)
    {
        $subject = Subject::find($id);
        if (!$subject) {
            return response()->json(['success' => false, 'message' => 'Subject not found'], 404);
        }
        $subject->delete();
        return response()->json(['success' => true, 'message' => 'Subject deleted successfully']);
    }
}
