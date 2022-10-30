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

        $subjects = Subject::withTrashed(boolval($request->withTrashed))->with(['equivalentNewerSubjects', 'equivalentPreviousSubjects', 'preRequisiteSubjects'])
            ->withCount(['programs', 'preRequisiteForSubjects', 'schoolSettings'])
            ->when($search, function ($query) use ($search, $like) {
                $query->where(function ($query) use ($search, $like) {
                    $query->where('code', $like, '%' . $search . '%')
                        ->orWhere('description', $like, '%' . $search . '%');
                });
            })
            ->when(($request->exists('subject_status') && $request->subject_status == 'ACTIVE'), function ($query) use ($request) {
                $query->whereNull('deleted_at');
            })
            ->when(($request->exists('subject_status') && $request->subject_status == 'INACTIVE'), function ($query) {
                $query->whereNotNull('deleted_at');
            })
            ->when($request->exists('p'), function ($query) use ($request) {
                $program_filters = $request->input('p');
                $withNoPrograms = in_array(0, $program_filters);
                $query->where(function ($query) use ($program_filters, $withNoPrograms) {
                    $query->when($withNoPrograms, function ($query)  use ($program_filters) {
                        $query->whereDoesntHave('programs')
                            ->orWhereHas('programs', function ($query) use ($program_filters) {
                                $query->whereIn('program_id', $program_filters);
                            });
                    })->when(!$withNoPrograms, function ($query)  use ($program_filters) {
                        $query->whereHas('programs', function ($query) use ($program_filters) {
                            $query->whereIn('program_id', $program_filters);
                        });
                    });
                });
            })
            ->when($request->exists('t'), function ($query) use ($request) {
                $term_filters = $request->input('t');
                $query->whereHas('programs', function ($query) use ($term_filters) {
                    $query->when(in_array(0, $term_filters), function ($query)  use ($term_filters) {
                        $query->whereNull('term_id')
                            ->orWhereIn('term_id', $term_filters);
                    })->when(!in_array(0, $term_filters), function ($query)  use ($term_filters) {
                        $query->whereIn('term_id', $term_filters);
                    });
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

        $new_subject->equivalentPreviousSubjects()->sync($data['equivalent_subjects']);

        return response()->json($new_subject->fresh(['equivalentPreviousSubjects', 'equivalentNewerSubjects'])->loadCount(['programs', 'schoolSettings', 'preRequisiteForSubjects']));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Subject  $subject
     * @return \Illuminate\Http\Response
     */
    public function show(Subject $subject)
    {
        return $subject->fresh(['equivalentPreviousSubjects', 'equivalentNewerSubjects'])->loadCount(['programs', 'schoolSettings', 'preRequisiteForSubjects']);
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

        $update_subject->equivalentPreviousSubjects()->sync($data['equivalent_subjects']);
        $update_subject->preRequisiteSubjects()->sync($data['prerequisite_subjects']);

        return response()->json($update_subject->fresh(['equivalentPreviousSubjects', 'equivalentNewerSubjects', 'preRequisiteSubjects'])->loadCount(['programs', 'schoolSettings', 'preRequisiteForSubjects']));
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
        $subject = Subject::withTrashed()->withCount(['equivalentNewerSubjects', 'equivalentPreviousSubjects', 'programs', 'preRequisiteForSubjects', 'schoolSettings'])->findOrFail($subject);

        if (boolval($request->forceDelete)) {
            $associated_subjects = $subject->equivalent_newer_subjects_count + $subject->equivalent_previous_subjects_count + $subject->prerequisite_for_subjects_count;
            $associated_programs = $subject->programs_count;
            $associated_school_settings = $subject->school_settings_count;
            if ($associated_programs > 0 || $associated_subjects > 0) {
                return response()->json(
                    [
                        "error" => "Unable to Delete Subject!",
                        "message" => "Subject is currently associated to $associated_programs program/s, $associated_subjects subject/s and offered in $associated_school_settings semester/s",
                    ],
                    500
                );
            }

            $subject->preRequisiteSubjects()->detach();
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
