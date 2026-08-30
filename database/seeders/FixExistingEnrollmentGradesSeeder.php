<?php

namespace Database\Seeders;

use App\Models\Enrollment;
use Illuminate\Database\Seeder;

class FixExistingEnrollmentGradesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Update existing enrollment records that have grade 7 or invalid grade levels
        // Change them to a default grade (e.g., grade6) or update based on student_data
        
        $enrollments = Enrollment::where('grade_level', '7')
            ->orWhere('grade_level', 'grade7')
            ->get();
        
        foreach ($enrollments as $enrollment) {
            // Check if there's a grade_level in student_data
            if ($enrollment->student_data && isset($enrollment->student_data['grade_level'])) {
                $studentDataGrade = $enrollment->student_data['grade_level'];
                // Only update if the student_data has a valid grade
                if (in_array($studentDataGrade, ['nursery', 'kindergarten', 'grade1', 'grade2', 'grade3', 'grade4', 'grade5', 'grade6'])) {
                    $enrollment->grade_level = $studentDataGrade;
                    $enrollment->save();
                    echo "Updated enrollment ID {$enrollment->id}: grade_level set to {$studentDataGrade}\n";
                } else {
                    // Default to grade6 if no valid grade found
                    $enrollment->grade_level = 'grade6';
                    $enrollment->save();
                    echo "Updated enrollment ID {$enrollment->id}: grade_level set to grade6 (default)\n";
                }
            } else {
                // Default to grade6 if no grade in student_data
                $enrollment->grade_level = 'grade6';
                $enrollment->save();
                echo "Updated enrollment ID {$enrollment->id}: grade_level set to grade6 (default - no student_data grade)\n";
            }
        }
        
        echo "\nTotal enrollments updated: " . $enrollments->count() . "\n";
    }
}
