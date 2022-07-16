<?php

namespace App\Models;

use App\Traits\UserStamps;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EnrolledSubject extends Model
{
    use HasFactory, UserStamps;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'enrollment_history_id',
        'offer_subject_id',
        'status',
        'created_by',
        'updated_by',
    ];

    /**
     * Get the user that created the EnrolledSubject
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by')->withTrashed();
    }

    /**
     * Get the enrollment history that owns the EnrolledSubject
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function enrollmentHistory(): BelongsTo
    {
        return $this->belongsTo(EnrollmentHistory::class)->withTrashed();
    }

    /**
     * Get the offer subject that owns the EnrolledSubject
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function offerSubject(): BelongsTo
    {
        return $this->belongsTo(OfferSubject::class);
    }

    /**
     * Get the user that last updated the EnrolledSubject
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by')->withTrashed();
    }
}
