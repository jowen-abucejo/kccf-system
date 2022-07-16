<?php

namespace App\Models;

use App\Traits\UserStamps;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\SoftDeletes;

class Subject extends Model
{
    use HasFactory, UserStamps, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'code',
        'description',
        'lab_units',
        'lec_units',
        'comments',
        'created_by',
        'updated_by',
        'deleted_by'
    ];

    /**
     * Get the user that created the Subject
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by')->withTrashed();
    }

    /**
     * Get the user that soft deleted the Subject
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function deletedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'deleted_by')->withTrashed();
    }

    /**
     * The (previous) subjects that is equivalent to the Subject
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function equivalentPreviousSubjects(): BelongsToMany
    {
        return $this->belongsToMany(Subject::class, EquivalentSubject::class, 'subject_id', 'equal_subject_id');
    }

    /**
     * The (newer) subjects that is equivalent to the Subject
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function equivalentNewerSubjects(): BelongsToMany
    {
        return $this->belongsToMany(Subject::class, EquivalentSubject::class, 'equal_subject_id', 'subject_id');
    }

    /**
     * Get all of the offer subjects for the Subject
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function offerSubjects(): HasMany
    {
        return $this->hasMany(OfferSubject::class);
    }

    /**
     * The prerequisites subjects of the Subject
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function preRequisiteSubjects(): BelongsToMany
    {
        return $this->belongsToMany(Subject::class, PreRequisiteSubject::class, 'subject_id', 'required_subject_id');
    }

    /**
     * The subjects that prerequisite is the Subject
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function preRequisiteForSubjects(): BelongsToMany
    {
        return $this->belongsToMany(Subject::class, PreRequisiteSubject::class, 'required_subject_id', 'subject_id');
    }

    /**
     * The programs that belong to the Subject
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function programs(): BelongsToMany
    {
        return $this->belongsToMany(Program::class, ProgramSubject::class);
    }

    /**
     * Get the user that last updated the Subject
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by')->withTrashed();
    }
}
