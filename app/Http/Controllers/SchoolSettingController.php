<?php

namespace App\Http\Controllers;

use App\Models\OfferSubject;
use App\Models\SchoolSetting;
use App\Models\Subject;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SchoolSettingController extends Controller
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
        $like = 'LIKE';

        $school_settings = SchoolSetting::with(['term'])->withCount(['students', 'studentRegistrations', 'enrollmentHistories'])
            ->when($request->withTrashed, function ($query) use ($search, $like) {
                $query
                    ->when($search, function ($query) use ($search, $like) {
                        $query->where('academic_year', $like, '%' . $search . '%');
                    });
            })
            ->when($request->exists('settings_status') && $request->settings_status != 'ALL', function ($query) use ($request) {
                $now = (Carbon::now())->addHours(8);
                $query->when($request->settings_status == 'OPEN_ENROLL', function ($query) use ($now) {
                    $query->where('enrollment_end_date', '>', $now->format('Y-m-d H:i:s'))->where('enrollment_start_date', '<=', $now->format('Y-m-d H:i:s'));
                })
                    ->when($request->settings_status == 'OPEN_ENCODE', function ($query) use ($now) {
                        $query->where('encoding_end_date', '>', $now->format('Y-m-d H:i:s'))->where('encoding_start_date', '<=', $now->format('Y-m-d H:i:s'));
                    });
            })->orderBy('academic_year', 'DESC')->orderBy('term_id', 'DESC');

        if (!boolval($request->page)) {
            $school_settings = $school_settings->get();
        } else {
            $school_settings = $school_settings->paginate($limit);
        }

        return response()->json($school_settings);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $data = $request->new_setting;

        $this->validate(
            $request,
            [
                'new_setting.academic_year' =>
                [
                    'bail',
                    'required',
                    'regex:/^(19|20)\d{2}[-](19|20)\d{2}$/'
                ],
                'new_setting.term' =>
                [
                    'bail',
                    'required',
                    Rule::unique('school_settings', 'term_id')->where('academic_year', $data['academic_year'])
                ]
            ],
            [
                'new_setting.academic_year.required' => 'Academic year is required.',
                'new_setting.academic_year.regex' => 'Academic year must be in a valid format.',
                'new_setting.term.required' => 'Term is required.',
                'new_setting.term.unique' => 'Setting with same academic year and term already exist',
            ]
        );

        $new_setting = SchoolSetting::create([
            'academic_year' => trim($data['academic_year']),
            'term_id' => $data['term'],
            'encoding_start_date' => $data['encoding_start_date'],
            'encoding_end_date' => $data['encoding_end_date'],
            'enrollment_start_date' => $data['enrollment_start_date'],
            'enrollment_end_date' => $data['enrollment_end_date'],
        ]);
        $new_setting->subjects()->sync($data['subjects']);
        OfferSubject::where('school_setting_id', $new_setting->id)
            ->whereNull('created_by')->update(['created_by' => auth()->user()->id, 'updated_by' => auth()->user()->id]);

        return response()->json($new_setting->fresh(['term'])->loadCount(['students', 'studentRegistrations', 'enrollmentHistories']));
    }

    /**
     * Display the specified resource.
     *
     * @param Request $request
     * @param  $schoolSetting
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $schoolSetting)
    {
        $credited_subjects = array();

        if ($request->student_id) {
            //get academic year and term only
            $school_year_and_term = SchoolSetting::withTrashed(boolval($request->withTrashed))
                ->select('academic_year', 'term_id')->findOrFail($schoolSetting);

            //get all credited subjects for student
            $student_credited_subjects = Subject::select('id')->whereHas('grades', function ($query) use ($request, $school_year_and_term) {
                $query->where('student_id', $request->student_id)->where('remarks', 'PASSED')
                    ->where(function ($query) use ($school_year_and_term) {
                        //get only credited subjects before the school setting given
                        $query->where('academic_year', '>', $school_year_and_term->academic_year)
                            ->orWhere(function ($query) use ($school_year_and_term) {
                                $query->where('academic_year', $school_year_and_term->academic_year)
                                    ->where('term_id', '>', $school_year_and_term->term_id);
                            });
                    });
            })->get()->pluck('id')->toArray();

            //get all equivalent subjects of all credited subjects
            $equivalent_credited_subjects = Subject::select('id')->whereHas('equivalentPreviousSubjects', function ($query) use ($student_credited_subjects) {
                $query->whereIn('equivalent_subjects.equal_subject_id', $student_credited_subjects);
            })->get()->pluck('id')->toArray();

            //merge credited subjects with equivalent subjects
            $credited_subjects = array_merge($student_credited_subjects, $equivalent_credited_subjects);
        }

        //get schoolSetting with its offered subjects the student are allowed to enroll into
        $school_setting_with_offered_subjects = SchoolSetting::withTrashed(boolval($request->withTrashed))
            ->with(['term', 'subjects' => function ($query) use ($request, $credited_subjects) {
                //filter offered subjects by program
                $query->when($request->program_id, function ($query) use ($request) {
                    $query->whereHas('programs', function ($query) use ($request) {
                        $query->where('program_subjects.program_id', $request->program_id);
                    });
                })
                    ->when($request->student_id, function ($query) use ($credited_subjects) {
                        //filter offered subjects by pre requisite subjects
                        $query->where(function ($query) use ($credited_subjects) {

                            $query->whereDoesntHave('preRequisiteSubjects', function ($query) use ($credited_subjects) {
                                $query->whereNotIn('pre_requisite_subjects.required_subject_id', $credited_subjects);
                            })
                                ->orWhere(function ($query) use ($credited_subjects) {
                                    $query->doesntHave('preRequisiteSubjects')
                                        ->has('equivalentPreviousSubjects')
                                        ->whereDoesntHave('equivalentPreviousSubjects', function ($query) use ($credited_subjects) {
                                            $query->whereIn('equivalent_subjects.equal_subject_id', $credited_subjects);
                                        });
                                });
                        });
                    });
            }])
            ->withCount(['students', 'studentRegistrations', 'enrollmentHistories'])->findOrFail($schoolSetting);

        return response()->json($school_setting_with_offered_subjects);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  number $schoolSetting
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $schoolSetting)
    {
        $update_schoolSetting = SchoolSetting::withTrashed()->withCount(['studentRegistrations', 'students'])->findOrFail($schoolSetting);

        $data = $request->new_setting;
        $this->validate(
            $request,
            [
                'new_setting.academic_year' =>
                [
                    'bail',
                    'required',
                    'regex:/^(19|20)\d{2}[-](19|20)\d{2}$/'
                ],
                'new_setting.term' =>
                [
                    'bail',
                    'required',
                    Rule::unique('school_settings', 'term_id')->where('academic_year', $data['academic_year'])->whereNot('id', $update_schoolSetting->id)
                ]
            ],
            [
                'new_setting.academic_year.required' => 'Academic year is required.',
                'new_setting.academic_year.regex' => 'Academic year must be in a valid format.',
                'new_setting.term.required' => 'Term is required.',
                'new_setting.term.unique' => 'Setting with same academic year and term already exist',
            ]
        );

        $update_schoolSetting->academic_year = $data['academic_year'];
        $update_schoolSetting->term_id = $data['term'];
        $update_schoolSetting->enrollment_start_date = $data['enrollment_start_date'];
        $update_schoolSetting->enrollment_end_date = $data['enrollment_end_date'];
        $update_schoolSetting->encoding_start_date = $data['encoding_start_date'];
        $update_schoolSetting->encoding_end_date = $data['encoding_end_date'];

        $student_count = $update_schoolSetting->students_count;
        if ($update_schoolSetting->isDirty('academic_year') && $student_count > 0) {
            return response()->json(
                [
                    "error" => "School Setting Update Failed!",
                    "message" => "Cannot modify academic year because $student_count enrolled student/s exist.",
                ],
                400
            );
        }

        if ($update_schoolSetting->isDirty('term_id') && $student_count > 0) {
            return response()->json(
                [
                    "error" => "School Setting Update Failed!",
                    "message" => "Cannot modify term because $student_count enrolled student/s exist.",
                ],
                400
            );
        }

        $update_schoolSetting->save();

        $update_schoolSetting->subjects()->sync($data['subjects']);
        OfferSubject::where('school_setting_id', $update_schoolSetting->id)
            ->whereNull('created_by')->update(['created_by' => auth()->user()->id, 'updated_by' => auth()->user()->id]);

        return response()->json($update_schoolSetting->fresh(['term'])->loadCount(['students', 'studentRegistrations', 'enrollmentHistories']));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  number $schoolSetting
     * @return \Illuminate\Http\Response
     */
    public function destroy($schoolSetting)
    {
        $delete_schoolSetting = SchoolSetting::withTrashed()->with(['term'])->withCount(['students', 'studentRegistrations', 'enrollmentHistories'])->findOrFail($schoolSetting);

        $registration_count = $delete_schoolSetting->student_registrations_count;
        $student_count = $delete_schoolSetting->student_registrations_count;
        $enrollment_count = $delete_schoolSetting->enrollment_histories_count;

        if ($registration_count === 0 && $student_count === 0 && $enrollment_count === 0) {
            $delete_schoolSetting->subjects()->detach();
            $delete_schoolSetting->forceDelete();
            return response($delete_schoolSetting);
        }

        $associated_students = max(array($student_count, $enrollment_count));

        return response()->json(
            [
                "error" => "Unable to Delete School Setting!",
                "message" => "School setting is associated to $associated_students student/s and $registration_count admission/s.",
            ],
            500
        );
    }
}
