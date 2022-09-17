<?php

namespace App\Http\Controllers;

use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Support\Str;


class SubjectController extends Controller
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

        $subjects = Subject::when($request->withTrashed, function ($query) use ($search, $like) {
            $query->withTrashed()
                ->when($search, function ($query) use ($search, $like) {
                    $query->where('code', $like, '%' . $search . '%')
                        ->orWhere('description', $like, '%' . $search . '%');
                });
        })
            ->when(boolval($request->p), function ($query) use ($request) {
                $query->whereHas(
                    'programs',
                    function ($query) use ($request) {
                        $query->whereIn('programs.id', $request->input('p'));
                    }
                );
            })
            ->when($request->subject_status != 'ALL', function ($query) use ($request) {
                $query->when($request->subject_status == 'ACTIVE', function ($query) {
                    $query->whereNull('deleted_at');
                })
                    ->when($request->subject_status == 'INACTIVE', function ($query) {
                        $query->whereNotNull('deleted_at');
                    });
            })
            ->orderBy('code', 'ASC')->orderBy('description', 'ASC');

        if (!boolval($request->page)) {
            $subjects = $subjects->get();
        } else {
            $subjects = $subjects->paginate($limit);
        }

        return response()->json($subjects);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $data = $request->new_subject;

        $this->validate(
            $request,
            [
                'new_subject.code' =>
                [
                    'required',
                ],
                'new_subject.description' =>
                [
                    'required',
                ],
                'new_subject.lab_units' =>
                [
                    'required',
                ],
                'new_subject.lec_units' =>
                [
                    'required',
                ]
            ],
            [
                'new_subject.code.required' => 'Subject Code is required.',
                'new_subject.description.required' => 'Subject Description is required.',
                'new_subject.lab_units.required' => 'Laboratory Units is required.',
                'new_subject.lec_units.required' => 'Lecture Units is required.',
            ]
        );

        $new_subject = Subject::create([
            'code' => Str::upper(trim($data['code'])),
            'description' => Str::upper(preg_replace('/\s+/', ' ', trim($data['description']))),
            'lab_units' => $data['lab_units'],
            'lec_units' => $data['lec_units'],
        ]);
        return response()->json($new_subject->fresh());
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Subject  $subject
     * @return \Illuminate\Http\Response
     */
    public function show(Subject $subject)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Subject  $subject
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $subject)
    {
        $update_subject = Subject::withTrashed()->findOrFail($subject);
        $data = $request->new_subject;

        $this->validate(
            $request,
            [
                'new_subject.code' =>
                [
                    'required',
                ],
                'new_subject.description' =>
                [
                    'required',
                ],
                'new_subject.lab_units' =>
                [
                    'required',
                ],
                'new_subject.lec_units' =>
                [
                    'required',
                ]
            ],
            [
                'new_subject.code.required' => 'Subject Code is required.',
                'new_subject.description.required' => 'Subject Description is required.',
                'new_subject.lab_units.required' => 'Laboratory Units is required.',
                'new_subject.lec_units.required' => 'Lecture Units is required.',
            ]
        );

        $update_subject->update([
            'code' => Str::upper(trim($data['code'])),
            'description' => Str::upper(preg_replace('/\s+/', ' ', trim($data['description']))),
            'lab_units' => $data['lab_units'],
            'lec_units' => $data['lec_units'],
        ]);

        return response()->json($update_subject->fresh());
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  $subject
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request,  $subject)
    {
        $subject = subject::withTrashed()->findOrFail($subject);

        if (boolval($request->forceDelete)) {
            $subject->forceDelete();
            return response()->json(
                [
                    "message" => "Subject Permanently Deleted!",
                ],
                200
            );
        }

        if (boolval($request->toggle)) {
            if ($subject->trashed()) {
                $subject->restore();
            } else {
                $subject->delete();
            }
            return response()->json(
                [
                    "deleted_at" => $subject->deleted_at,
                ],
                200
            );
        }
    }
}
