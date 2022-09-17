<?php

namespace App\Http\Controllers;

use App\Models\StudentType;
use Illuminate\Http\Request;

class StudentTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $student_types = StudentType::all();

        return response()->json($student_types);
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
     * @param  \App\Models\StudentType  $studentType
     * @return \Illuminate\Http\Response
     */
    public function show(StudentType $studentType)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\StudentType  $studentType
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, StudentType $studentType)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\StudentType  $studentType
     * @return \Illuminate\Http\Response
     */
    public function destroy(StudentType $studentType)
    {
        //
    }
}
