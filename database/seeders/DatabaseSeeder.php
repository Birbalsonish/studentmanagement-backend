<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\{Student, Teacher, ClassModel, Subject, Grade, Result, Enrollment, Attendance, Fee};
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        
        Student::truncate();
        Teacher::truncate();
        ClassModel::truncate();
        Subject::truncate();
        Grade::truncate();
        Result::truncate();
        Enrollment::truncate();
        Attendance::truncate();
        Fee::truncate();

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $this->command->info('🌱 Seeding Teachers...');
        $teachers = $this->seedTeachers();

        $this->command->info('🌱 Seeding Classes...');
        $classes = $this->seedClasses($teachers);

        $this->command->info('🌱 Seeding Subjects...');
        $subjects = $this->seedSubjects($teachers, $classes);

        $this->command->info('🌱 Seeding Students...');
        $students = $this->seedStudents();

        $this->command->info('🌱 Seeding Enrollments...');
        $this->seedEnrollments($students, $classes);

        $this->command->info('🌱 Seeding Grades...');
        $this->seedGrades($students, $subjects);

        $this->command->info('🌱 Seeding Results...');
        $this->seedResults($students, $classes);

        $this->command->info('🌱 Seeding Attendance...');
        $this->seedAttendance($students, $classes, $subjects);

        $this->command->info('🌱 Seeding Fees...');
        $this->seedFees($students);

        $this->command->info('✅ Database seeding completed successfully!');
    }

    private function seedTeachers()
    {
        $teachers = [];
        $names = [
            'John Smith', 'Emma Johnson', 'Michael Brown', 'Sarah Davis', 'James Wilson',
            'Emily Martinez', 'Robert Taylor', 'Jessica Anderson', 'David Thomas', 'Jennifer Jackson',
            'William White', 'Linda Harris', 'Richard Martin', 'Mary Thompson', 'Charles Garcia',
            'Patricia Rodriguez', 'Joseph Lewis', 'Barbara Lee', 'Thomas Walker', 'Nancy Hall'
        ];

        foreach ($names as $index => $name) {
            $teachers[] = Teacher::create([
                'name' => $name,
                'email' => strtolower(str_replace(' ', '.', $name)) . '@school.com',
                'phone' => '555-' . str_pad($index + 1, 4, '0', STR_PAD_LEFT),
                'address' => ($index + 1) . ' Main Street, City',
                'gender' => $index % 2 == 0 ? 'Male' : 'Female',
                'qualification' => ['B.Ed', 'M.Ed', 'Ph.D'][array_rand(['B.Ed', 'M.Ed', 'Ph.D'])],
                'specialization' => ['Mathematics', 'Science', 'English', 'History', 'Computer Science'][array_rand(['Mathematics', 'Science', 'English', 'History', 'Computer Science'])],
                'joining_date' => now()->subYears(rand(1, 10)),
                'salary' => rand(30000, 80000),
                'status' => 'Active',
                'employee_id' => 'EMP' . str_pad($index + 1, 4, '0', STR_PAD_LEFT),
            ]);
        }

        return $teachers;
    }

    private function seedClasses($teachers)
    {
        $classes = [];
        $classNames = ['Grade 1', 'Grade 2', 'Grade 3', 'Grade 4', 'Grade 5', 
                       'Grade 6', 'Grade 7', 'Grade 8', 'Grade 9', 'Grade 10'];
        $sections = ['A', 'B'];

        foreach ($classNames as $index => $className) {
            foreach ($sections as $section) {
                $classes[] = ClassModel::create([
                    'name' => $className,
                    'section' => $section,
                    'teacher_id' => $teachers[array_rand($teachers)]->id,
                    'capacity' => 30,
                    'room_number' => 'Room ' . (($index * 2) + ord($section) - ord('A') + 1),
                    'status' => 'Active',
                    'description' => 'Standard class for ' . $className,
                ]);
            }
        }

        return $classes;
    }

    private function seedSubjects($teachers, $classes)
    {
        $subjects = [];
        $subjectNames = [
            'Mathematics', 'Science', 'English', 'History', 'Geography',
            'Computer Science', 'Physics', 'Chemistry', 'Biology', 'Physical Education',
            'Art', 'Music', 'Social Studies', 'Economics', 'Literature'
        ];

        foreach ($subjectNames as $index => $subjectName) {
            $subjects[] = Subject::create([
                'name' => $subjectName,
                'code' => 'SUB' . str_pad($index + 1, 3, '0', STR_PAD_LEFT),
                'teacher_id' => $teachers[array_rand($teachers)]->id,
                'class_id' => $classes[array_rand($classes)]->id,
                'credits' => rand(2, 4),
                'type' => ['Theory', 'Practical', 'Both'][array_rand(['Theory', 'Practical', 'Both'])],
                'status' => 'Active',
                'description' => 'Course for ' . $subjectName,
            ]);
        }

        return $subjects;
    }

    private function seedStudents()
    {
        $students = [];
        $firstNames = ['Alex', 'Sam', 'Jordan', 'Taylor', 'Morgan', 'Casey', 'Riley', 'Avery', 'Cameron', 'Dakota'];
        $lastNames = ['Anderson', 'Brown', 'Clark', 'Davis', 'Evans', 'Foster', 'Gray', 'Harris', 'Irwin', 'Jones'];

        for ($i = 1; $i <= 50; $i++) {
            $firstName = $firstNames[array_rand($firstNames)];
            $lastName = $lastNames[array_rand($lastNames)];
            $name = $firstName . ' ' . $lastName;

            $students[] = Student::create([
                'name' => $name,
                'email' => strtolower($firstName . '.' . $lastName . $i) . '@student.school.com',
                'phone' => '555-' . str_pad(rand(1000, 9999), 4, '0', STR_PAD_LEFT),
                'date_of_birth' => now()->subYears(rand(6, 18)),
                'address' => rand(1, 100) . ' Student Street, City',
                'guardian_name' => 'Guardian of ' . $name,
                'guardian_phone' => '555-' . str_pad(rand(1000, 9999), 4, '0', STR_PAD_LEFT),
                'gender' => ['Male', 'Female'][array_rand(['Male', 'Female'])],
                'status' => 'Active',
                'admission_number' => 'ADM' . date('Y') . str_pad($i, 4, '0', STR_PAD_LEFT),
                'admission_date' => now()->subMonths(rand(1, 24)),
            ]);
        }

        return $students;
    }

    private function seedEnrollments($students, $classes)
    {
        foreach ($students as $student) {
            Enrollment::create([
                'student_id' => $student->id,
                'class_id' => $classes[array_rand($classes)]->id,
                'enrollment_date' => now()->subMonths(rand(1, 12)),
                'academic_year' => date('Y'),
                'status' => 'Active',
            ]);
        }
    }

    private function seedGrades($students, $subjects)
    {
        $examTypes = ['Mid-term', 'Final', 'Quiz', 'Assignment'];

        foreach ($students as $student) {
            for ($i = 0; $i < 5; $i++) {
                $totalMarks = 100;
                $marksObtained = rand(40, 100);
                $percentage = ($marksObtained / $totalMarks) * 100;

                Grade::create([
                    'student_id' => $student->id,
                    'subject_id' => $subjects[array_rand($subjects)]->id,
                    'exam_type' => $examTypes[array_rand($examTypes)],
                    'marks_obtained' => $marksObtained,
                    'total_marks' => $totalMarks,
                    'percentage' => $percentage,
                    'grade' => Grade::calculateGrade($percentage),
                    'exam_date' => now()->subDays(rand(1, 90)),
                    'academic_year' => date('Y'),
                ]);
            }
        }
    }

    private function seedResults($students, $classes)
    {
        foreach ($students as $student) {
            $totalMarks = 500;
            $obtainedMarks = rand(200, 480);
            $percentage = ($obtainedMarks / $totalMarks) * 100;

            Result::create([
                'student_id' => $student->id,
                'class_id' => $classes[array_rand($classes)]->id,
                'exam_type' => 'Annual',
                'total_marks' => $totalMarks,
                'obtained_marks' => $obtainedMarks,
                'percentage' => $percentage,
                'grade' => Grade::calculateGrade($percentage),
                'division' => Result::calculateDivision($percentage),
                'result_status' => $percentage >= 40 ? 'Pass' : 'Fail',
                'rank' => null,
                'academic_year' => date('Y'),
            ]);
        }
    }

    private function seedAttendance($students, $classes, $subjects)
    {
        $statuses = ['Present', 'Absent', 'Late', 'Excused'];
        
        for ($day = 0; $day < 30; $day++) {
            $date = now()->subDays($day);
            
            foreach (array_slice($students, 0, 20) as $student) {
                Attendance::create([
                    'student_id' => $student->id,
                    'class_id' => $classes[array_rand($classes)]->id,
                    'subject_id' => $subjects[array_rand($subjects)]->id,
                    'date' => $date,
                    'status' => $statuses[array_rand($statuses)],
                    'check_in_time' => '08:00:00',
                    'check_out_time' => '15:00:00',
                ]);
            }
        }
    }

    private function seedFees($students)
    {
        $feeTypes = ['Tuition', 'Library', 'Sports', 'Lab', 'Transport'];
        
        foreach ($students as $student) {
            foreach ($feeTypes as $feeType) {
                $amount = rand(500, 5000);
                $paidAmount = rand(0, $amount);

                Fee::create([
                    'student_id' => $student->id,
                    'fee_type' => $feeType,
                    'amount' => $amount,
                    'paid_amount' => $paidAmount,
                    'pending_amount' => $amount - $paidAmount,
                    'due_date' => now()->addDays(rand(1, 90)),
                    'paid_date' => $paidAmount > 0 ? now()->subDays(rand(1, 30)) : null,
                    'status' => $paidAmount >= $amount ? 'Paid' : ($paidAmount > 0 ? 'Partial' : 'Pending'),
                    'payment_method' => $paidAmount > 0 ? ['Cash', 'Card', 'Online'][array_rand(['Cash', 'Card', 'Online'])] : null,
                    'transaction_id' => $paidAmount > 0 ? 'TXN' . rand(10000, 99999) : null,
                    'academic_year' => date('Y'),
                ]);
            }
        }
    }
}
