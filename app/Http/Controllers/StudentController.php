<?php

namespace App\Http\Controllers;

use App\Models\Student;
use Carbon\Carbon;
use Illuminate\Http\Request;

class StudentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $limit = $request->limit;
        $search = $request->search ?? '';
        $start_date = $request->start_date ? Carbon::createFromFormat('Y-m-d H:i:s', $request->start_date)->startOfDay()->toDateTimeString() : null;
        $end_date = $request->end_date ? Carbon::createFromFormat('Y-m-d H:i:s', $request->end_date)->endOfDay()->toDateTimeString() : null;
        $like = 'LIKE';
        $paginated = Student::withTrashed()->with([
            'level', 'program', 'registration.educationBackgrounds',
            'registration.guardians', 'registration.level', 'registration.program',
            'registration.schoolSetting', 'registration.siblings', 'schoolSetting', 'enrollmentHistories' => function ($query) {
                $query->with(['schoolSetting.term', 'enrolledSubjects'])->leftJoin('school_settings', 'school_setting_id', '=', 'school_settings.id')
                    ->orderBy('school_settings.academic_year')->orderBy('school_settings.term_id')->select('enrollment_histories.*');
            }
        ])->withCount(['enrollmentHistories'])
            ->when($start_date && $end_date, function ($query) use ($start_date, $end_date) {
                $query->whereHas(
                    'registration',
                    function ($query) use ($start_date, $end_date) {
                        $query->where('created_at', '<=', $end_date)->where('created_at', '>=', $start_date);
                    }
                );
            })
            ->when($search, function ($query) use ($like, $search) {
                $query->where(function ($query) use ($like, $search) {
                    $query->whereHas(
                        'registration',
                        function ($query) use ($like, $search) {
                            $query->where(
                                function ($query) use ($like, $search) {
                                    $query->where('last_name', $like,  '%' . $search . '%')
                                        ->orWhere('first_name', $like,  '%' . $search . '%');
                                }
                            );
                        }
                    )->orWhere('student_number', $like, '%' . $search . '%');
                });
            })
            ->when($request->exists('student_status') && $request->student_status != 'ALL', function ($query) use ($request) {
                $query->when($request->student_status == 'ACTIVE', function ($query) {
                    $query->whereNull('deleted_at');
                })
                    ->when($request->student_status == 'INACTIVE', function ($query) {
                        $query->whereNotNull('deleted_at');
                    });
            })
            ->when($request->exists('p'), function ($query) use ($request) {
                $query->whereIn('program_id', $request->input('p'));
            })
            ->when($request->exists('l'), function ($query) use ($request) {
                $query->whereIn('level_id', $request->input('l'));
            })
            ->when($request->exists('st'), function ($query) use ($request) {
                $query->whereIn('student_type_id', $request->input('st'));
            })
            ->paginate($limit);
        return response()->json($paginated);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  number  $student
     * @return \Illuminate\Http\Response
     */
    public function show($student)
    {
        return Student::withTrashed()->with([
            'level', 'program', 'registration.educationBackgrounds', 'registration.guardians',
            'registration.level', 'registration.program', 'registration.schoolSetting',
            'registration.siblings', 'schoolSetting', 'enrollmentHistories' => function ($query) {
                $query->with(['schoolSetting.term', 'enrolledSubjects'])->leftJoin('school_settings', 'school_setting_id', '=', 'school_settings.id')
                    ->orderBy('school_settings.academic_year')->orderBy('school_settings.term_id')->select('enrollment_histories.*');
            }
        ])->withCount(['enrollmentHistories'])->findOrFail($student);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  number  $student
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $student)
    {
        $updated_student = Student::with('registration')->findOrFail($student);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param   $student
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $student)
    {
        $student = Student::withTrashed()->withCount(['enrollmentHistories'])->findOrFail($student);

        if (boolval($request->forceDelete)) {
            if ($student->enrollment_histories_count > 0) {
                return response()->json(
                    [
                        "error" => "Unable to Delete Student!",
                        "message" => "Student is currently associated to $student->enrollment_histories_count enrollments",
                    ],
                    500
                );
            }

            $student->user->roles()->detach();
            $student->forceDelete();
            $student->user()->forceDelete();
            return response()->json(
                [
                    "message" => "Student Records Permanently Deleted!",
                ],
                200
            );
        }

        if (boolval($request->toggle)) {
            if ($student->trashed()) {
                $student->user()->restore();
                $student->restore();
            } else {
                $student->user->delete();
                $student->delete();
            }
            return response()->json(
                [
                    "deleted_at" => $student->deleted_at,
                ],
                200
            );
        }
    }
}
