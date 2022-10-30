<?php

namespace App\Http\Controllers;

use App\Mail\NewPassword;
use App\Models\StudentRegistration;
use App\Models\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class StudentRegistrationController extends Controller
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
        try {
            $data = $request->registration;

            $first_name = Str::title($data['basic_info']['first_name']);
            $middle_name = Str::title($data['basic_info']['middle_name']);
            $last_name = Str::title($data['basic_info']['last_name']);
            $name_suffix = Str::upper($data['basic_info']['name_suffix']);
            $email = $data['basic_info']['email'];

            //create student registration model
            $registration = StudentRegistration::create([
                'school_setting_id' => $data['admission_details']['school_setting_id'],
                'program_id' => $data['admission_details']['program_id'],
                'level_id' => $data['admission_details']['level_id'],
                'student_type_id' => $data['admission_details']['student_type_id'],
                'last_name' => $last_name,
                'first_name' => $first_name,
                'middle_name' => $middle_name,
                'name_suffix' => $name_suffix,
                'sex' => $data['basic_info']['sex'],
                'civil_status' => $data['basic_info']['civil_status'],
                'birth_date' => $data['basic_info']['birth_date'],
                'birth_place' => $data['basic_info']['birth_place'],
                'address' => $data['basic_info']['address'],
                'email' => $email,
                'contact_number' => $data['basic_info']['contact_number'],
                'religion' => $data['family_info']['religion_info'],
                'other_info' => $data['family_info']['other_info'],
            ]);

            //Associate siblings,parents and guardian to registration
            if (count($data['family_info']['siblings']) > 0)
                $registration->siblings()->createMany($data['family_info']['siblings']);

            if (!boolval($data['mother_info']['is_deceased']))
                $data['mother_info']['address'] = $data['mother_info']['same_address'] ? $data['basic_info']['address'] : $data['mother_info']['address'];
            if (!boolval($data['father_info']['is_deceased']))
                $data['father_info']['address'] = $data['father_info']['same_address'] ? $data['basic_info']['address'] : $data['father_info']['address'];

            $guardians = array($data['mother_info'], $data['father_info']);

            //save guardian's info if not set as mother or father
            if (array_key_exists('guardian_info', $data) && $data['guardian_info']['relationship'] == "other") {
                $data['guardian_info']['address'] = $data['guardian_info']['same_address'] ? $data['basic_info']['address'] : $data['guardian_info']['address'];
                array_push($guardians, $data['guardian_info']);
            }

            $registration->guardians()->createMany($guardians);

            //save educational background
            $education_backgrounds = array();
            if (array_key_exists('preschool', $data['education'])) {
                array_push($education_backgrounds, $data['education']['preschool']);
                if (array_key_exists('grade_school', $data['education'])) {
                    array_push($education_backgrounds, $data['education']['grade_school']);
                    if (array_key_exists('junior_high', $data['education'])) {
                        array_push($education_backgrounds, $data['education']['junior_high']);
                        if (array_key_exists('senior_high', $data['education'])) {
                            array_push($education_backgrounds, $data['education']['senior_high']);
                            if (array_key_exists('college', $data['education'])) {
                                array_push($education_backgrounds, $data['education']['college']);
                            }
                        }
                    }
                }
            }
            $registration->educationBackgrounds()->createMany($education_backgrounds);

            //create user account for student
            $random_password = app('App\Http\Controllers\UserController')->generatePassword();
            $full_name = $first_name . ' ' . $middle_name . ' ' . $last_name . ' ' . $name_suffix;
            $new_user = User::create([
                'name' => trim($full_name),
                'email' => $email,
                'password' => Hash::make($random_password),
            ]);
            $new_user->assignRole('student');

            $registration->user()->associate($new_user)->save();

            //create student record for the registration
            $student =  $new_user->student()->create([
                'user_id' => $new_user->id,
                'school_setting_id' => $data['admission_details']['school_setting_id'],
                'program_id' => $data['admission_details']['program_id'],
                'level_id' => $data['admission_details']['level_id'],
                'student_type_id' => $data['admission_details']['student_type_id'],
                'admission_datetime' => $data['admission_details']['registration_date'] ?? Carbon::now("Asia/Manila")->format('Y-m-d H:i:s')
            ]);

            //associate the new student to the registration model
            $registration->student()->associate($student)->save();
        } catch (\Throwable $th) {
            if (isset($registration) && $registration) {
                if ($registration->student()->count() > 0) {
                    $registration->student()->forceDelete();
                } else {
                    $registration->delete();
                }

                if (isset($new_user)) {
                    $new_user->roles()->detach();
                    $new_user->forceDelete();
                }
            }
            return response()->json(
                [
                    "error" => "Student Registration Failed!",
                    "message" => $th->getMessage(),
                ],
                500
            );
        }

        //send email notification
        try {
            $new_email = new NewPassword($random_password);
            Mail::to(
                $email,
                $full_name
            )->send($new_email);
        } catch (\Throwable $th) {
        }


        return response()->json(
            app('App\Http\Controllers\StudentController')->show($student->id)
        );
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\StudentRegistration  $studentRegistration
     * @return \Illuminate\Http\Response
     */
    public function show(StudentRegistration $studentRegistration)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\StudentRegistration  $studentRegistration
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, StudentRegistration $studentRegistration)
    {
        try {
            $data = $request->registration;

            $first_name = Str::title($data['basic_info']['first_name']);
            $middle_name = Str::title($data['basic_info']['middle_name']);
            $last_name = Str::title($data['basic_info']['last_name']);
            $name_suffix = Str::upper($data['basic_info']['name_suffix']);
            $email = $data['basic_info']['email'];

            //update student registration
            $studentRegistration->school_setting_id = $data['admission_details']['school_setting_id'];
            $studentRegistration->program_id = $data['admission_details']['program_id'];
            $studentRegistration->level_id = $data['admission_details']['level_id'];
            $studentRegistration->student_type_id = $data['admission_details']['student_type_id'];
            $studentRegistration->last_name = $last_name;
            $studentRegistration->first_name = $first_name;
            $studentRegistration->middle_name = $middle_name;
            $studentRegistration->name_suffix = $name_suffix;
            $studentRegistration->sex = $data['basic_info']['sex'];
            $studentRegistration->civil_status = $data['basic_info']['civil_status'];
            $studentRegistration->birth_date = $data['basic_info']['birth_date'];
            $studentRegistration->birth_place = $data['basic_info']['birth_place'];
            $studentRegistration->address = $data['basic_info']['address'];
            $studentRegistration->email = $email;
            $studentRegistration->contact_number = $data['basic_info']['contact_number'];
            $studentRegistration->religion = $data['family_info']['religion_info'];
            $studentRegistration->other_info = $data['family_info']['other_info'];
            $studentRegistration->save();

            //sync nad update siblings
            $sibling_ids = array();
            foreach ($data['family_info']['siblings'] as $sibling) {
                $updated = $studentRegistration->siblings()->updateOrCreate(
                    ['id' => $sibling['id']],
                    [
                        'last_name' => $sibling['last_name'],
                        'first_name' => $sibling['first_name'],
                        'middle_name' => $sibling['middle_name'],
                        'name_suffix' => $sibling['name_suffix'],
                        'birth_date' => $sibling['birth_date'],
                    ]
                );
                array_push($sibling_ids, $updated->id);
            }
            $studentRegistration->siblings()->whereNotIn('id', $sibling_ids)->delete();

            //update guardians
            $guardians = array($data['mother_info'], $data['father_info']);
            if (array_key_exists('guardian_info', $data) && $data['guardian_info']['relationship'] == "other") {
                $data['guardian_info']['address'] = $data['guardian_info']['same_address'] ? $data['basic_info']['address'] : $data['guardian_info']['address'];
                array_push($guardians, $data['guardian_info']);
            }

            //sync guardians
            $guardian_ids = array();
            foreach ($guardians as $guardian) {
                $updated = $studentRegistration->guardians()->updateOrCreate(
                    ['id' => $guardian['id']],
                    [
                        'last_name' => $guardian['last_name'],
                        'first_name' => $guardian['first_name'],
                        'middle_name' => $guardian['middle_name'],
                        'name_suffix' => array_key_exists('name_suffix', $guardian) ? $guardian['name_suffix'] : '',
                        'birth_date' => $guardian['birth_date'],
                        'occupation' => $guardian['occupation'] ?? null,
                        'address' => $guardian['address'] ?? null,
                        'contact_number' => $guardian['contact_number'] ?? null,
                        'email' => $guardian['email'] ?? null,
                        'relationship' => $guardian['relationship'],
                        'is_deceased' => $guardian['is_deceased'],
                        'is_guardian' => $guardian['is_guardian'],
                    ]
                );
                array_push($guardian_ids, $updated->id);
            }
            $studentRegistration->guardians()->whereNotIn('id', $guardian_ids)->delete();

            //update educational background
            $education_backgrounds = array();
            if (array_key_exists('preschool', $data['education']) && $data['education']['preschool']['school_name']) {
                array_push($education_backgrounds, $data['education']['preschool']);
                if (array_key_exists('grade_school', $data['education']) && $data['education']['grade_school']['school_name']) {
                    array_push($education_backgrounds, $data['education']['grade_school']);
                    if (array_key_exists('junior_high', $data['education']) && $data['education']['junior_high']['school_name']) {
                        array_push($education_backgrounds, $data['education']['junior_high']);
                        if (array_key_exists('senior_high', $data['education']) && $data['education']['senior_high']['school_name']) {
                            array_push($education_backgrounds, $data['education']['senior_high']);
                            if (array_key_exists('college', $data['education']) && $data['education']['college']['school_name']) {
                                array_push($education_backgrounds, $data['education']['college']);
                            }
                        }
                    }
                }
            }
            //sync education backgrounds
            $education_background_ids = array();
            foreach ($education_backgrounds as $education) {
                $background = $studentRegistration->educationBackgrounds()->updateOrCreate(
                    ['id' => $education['id']],
                    [
                        'school_name' => $education['school_name'],
                        'school_address' => $education['school_address'],
                        'academic_year' => $education['academic_year'],
                        'program' => $education['program'],
                    ]
                );
                array_push($education_background_ids, $background->id);
            }
            $studentRegistration->educationBackgrounds()->whereNotIn('id', $education_background_ids)->delete();

            //update user account for student
            $full_name = $first_name . ' ' . $middle_name . ' ' . $last_name . ' ' . $name_suffix;
            $student = $studentRegistration->student;
            $user = $student->user;
            $user->name = $full_name;
            $user->email = $email;

            //update student if no enrollment histories yet to school
            if ($student->enrollmentHistories()->count() === 0) {
                $student->program_id = $data['admission_details']['program_id'];
                $student->level_id = $data['admission_details']['level_id'];
                $student->school_setting_id = $data['admission_details']['school_setting_id'];
                $student->student_type_id = $data['admission_details']['student_type_id'];
            }

            $student->admission_datetime = $data['admission_details']['registration_date'];
            $student->save();


            //send email notification if email was updated
            if ($user->isDirty('email')) {
                try {
                    $random_password = app('App\Http\Controllers\UserController')->generatePassword();
                    $new_email = new NewPassword($random_password, "Student Account Updated");
                    Mail::to(
                        $email,
                        $full_name
                    )->send($new_email);
                    $user->password = Hash::make($random_password);
                    $user->save();
                } catch (\Throwable $th) {
                }
            } else {
                $user->save();
            }
        } catch (\Throwable $th) {
            return response()->json(
                [
                    "error" => "Student Admission Update Failed!",
                    "message" => $th->getMessage(),
                ],
                500
            );
        }

        return response()->json(
            app('App\Http\Controllers\StudentController')->show($student->id)
        );
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\StudentRegistration  $studentRegistration
     * @return \Illuminate\Http\Response
     */
    public function destroy(StudentRegistration $studentRegistration)
    {
        //
    }
}
