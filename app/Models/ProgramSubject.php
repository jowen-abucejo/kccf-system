<?php

namespace App\Models;

use App\Traits\UserStamps;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProgramSubject extends Model
{
    use HasFactory, UserStamps, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'program_id',
        'subject_id',
        'term_id',
        'level_id',
        'created_by',
        'updated_by',
        'deleted_by'
    ];

    /**
     * Get the user that created the ProgramSubject
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by')->widthTrashed();
    }

    /**
     * Get the user that soft deleted the ProgramSubject
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function deletedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'deleted_by')->widthTrashed();
    }

    /**
     * Get the level that owns the ProgramSubject
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function level(): BelongsTo
    {
        return $this->belongsTo(Level::class)->withTrashed();
    }

    /**
     * Get all of the preRequisiteSubjects for the ProgramSubject
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasManyThrough
     */
    public function preRequisiteSubjects(): HasManyThrough
    {
        return $this->hasManyThrough(Subject::class, PreRequisiteSubject::class)->withTrashedParents();
    }

    /**
     * Get the program that owns the ProgramSubject
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function program(): BelongsTo
    {
        return $this->belongsTo(Program::class)->withTrashed();
    }

    /**
     * Get the subject that owns the ProgramSubject
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class)->withTrashed();
    }

    /**
     * Get the term that owns the ProgramSubject
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function term(): BelongsTo
    {
        return $this->belongsTo(Term::class)->withTrashed();
    }

    /**
     * Get the user that last updated the ProgramSubject
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by')->withTrashed();
    }
}
