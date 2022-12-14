<?php

namespace App\Http\Controllers;

use App\Models\EnrolledSubject;
use Illuminate\Http\Request;

class EnrolledSubjectController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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
     * @param  \App\Models\EnrolledSubject  $enrolledSubject
     * @return \Illuminate\Http\Response
     */
    public function show(EnrolledSubject $enrolledSubject)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\EnrolledSubject  $enrolledSubject
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, EnrolledSubject $enrolledSubject)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\EnrolledSubject  $enrolledSubject
     * @return \Illuminate\Http\Response
     */
    public function destroy(EnrolledSubject $enrolledSubject)
    {
        //
    }

    public function countEnrolledSubjects($program_id, $subject_id)
    {
        $count =  EnrolledSubject::whereHas('enrollmentHistory', function ($query) use ($program_id) {
            $query->where('program_id', $program_id);
        })->whereHas('offerSubject', function ($query) use ($subject_id) {
            $query->where('subject_id', $subject_id);
        })->count();

        return $count;
    }
}
