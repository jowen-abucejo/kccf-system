<?php

namespace App\Http\Controllers;

use App\Models\Program;
use App\Models\ProgramSubject;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ProgramController extends Controller
{
    /**
     * Display a listing of the resource.
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $limit = $request->limit;
        $search = $request->search ?? '';
        $like = 'LIKE';

        $programs = Program::when($request->withTrashed, function ($query) use ($search, $like) {
            $query->withTrashed()->with(['programLevel'])->withCount(['enrollmentHistories', 'students', 'studentRegistrations'])
                ->when($search, function ($query) use ($search, $like) {
                    $query->where('code', $like, '%' . $search . '%')
                        ->orWhere('description', $like, '%' . $search . '%');
                });
        })
            ->when($request->exists('program_status') && $request->program_status != 'ALL', function ($query) use ($request) {
                $query->when($request->program_status == 'ACTIVE', function ($query) {
                    $query->whereNull('deleted_at');
                })
                    ->when($request->program_status == 'INACTIVE', function ($query) {
                        $query->whereNotNull('deleted_at');
                    });
            })
            ->when($request->exists('pl'), function ($query) use ($request) {
                $query->whereIn('program_level_id', $request->input('pl'));
            })
            ->orderBy('code', 'ASC')->orderBy('description', 'ASC');

        if (!boolval($request->page)) {
            $programs = $programs->get();
        } else {
            $programs = $programs->paginate($limit);
        }

        return response()->json($programs);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $data = $request->new_program;

        $this->validate(
            $request,
            [
                'new_program.code' =>
                [
                    'required',
                ],
                'new_program.description' =>
                [
                    'required',
                ],
                'new_program.program_level_id' =>
                [
                    'required',
                ],
            ],
            [
                'new_program.code.required' => 'Program Code is required.',
                'new_program.description.required' => 'Program Description is required.',
                'new_program.lab_units.required' => 'Education Level is required.',
            ]
        );

        $new_program = Program::create([
            'code' => Str::upper(trim($data['code'])),
            'description' => Str::upper(preg_replace('/\s+/', ' ', trim($data['description']))),
            'program_level_id' => $data['program_level_id'],
        ]);

        $to_add_subjects = array();

        foreach ($data['subjects'] as $ps) {
            $ps['program_id'] = $new_program->id;
            $ps['updated_by'] = auth()->user()->id;
            $ps['created_by'] = auth()->user()->id;
            array_push($to_add_subjects, $ps);
        }

        ProgramSubject::upsert($to_add_subjects, ['program_id', 'subject_id', 'term_id', 'level_id', 'created_by', 'updated_by'], []);
        return response()->json($new_program->fresh(['programLevel'])->loadCount('enrollmentHistories', 'students', 'studentRegistrations'));
    }

    /**
     * Display the specified resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  $program
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $program)
    {
        return Program::withTrashed()->with(['programLevel', 'subjects' => function ($query) use ($request) {
            $query->whereNull('program_subjects.deleted_at')->withTrashed(boolval($request->withTrashed))->withCount('schoolSettings');
        }])->findOrFail($program);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  $program
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $program)
    {
        $update_program = Program::withTrashed()->with('programLevel')->findOrFail($program);
        $data = $request->new_program;

        $update_program->update([
            'code' => Str::upper(trim($data['code'])),
            'description' => Str::upper(preg_replace('/\s+/', ' ', trim($data['description']))),
            'program_level_id' => $data['program_level_id'],
        ]);

        //subjects to sync
        $to_add_subjects = $data['subjects'];


        //restore if subject is already attached but soft deleted, create if not yet attached
        foreach ($to_add_subjects as $ps) {
            $program_subject = ProgramSubject::withTrashed()->where([
                [
                    'program_id', '=', $ps['program_id'],
                ],
                [
                    'subject_id', '=', $ps['subject_id']
                ]
            ])->first()
                ?: ProgramSubject::create([
                    'program_id' => $ps['program_id'],
                    'subject_id' => $ps['subject_id'],
                    'term_id' => $ps['term_id'],
                    'level_id' => $ps['level_id'],

                ]);
            if ($program_subject->trashed()) {
                $program_subject->restore();
            }

            $program_subject->term_id = $ps['term_id'];
            $program_subject->level_id = $ps['level_id'];

            $program_subject->save();
        }

        //ids of subject to attached
        $subject_ids = array_column($to_add_subjects, 'subject_id');

        //subject ids that will be detached
        $pending_detach_subjects = ProgramSubject::withTrashed()->whereNotIn('subject_id', $subject_ids)->where('program_id', $update_program->id)->get();
        $to_soft_delete_program_subjects = array();

        if ($pending_detach_subjects->count() > 0) {
            foreach ($pending_detach_subjects as $ps) {
                //check if subject id should be soft deleted/detached only from program
                $enrolled_subject_count = app('App\Http\Controllers\EnrolledSubjectController')->countEnrolledSubjects($update_program->id, $ps->subject_id);
                if ($enrolled_subject_count) {
                    array_push($to_soft_delete_program_subjects, $ps->subject_id);
                }
            }
            //keep soft deleted subject ids
            $subject_ids = array_merge($subject_ids, $to_soft_delete_program_subjects);
        }

        $update_program->subjects()->sync($subject_ids);
        ProgramSubject::whereIn('subject_id', $to_soft_delete_program_subjects)->update(["deleted_at" => Carbon::now()]);

        return response()->json($update_program->fresh(['programLevel'])->loadCount('enrollmentHistories', 'students', 'studentRegistrations'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  $program
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request,  $program)
    {
        $deleted_program = Program::withTrashed()->withCount('enrollmentHistories', 'students', 'studentRegistrations')->findOrFail($program);

        if (boolval($request->forceDelete)) {
            $enrollment_count = $deleted_program->enrollment_histories_count;
            $student_count = $deleted_program->students_count;
            $admission_count = $deleted_program->student_registrations_count;
            if ($enrollment_count > 0 || $student_count > 0 || $admission_count > 0) {
                return response()->json(
                    [
                        "error" => "Unable to Delete Curriculum!",
                        "message" => "Curriculum is currently associated to $enrollment_count enrollment/s, $student_count student/s and $admission_count admission/s",
                    ],
                    500
                );
            }
            $deleted_program->forceDelete();
            return response()->json(
                [
                    "message" => "Program Permanently Deleted!",
                ],
                200
            );
        }

        if (boolval($request->toggle)) {
            if ($deleted_program->trashed()) {
                $deleted_program->restore();
            } else {
                $deleted_program->delete();
            }
            return response()->json(
                [
                    "deleted_at" => $deleted_program->deleted_at,
                ],
                200
            );
        }
    }
}
