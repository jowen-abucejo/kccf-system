<?php

namespace App\Http\Controllers;

use App\Models\SchoolSetting;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SchoolSettingController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $limit = $request->limit;
        $search = $request->search ?? '';
        $like = 'LIKE';

        $school_settings = SchoolSetting::with('term')->orderBy('academic_year', 'DESC')->orderBy('term_id', 'DESC')
            ->when($request->withTrashed, function ($query) use ($search, $like) {
                $query
                    ->when($search, function ($query) use ($search, $like) {
                        $query->where('academic_year', $like, '%' . $search . '%');
                    });
            })
            ->when($request->settings_status != 'ALL', function ($query) use ($request) {
                $query->when($request->settings_status == 'OPEN_ENROLL', function ($query) {
                    $query->whereDate('enrollment_end_date', '>', Carbon::now());
                })
                    ->when($request->settings_status == 'OPEN_ENCODE', function ($query) {
                        $query->whereDate('encoding_end_date', '>', Carbon::now());
                    });
            });

        if (!boolval($request->page)) {
            $school_settings = $school_settings->get();
        } else {
            $school_settings = $school_settings->paginate($limit);
        }

        return response()->json($school_settings);
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

        $this->validate(
            $request,
            [
                'new_setting.academic_year' =>
                [
                    'required',
                ],
                'new_setting.term' =>
                [
                    'bail',
                    'required',
                    Rule::unique('school_settings', 'term_id')->where('academic_year', $data['academic_year'])
                ]
            ],
            [
                'new_setting.academic_year.required' => 'Academic year is required.',
                'new_setting.term.required' => 'Term is required.',
                'new_setting.term.unique' => 'Setting with same academic year and term already exist',
            ]
        );

        $new_setting = SchoolSetting::create([
            'academic_year' => $data['academic_year'],
            'term_id' => $data['term'],
            'encoding_start_date' => $data['encoding_start_date'],
            'encoding_end_date' => $data['encoding_end_date'],
            'enrollment_start_date' => $data['enrollment_start_date'],
            'enrollment_end_date' => $data['enrollment_end_date'],
        ]);
        return response()->json($new_setting->fresh(['term']));
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
        $data = $request->new_setting;
        $this->validate(
            $request,
            [
                'new_setting.academic_year' =>
                [
                    'bail',
                    'required',
                ],
                'new_setting.term' =>
                [
                    'bail',
                    'required',
                    Rule::unique('school_settings', 'term_id')->where('academic_year', $data['academic_year'])->whereNot('id', $schoolSetting->id)
                ]
            ],
            [
                'new_setting.academic_year.required' => 'Academic year is required.',
                'new_setting.term.required' => 'Term is required.',
                'new_setting.term.unique' => 'Setting with same academic year and term already exist',
            ]
        );

        $schoolSetting->academic_year = $data['academic_year'];
        $schoolSetting->term_id = $data['term'];
        $schoolSetting->enrollment_start_date = $data['enrollment_start_date'];
        $schoolSetting->enrollment_end_date = $data['enrollment_end_date'];
        $schoolSetting->encoding_start_date = $data['encoding_start_date'];
        $schoolSetting->encoding_end_date = $data['encoding_end_date'];
        $schoolSetting->save();

        return response()->json($schoolSetting->fresh(['term']));
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
