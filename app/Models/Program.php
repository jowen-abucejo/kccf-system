<?php

namespace App\Models;

use App\Traits\UserStamps;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Program extends Model
{
    use HasFactory, UserStamps, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'program_level_id',
        'code',
        'description',
        'comments',
        'created_by',
        'updated_by',
        'deleted_by'
    ];

    /**
     * Get the user that created the Program
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by')->withTrashed();
    }

    /**
     * Get the user that soft deleted the Program
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function deletedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'deleted_by')->withTrashed();
    }

    /**
     * Get all of the enrollmentHistories for the Program
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function enrollmentHistories(): HasMany
    {
        return $this->hasMany(EnrollmentHistory::class);
    }

    /**
     * Get the programLevel that owns the Program
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function programLevel(): BelongsTo
    {
        return $this->belongsTo(ProgramLevel::class)->withTrashed();
    }

    /**
     * Get all of the student registrations for the Program
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function studentRegistrations(): HasMany
    {
        return $this->hasMany(StudentRegistration::class);
    }

    /**
     * Get all of the students for the Program
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function students(): HasMany
    {
        return $this->hasMany(Student::class)->withTrashed();
    }

    /**
     * The subjects that belong to the Program
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function subjects(): BelongsToMany
    {
        return $this->belongsToMany(Subject::class, ProgramSubject::class)->withPivot('level_id', 'term_id');
    }

    /**
     * Get the user that last updated the Program
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by')->withTrashed();
    }
}
