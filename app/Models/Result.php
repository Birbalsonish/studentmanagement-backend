<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Result extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'student_id',
        'class_id',
        'exam_type',
        'total_marks',
        'obtained_marks',
        'percentage',
        'grade',
        'division',
        'result_status',
        'rank',
        'academic_year',
        'remarks',
    ];

    protected $casts = [
        'total_marks' => 'decimal:2',
        'obtained_marks' => 'decimal:2',
        'percentage' => 'decimal:2',
        'rank' => 'integer',
    ];

    // Relationships
    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function class()
    {
        return $this->belongsTo(ClassModel::class, 'class_id');
    }

    // Calculate division based on percentage
    public static function calculateDivision($percentage)
    {
        if ($percentage >= 75) return 'First';
        if ($percentage >= 60) return 'Second';
        if ($percentage >= 40) return 'Third';
        return 'Fail';
    }
}
