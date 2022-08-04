<?php

namespace App\Http\Controllers;

use App\Models\SchoolSetting;
use Illuminate\Http\Request;

class SchoolSettingController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return response()->json(SchoolSetting::with('term')->get());
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
        $new_setting = SchoolSetting::create([
            'academic_year' => $data['academic_year'],
            'term_id' => $data['term'],
            'encoding_start_date' => $data['encoding_start_date'],
            'encoding_end_date' => $data['encoding_end_date'],
            'enrollment_start_date' => $data['enrollment_start_date'],
            'enrollment_end_date' => $data['enrollment_end_date'],
        ]);
        return response()->json($new_setting);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\SchoolSetting  $schoolSetting
     * @return \Illuminate\Http\Response
     */
    public function show(SchoolSetting $schoolSetting)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\SchoolSetting  $schoolSetting
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, SchoolSetting $schoolSetting)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\SchoolSetting  $schoolSetting
     * @return \Illuminate\Http\Response
     */
    public function destroy(SchoolSetting $schoolSetting)
    {
        //
    }
}
