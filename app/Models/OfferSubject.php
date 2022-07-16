<?php

namespace App\Models;

use App\Traits\UserStamps;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class OfferSubject extends Model
{
    use HasFactory, UserStamps;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'subject_id',
        'school_setting_id',
        'created_by',
        'updated_by',
    ];

    /**
     * Get the user that created the OfferSubject
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by')->withTrashed();
    }

    /**
     * Get all of the enrolled subjects for the OfferSubject
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function enrolledSubjects(): HasMany
    {
        return $this->hasMany(EnrolledSubject::class);
    }

    /**
     * Get all of the school setting for the OfferSubject
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function schoolSetting(): HasMany
    {
        return $this->hasMany(SchoolSetting::class)->withTrashed();
    }

    /**
     * Get the subject that owns the OfferSubject
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class)->withTrashed();
    }

    /**
     * Get all of the user that last updated the OfferSubject
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function updatedBy(): HasMany
    {
        return $this->hasMany(User::class, 'updated_by')->withTrashed();
    }
}
