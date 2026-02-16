<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ClassModel extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'classes';

    protected $fillable = [
        'name',
        'section',
        'teacher_id',
        'capacity',
        'room_number',
        'status',
        'description',
    ];

    protected $casts = [
        'capacity' => 'integer',
    ];

    // Relationships
    public function teacher()
    {
        return $this->belongsTo(Teacher::class);
    }

    public function subjects()
    {
        return $this->hasMany(Subject::class, 'class_id');
    }

    public function enrollments()
    {
        return $this->hasMany(Enrollment::class, 'class_id');
    }

    public function students()
    {
        return $this->belongsToMany(Student::class, 'enrollments', 'class_id', 'student_id');
    }

    public function attendance()
    {
        return $this->hasMany(Attendance::class, 'class_id');
    }

    public function results()
    {
        return $this->hasMany(Result::class, 'class_id');
    }

    // Accessor for full name
    public function getFullNameAttribute()
    {
        return $this->section ? "{$this->name} - {$this->section}" : $this->name;
    }
}
