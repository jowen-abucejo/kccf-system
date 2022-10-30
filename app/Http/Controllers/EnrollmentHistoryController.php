<?php

namespace App\Http\Controllers;

use App\Models\EnrollmentHistory;
use Illuminate\Http\Request;
use Throwable;

class EnrollmentHistoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $limit = $request->limit;
        $search = $request->search ?? '';
        $like = 'LIKE';

        $enrollment_histories = EnrollmentHistory::withTrashed()
            ->when($request->withProgram, function ($query) {
                $query->with('program');
            })
            ->when($request->withStudentType, function ($query) {
                $query->with('studentType');
            })
            ->when($request->withSubjects, function ($query) {
                $query->with('enrolledSubjects');
            })
            ->when($request->withSchoolSetting, function ($query) {
                $query->with('schoolSetting.term')
                    //order records based on the school setting's academic year and term
                    ->leftJoin('school_settings', 'school_setting_id', '=', 'school_settings.id')
                    ->orderBy('school_settings.academic_year')->orderBy('school_settings.term_id')->select('enrollment_histories.*');
            })
            ->when($request->student_id, function ($query) use ($request) {
                $query->where('student_id', $request->student_id);
            });

        if (!boolval($request->page)) {
            $enrollment_histories = $enrollment_histories->get();
        } else {
            $enrollment_histories = $enrollment_histories->paginate($limit);
        }
        return response()->json($enrollment_histories);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $data = $request->new_enrollment;

        if (EnrollmentHistory::where('student_id', $data['student_id'])->where('school_setting_id', $data['school_setting_id'])->count() > 0)
            return response()->json(
                [
                    "error" => "Student Enrollment Failed!",
                    "message" => "Student is already enrolled in selected academic year and term.",
                ],
                400
            );

        $new_enrollment_history = EnrollmentHistory::create([
            'student_id' => $data['student_id'],
            'school_setting_id' => $data['school_setting_id'],
            'program_id' => $data['program_id'],
            'level_id' => $data['level_id'],
            'student_type_id' => $data['student_type_id'],
        ]);
        try {
            $student_enrolled = $new_enrollment_history->student;
            $latest_enrollment = $student_enrolled->enrollmentHistories()->with(['schoolSetting'])->leftJoin('school_settings', 'school_setting_id', '=', 'school_settings.id')
                ->orderBy('school_settings.academic_year', 'DESC')->orderBy('school_settings.term_id', 'DESC')->select('enrollment_histories.*')->first();

            //update student's program, level and type
            if (
                $latest_enrollment->schoolSetting->academic_year < $new_enrollment_history->schoolSetting->academic_year
                || ($latest_enrollment->schoolSetting->academic_year == $new_enrollment_history->schoolSetting->academic_year &&
                    $latest_enrollment->schoolSetting->term_id < $new_enrollment_history->schoolSetting->term_id)
            ) {
                $student_enrolled->program_id =  $data['program_id'];
                $student_enrolled->level_id =  $data['level_id'];
                $student_enrolled->student_type_id =  $data['student_type_id'];
                $student_enrolled->save();
            }

            $new_enrollment_history->offerSubjects()
                ->syncWithPivotValues(
                    $data['enrolled_subjects'],
                    ['created_by' => auth()->user()->id, 'updated_by' => auth()->user()->id]
                );
        } catch (Throwable $th) {
            if ($new_enrollment_history) {
                $new_enrollment_history->enrolledSubjects()->delete();
                $new_enrollment_history->forceDelete();
            }
            return response()->json(
                [
                    "error" => "Student Enrollment Failed!",
                    "message" => $th->getMessage(),
                ],
                500
            );
        }

        return response()->json(
            app('App\Http\Controllers\StudentController')->show($student_enrolled->id)
        );
    }

    /**
     * Display the specified resource.
     *
     * @param  number $enrollmentHistory
     * @return \Illuminate\Http\Response
     */
    public function show($enrollmentHistory)
    {
        return response()->json(EnrollmentHistory::withTrashed()->with(['program', 'level', 'studentType', 'schoolSetting.term', 'enrolledSubjects'])->findOrFail($enrollmentHistory));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  number $enrollmentHistory
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $enrollmentHistory)
    {
        $data = $request->new_enrollment;

        $updated_enrollment = EnrollmentHistory::withTrashed()->findOrFail($enrollmentHistory);
        $updated_enrollment->update([
            'student_id' => $data['student_id'],
            'school_setting_id' => $data['school_setting_id'],
            'program_id' => $data['program_id'],
            'level_id' => $data['level_id'],
            'student_type_id' => $data['student_type_id'],
        ]);

        $student_enrolled = $updated_enrollment->student;
        $latest_enrollment = $student_enrolled->enrollmentHistories()->leftJoin('school_settings', 'school_setting_id', '=', 'school_settings.id')
            ->orderBy('school_settings.academic_year', 'DESC')->orderBy('school_settings.term_id', 'DESC')->select('enrollment_histories.id')->first();

        //update student's program, level and type if updated enrollment is the latest
        if ($latest_enrollment->id === $updated_enrollment->id) {
            $student_enrolled->program_id = $updated_enrollment->program_id;
            $student_enrolled->level_id = $updated_enrollment->level_id;
            $student_enrolled->student_type_id = $updated_enrollment->student_type_id;
            $student_enrolled->save();
        }


        foreach ($data['enrolled_subjects'] as $offer_subject_id) {
            $updated_enrollment->enrolledSubjects()
                ->where('offer_subject_id', $offer_subject_id)
                ->where('status', '!=', 'DROPPED')
                ->firstOr(function () use ($updated_enrollment, $offer_subject_id) {
                    return $updated_enrollment->enrolledSubjects()
                        ->create([
                            'offer_subject_id' => $offer_subject_id,
                            'created_by' => auth()->user()->id,
                            'updated_by' => auth()->user()->id,
                        ]);
                });
        }

        $updated_enrollment->enrolledSubjects()->where('status', 'ENROLLED')->whereNotIn('offer_subject_id', $data['enrolled_subjects'])->update(["status" => "DROPPED"]);
        $updated_enrollment->enrolledSubjects()->where('status', 'ENLISTED')->whereNotIn('offer_subject_id', $data['enrolled_subjects'])->delete();

        return response()->json(
            app('App\Http\Controllers\StudentController')->show($student_enrolled->id)
        );
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\EnrollmentHistory  $enrollmentHistory
     * @return \Illuminate\Http\Response
     */
    public function destroy(EnrollmentHistory $enrollmentHistory)
    {
        //
    }
}
