<?php

namespace App\Http\Controllers;

use App\Models\{Student, Teacher, ClassModel, Subject, Enrollment, Fee, Attendance};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $data = [
            'overview' => $this->getOverview(),
            'recent_enrollments' => $this->getRecentEnrollments(),
            'fee_statistics' => $this->getFeeStatistics(),
            'attendance_summary' => $this->getAttendanceSummary(),
            'top_performers' => $this->getTopPerformers(),
        ];

        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }

    private function getOverview()
    {
        return [
            'total_students' => Student::where('status', 'Active')->count(),
            'total_teachers' => Teacher::where('status', 'Active')->count(),
            'total_classes' => ClassModel::where('status', 'Active')->count(),
            'total_subjects' => Subject::where('status', 'Active')->count(),
            'total_enrollments' => Enrollment::where('status', 'Active')->count(),
        ];
    }

    private function getRecentEnrollments()
    {
        return Enrollment::with(['student', 'class'])
            ->latest()
            ->limit(5)
            ->get();
    }

    private function getFeeStatistics()
    {
        $currentYear = date('Y');
        
        return [
            'total_fees' => Fee::where('academic_year', $currentYear)->sum('amount'),
            'collected_fees' => Fee::where('academic_year', $currentYear)->sum('paid_amount'),
            'pending_fees' => Fee::where('academic_year', $currentYear)->sum('pending_amount'),
            'overdue_fees' => Fee::where('status', 'Overdue')->sum('pending_amount'),
        ];
    }

    private function getAttendanceSummary()
    {
        $today = now()->format('Y-m-d');
        $totalToday = Attendance::whereDate('date', $today)->count();
        $presentToday = Attendance::whereDate('date', $today)->where('status', 'Present')->count();
        $absentToday = Attendance::whereDate('date', $today)->where('status', 'Absent')->count();

        return [
            'total_today' => $totalToday,
            'present_today' => $presentToday,
            'absent_today' => $absentToday,
            'attendance_rate' => $totalToday > 0 ? round(($presentToday / $totalToday) * 100, 2) : 0,
        ];
    }

    private function getTopPerformers()
    {
        return DB::table('results')
            ->join('students', 'results.student_id', '=', 'students.id')
            ->select('students.name', 'results.percentage', 'results.grade', 'results.exam_type')
            ->orderBy('results.percentage', 'desc')
            ->limit(5)
            ->get();
    }

    public function statistics(Request $request)
    {
        $year = $request->academic_year ?? date('Y');

        $data = [
            'students_by_class' => $this->getStudentsByClass(),
            'attendance_trends' => $this->getAttendanceTrends($year),
            'fee_collection_monthly' => $this->getFeeCollectionMonthly($year),
            'subject_wise_performance' => $this->getSubjectWisePerformance($year),
        ];

        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }

    private function getStudentsByClass()
    {
        return DB::table('enrollments')
            ->join('classes', 'enrollments.class_id', '=', 'classes.id')
            ->select('classes.name', DB::raw('COUNT(*) as student_count'))
            ->where('enrollments.status', 'Active')
            ->groupBy('classes.name')
            ->get();
    }

    private function getAttendanceTrends($year)
    {
        return DB::table('attendance')
            ->select(
                DB::raw('MONTH(date) as month'),
                DB::raw('COUNT(*) as total'),
                DB::raw('SUM(CASE WHEN status = "Present" THEN 1 ELSE 0 END) as present'),
                DB::raw('SUM(CASE WHEN status = "Absent" THEN 1 ELSE 0 END) as absent')
            )
            ->whereYear('date', $year)
            ->groupBy(DB::raw('MONTH(date)'))
            ->orderBy('month')
            ->get();
    }

    private function getFeeCollectionMonthly($year)
    {
        return DB::table('fees')
            ->select(
                DB::raw('MONTH(paid_date) as month'),
                DB::raw('SUM(paid_amount) as collected')
            )
            ->whereYear('paid_date', $year)
            ->whereNotNull('paid_date')
            ->groupBy(DB::raw('MONTH(paid_date)'))
            ->orderBy('month')
            ->get();
    }

    private function getSubjectWisePerformance($year)
    {
        return DB::table('grades')
            ->join('subjects', 'grades.subject_id', '=', 'subjects.id')
            ->select(
                'subjects.name',
                DB::raw('AVG(grades.percentage) as average_percentage'),
                DB::raw('COUNT(*) as total_students')
            )
            ->where('grades.academic_year', $year)
            ->groupBy('subjects.name')
            ->orderBy('average_percentage', 'desc')
            ->get();
    }
}
