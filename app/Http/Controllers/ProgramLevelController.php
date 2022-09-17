<?php

namespace App\Http\Controllers;

use App\Models\ProgramLevel;
use Illuminate\Http\Request;

class ProgramLevelController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $program_levels = ProgramLevel::all();
        return response()->json($program_levels);
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
     * @param  \App\Models\ProgramLevel  $programLevel
     * @return \Illuminate\Http\Response
     */
    public function show(ProgramLevel $programLevel)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\ProgramLevel  $programLevel
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, ProgramLevel $programLevel)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\ProgramLevel  $programLevel
     * @return \Illuminate\Http\Response
     */
    public function destroy(ProgramLevel $programLevel)
    {
        //
    }
}
