<?php

namespace App\Http\Controllers;

use App\Models\ProgramSubject;
use Illuminate\Http\Request;

class ProgramSubjectController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $only_subjects = boolval($request->only_subject_ids);
        $programs_subjects = ProgramSubject::withTrashed(boolval($request->withTrashed))->with(['subject'])
            ->when($request->term_id, function ($query) use ($request, $only_subjects) {
                $query->distinct();
                $query->when($only_subjects, function ($query) use ($request) {
                    $query->select('subject_id');
                })->where('term_id', $request->term_id);
            })
            ->get();

        if ($only_subjects) $programs_subjects = $programs_subjects->pluck('subject_id');
        return response()->json($programs_subjects);
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
     * @param  \App\Models\ProgramSubject  $programSubject
     * @return \Illuminate\Http\Response
     */
    public function show(ProgramSubject $programSubject)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\ProgramSubject  $programSubject
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, ProgramSubject $programSubject)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\ProgramSubject  $programSubject
     * @return \Illuminate\Http\Response
     */
    public function destroy(ProgramSubject $programSubject)
    {
        //
    }
}
