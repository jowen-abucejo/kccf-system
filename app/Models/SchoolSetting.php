<?php

namespace App\Models;

use App\Traits\UserStamps;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class SchoolSetting extends Model
{
    use HasFactory, UserStamps, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'academic_year',
        'term_id',
        'encoding_start_date',
        'encoding_end_date',
        'enrollment_start_date',
        'enrollment_end_date',
        'created_by',
        'updated_by',
        'deleted_by'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'encoding_start_date' => 'datetime',
        'encoding_end_date' => 'datetime',
        'enrollment_start_date' => 'datetime',
        'enrollment_end_date' => 'datetime',
    ];

    /**
     * Get the user that created the record of SchoolSetting
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by')->withTrashed();
    }

    /**
     * Get the user that soft deleted the record of SchoolSetting
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function deletedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'deleted_by')->withTrashed();
    }

    /**
     * Get all of the enrollmentHistories for the SchoolSetting
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function enrollmentHistories(): HasMany
    {
        return $this->hasMany(EnrollmentHistory::class);
    }

    /**
     * Get all of the offer subjects for the SchoolSetting
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function offerSubjects(): HasMany
    {
        return $this->hasMany(OfferSubject::class);
    }

    /**
     * Get all of the student registrations for the SchoolSetting
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function studentRegistrations(): HasMany
    {
        return $this->hasMany(StudentRegistration::class);
    }

    /**
     * Get all of the students for the SchoolSetting
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function students(): HasMany
    {
        return $this->hasMany(Student::class)->withTrashed();
    }

    /**
     * Get the term that owns the SchoolSetting
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function term(): BelongsTo
    {
        return $this->belongsTo(Term::class)->withTrashed();
    }

    /**
     * Get the last user that updated the record of SchoolSetting
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by')->withTrashed();
    }
}
