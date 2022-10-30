<?php

namespace App\Models;

use App\Traits\UserStamps;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class EnrollmentHistory extends Model
{
    use HasFactory, UserStamps, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'student_id',
        'school_setting_id',
        'program_id',
        'level_id',
        'student_type_id',
        'status',
        'reg_form_generated',
        'created_by',
        'updated_by',
        'deleted_by'
    ];

    /**
     * Get the user that created the record of EnrollmentHistory
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by')->withTrashed();
    }

    /**
     * Get the user that soft deleted the record of EnrollmentHistory
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function deletedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'deleted_by')->withTrashed();
    }

    /**
     * Get all of the enrolled subjects for the EnrollmentHistory
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function enrolledSubjects(): HasMany
    {
        return $this->hasMany(EnrolledSubject::class);
    }

    /**
     * Get the level that owns the EnrollmentHistory
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function level(): BelongsTo
    {
        return $this->belongsTo(Level::class)->withTrashed();
    }

    /**
     * The offerSubjects that belong to the EnrollmentHistory
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function offerSubjects(): BelongsToMany
    {
        return $this->belongsToMany(OfferSubject::class, EnrolledSubject::class)->withTimestamps();
    }

    /**
     * Get the program that owns the EnrollmentHistory
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function program(): BelongsTo
    {
        return $this->belongsTo(Program::class)->withTrashed();
    }

    /**
     * Get the studentType that owns the EnrollmentHistory
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function studentType(): BelongsTo
    {
        return $this->belongsTo(StudentType::class);
    }

    /**
     * Get the schoolSetting that owns the EnrollmentHistory
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function schoolSetting(): BelongsTo
    {
        return $this->belongsTo(SchoolSetting::class)->withTrashed();
    }

    /**
     * Get the student that owns the EnrollmentHistory
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class)->withTrashed();
    }

    /**
     * Get the last user that updated the record of EnrollmentHistory
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by')->withTrashed();
    }
}
