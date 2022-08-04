<?php

namespace App\Http\Controllers;

use App\Models\Student;
use Carbon\Carbon;
use Illuminate\Http\Request;

class StudentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $limit = $request->limit;
        $search = $request->search;
        $start_date = $request->start_date ? Carbon::createFromFormat('Y-m-d H:i:s', $request->start_date)->startOfDay()->toDateTimeString() : null;
        $end_date = $request->end_date ? Carbon::createFromFormat('Y-m-d H:i:s', $request->end_date)->endOfDay()->toDateTimeString() : null;
        $like = 'LIKE';
        $paginated = Student::withTrashed()->with(['level', 'program', 'registration.educationBackgrounds', 'registration.guardians', 'registration.level', 'registration.program', 'registration.schoolSetting', 'registration.siblings', 'schoolSetting'])
            ->when($start_date && $end_date, function ($query) use ($start_date, $end_date) {
                $query->where('created_at', '<=', $end_date)->where('created_at', '>=', $start_date);
            })
            ->when($search, function ($query) use ($like, $search) {
                $query->whereHas('registration', function ($query) use ($like, $search) {
                    $query->where('last_name', $like,  '%' . $search . '%')
                        ->orWhere('first_name', $like,  '%' . $search . '%');
                })
                    ->orWhere('student_number', $like, '%' . $search . '%');
            })
            ->paginate($limit);

        return response()->json($paginated);
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
     * @param  \App\Models\Student  $student
     * @return \Illuminate\Http\Response
     */
    public function show(Student $student)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Student  $student
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Student $student)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Student  $student
     * @return \Illuminate\Http\Response
     */
    public function destroy(Student $student)
    {
        //
    }
}
