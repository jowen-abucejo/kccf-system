<?php

namespace App\Models;

use App\Traits\UserStamps;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes, HasRoles, UserStamps;

    protected $guard_name = 'api';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * Get the user that created the User
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by')->withTrashed();
    }

    /**
     * Get all of the created enrolled subjects by the User
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function createdEnrolledSubjects(): HasMany
    {
        return $this->hasMany(EnrolledSubject::class, 'created_by');
    }

    /**
     * Get all of the created enrollment histories by the User
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function createdEnrollmentHistories(): HasMany
    {
        return $this->hasMany(EnrollmentHistory::class, 'created_by')->withTrashed();
    }

    /**
     * Get all of the created offer subjects by the User
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function createdOfferSubjects(): HasMany
    {
        return $this->hasMany(OfferSubject::class, 'created_by');
    }

    /**
     * Get all of the created programs by the User
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function createdPrograms(): HasMany
    {
        return $this->hasMany(Program::class, 'created_by')->withTrashed();
    }

    /**
     * Get all of the created program subjects by the User
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function createdProgramSubjects(): HasMany
    {
        return $this->hasMany(ProgramSubject::class, 'created_by')->withTrashed();
    }

    /**
     * Get all of the createdSchoolSettings for the User
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function createdSchoolSettings(): HasMany
    {
        return $this->hasMany(SchoolSetting::class, 'created_by')->withTrashed();
    }

    /**
     * Get all of the created students by the User
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function createdStudents(): HasMany
    {
        return $this->hasMany(Student::class, 'created_by')->withTrashed();
    }

    /**
     * Get all of the created student registrations by the User
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function createdStudentRegistrations(): HasMany
    {
        return $this->hasMany(StudentRegistration::class, 'created_by')->withTrashed();
    }

    /**
     * Get all of the created subjects by the User
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function createdSubjects(): HasMany
    {
        return $this->hasMany(Subject::class, 'created_by')->withTrashed();
    }

    /**
     * Get all of the created users by the User
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function createdUsers(): HasMany
    {
        return $this->hasMany(User::class, 'created_by')->withTrashed();
    }

    /**
     * Get the user that soft deleted the User
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function deletedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'deleted_by')->withTrashed();
    }

    /**
     * Get all of the soft deleted enrollment histories by the User
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function deletedEnrollmentHistories(): HasMany
    {
        return $this->hasMany(EnrollmentHistory::class, 'deleted_by')->withTrashed();
    }

    /**
     * Get all of the deleted levels by the User
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function deletedLevels(): HasMany
    {
        return $this->hasMany(Levels::class, 'deleted_by')->withTrashed();
    }

    /**
     * Get all of the soft deleted programs by the User
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function deletedPrograms(): HasMany
    {
        return $this->hasMany(Program::class, 'deleted_by')->withTrashed();
    }

    /**
     * Get all of the soft deleted program levels by the User
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function deletedProgramLevels(): HasMany
    {
        return $this->hasMany(ProgramLevel::class, 'deleted_by')->withTrashed();
    }

    /**
     * Get all of the soft deleted program subjects by the User
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function deletedProgramSubjects(): HasMany
    {
        return $this->hasMany(Subject::class, 'deleted_by')->withTrashed();
    }

    /**
     * Get all of the soft deleted school settings by the User
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function deletedSchoolSettings(): HasMany
    {
        return $this->hasMany(SchoolSetting::class, 'deleted_by')->withTrashed();
    }

    /**
     * Get all of the soft deleted students by the User
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function deletedStudents(): HasMany
    {
        return $this->hasMany(Student::class, 'deleted_by')->withTrashed();
    }

    /**
     * Get all of the soft deleted subjects by the User
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function deletedSubjects(): HasMany
    {
        return $this->hasMany(Subjects::class, 'deleted_by')->withTrashed();
    }

    /**
     * Get all of the soft deleted terms by the User
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function deletedTerms(): HasMany
    {
        return $this->hasMany(Term::class, 'deleted_by')->withTrashed();
    }

    /**
     * Get all of the soft deleted users by the User
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function deletedUsers(): HasMany
    {
        return $this->hasMany(User::class, 'deleted_by')->withTrashed();
    }

    /**
     * Get the student associated with the User
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function student(): HasOne
    {
        return $this->hasOne(Student::class);
    }

    /**
     * Get the student registration associated with the User
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function studentRegistration(): HasOne
    {
        return $this->hasOne(StudentRegistration::class);
    }

    /**
     * Get the user that last updated the User
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by')->withTrashed();
    }

    /**
     * Get all of the updatedEnrolledSubjects for the User
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function updatedEnrolledSubjects(): HasMany
    {
        return $this->hasMany(EnrolledSubject::class, 'created_by');
    }

    /**
     * Get all of the updated enrollment histories by the User
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function updatedEnrollmentHistories(): HasMany
    {
        return $this->hasMany(EnrollmentHistory::class, 'updated_by')->withTrashed();
    }

    /**
     * Get all of the updated offer subjects by the User
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function updatedOfferSubjects(): HasMany
    {
        return $this->hasMany(OfferSubject::class, 'updated_by');
    }

    /**
     * Get all of the updated programs for the User
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function updatedPrograms(): HasMany
    {
        return $this->hasMany(Comment::class, 'updated_by')->withTrashed();
    }

    /**
     * Get all of the updatedProgramSubjects by the User
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function updatedProgramSubjects(): HasMany
    {
        return $this->hasMany(ProgramSubject::class, 'updated_by')->withTrashed();
    }

    /**
     * Get all of the updated school settings by the User
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function updatedSchoolSettings(): HasMany
    {
        return $this->hasMany(SchoolSetting::class, 'updated_by')->withTrashed();
    }

    /**
     * Get all of the updated subjects by the User
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function updatedSubjects(): HasMany
    {
        return $this->hasMany(Subject::class, 'updated_by')->withTrashed();
    }

    /**
     * Get all of the updated students by the User
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function updatedStudents(): HasMany
    {
        return $this->hasMany(Student::class, 'updated_by')->withTrashed();
    }

    /**
     * Get all of the updated student registrations by the User
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function updatedStudentRegistrations(): HasMany
    {
        return $this->hasMany(StudentRegistration::class, 'updated_by')->withTrashed();
    }

    /**
     * Get all of the updated users by the User
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function updatedUsers(): HasMany
    {
        return $this->hasMany(User::class, 'updated_by')->withTrashed();
    }
}
