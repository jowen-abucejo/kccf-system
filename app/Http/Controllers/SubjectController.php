<?php

namespace App\Http\Controllers;

use App\Models\Program;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class SubjectController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // User::create([
        //     'name' => 'Super Admin',
        //     'email' => 'jowennavalabucejo24@gmail.com',
        //     'password' => Hash::make('admin')
        // ]);

        // $program = Program::create([
        //     'code' => 'BSIT',
        //     'description' => "BS in Information Technology",
        // ]);

        // $program->subjects()->create([
        //     'code' => 'MATH1',
        //     'description' => 'Intermediate Algebra'
        // ]);

        // Subject::create([
        //     'code' => 'MATH2',
        //     'description' => "Algebra",
        // ]);
        // $new = new Subject([
        //     'code' => 'MATH2',
        //     'description' => 'Intermediate Algebra2',
        //     'lec_units' => 3,
        //     'lab_units' => 0
        // ]);
        // $new->save();

        // $new->progr
        // $sub->equivalentSubjects()->create([
        //     'code' => 'MATH1.1',
        //     'description' => 'Intermediate Algebra1',
        //     'lec_units' => 3,
        //     'lab_units' => 0
        // ]);
        // $eq = $sub->equivalentSubjects;
        // $new = Subject::find(4);
        // $new->preRequisiteSubjects()->create(['subject_id' => 2]);
        // return $new->preRequisiteSubjects;
        return response("Hello World");
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
    public function update(Request $request, Subject $subject)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Subject  $subject
     * @return \Illuminate\Http\Response
     */
    public function destroy(Subject $subject)
    {
        //
    }
}
