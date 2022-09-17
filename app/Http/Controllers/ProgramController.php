<?php

namespace App\Http\Controllers;

use App\Models\Program;
use App\Models\ProgramSubject;
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
            $query->withTrashed()->with('programLevel')
                ->when($search, function ($query) use ($search, $like) {
                    $query->where('code', $like, '%' . $search . '%')
                        ->orWhere('description', $like, '%' . $search . '%');
                });
        })
            ->when($request->program_status != 'ALL', function ($query) use ($request) {
                $query->when($request->program_status == 'ACTIVE', function ($query) {
                    $query->whereNull('deleted_at');
                })
                    ->when($request->program_status == 'INACTIVE', function ($query) {
                        $query->whereNotNull('deleted_at');
                    });
            })
            ->when(boolval($request->pl), function ($query) use ($request) {
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
        return response()->json($new_program->fresh('programLevel'));
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
        return Program::withTrashed()->with(['subjects' => function ($query) use ($request) {
            $query->withTrashed(boolval($request->withTrashed))->withCount('offerSubjects');
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
        $update_program = Program::withTrashed()->findOrFail($program);
        $data = $request->new_program;

        $update_program->update([
            'code' => Str::upper(trim($data['code'])),
            'description' => Str::upper(preg_replace('/\s+/', ' ', trim($data['description']))),
            'program_level_id' => $data['program_level_id'],
        ]);

        $to_add_subjects = $data['subjects'];

        foreach ($to_add_subjects as $ps) {
            ProgramSubject::updateOrCreate([
                'program_id' => $ps['program_id'],
                'subject_id' => $ps['subject_id']
            ], [
                'term_id' => $ps['term_id'],
                'level_id' => $ps['level_id'],
            ]);
        }

        $subject_ids = array_column($to_add_subjects, 'subject_id');
        $update_program->subjects()->sync($subject_ids);

        return response()->json($update_program->fresh('programLevel'));
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
        $program = Program::withTrashed()->findOrFail($program);

        if (boolval($request->forceDelete)) {
            $program->forceDelete();
            return response()->json(
                [
                    "message" => "Program Permanently Deleted!",
                ],
                200
            );
        }

        if (boolval($request->toggle)) {
            if ($program->trashed()) {
                $program->restore();
            } else {
                $program->delete();
            }
            return response()->json(
                [
                    "deleted_at" => $program->deleted_at,
                ],
                200
            );
        }
    }
}
