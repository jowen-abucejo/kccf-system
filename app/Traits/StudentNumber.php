<?php

namespace App\Traits;

use App\Models\SchoolSetting;
use App\Models\Student;

trait StudentNumber
{
    public static function bootStudentNumber()
    {
        // updating student number when model is created
        static::creating(function (Student $student) {
            $academic_year = SchoolSetting::find($student->school_setting_id)->value('academic_year');

            if (auth()->user() && $academic_year) {
                $arrayed_year = explode('-', $academic_year);

                $prefix = $arrayed_year[0] . '-';
                $suffix = '00001';

                $max_student_number = Student::withTrashed()->where('student_number', 'LIKE', $prefix . '%')->max('student_number');

                if ($max_student_number) {
                    $arrayed_student_number = explode('-', $max_student_number);

                    $suffix = $arrayed_student_number[1];

                    //add 1 to last student number
                    $new_number = intval($suffix);
                    $suffix = sprintf("%05d", $new_number + 1);
                }
                //set student number for new student
                $student->student_number = $prefix . $suffix;
            }
        });
    }
}
