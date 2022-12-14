<?php

namespace App\Models;

use App\Traits\StudentNumber;
use App\Traits\UserStamps;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Student extends Model
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes, StudentNumber, UserStamps;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'student_number',
        'user_id',
        'school_setting_id',
        'program_id',
        'level_id',
        'regular',
        'student_type_id',
        'admission_datetime',
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
        'admission_datetime' => 'datetime',
    ];

    /**
     * Get the user that created the record of Student
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by')->withTrashed();
    }

    /**
     * Get the user that soft deleted the record of Student
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function deletedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'deleted_by')->withTrashed();
    }

    /**
     * Get all of the enrollmentHistories for the Student
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function enrollmentHistories(): HasMany
    {
        return $this->hasMany(EnrollmentHistory::class)->withTrashed();
    }

    /**
     * Get all of the grades for the Student
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function grades(): HasMany
    {
        return $this->hasMany(Grade::class)->withTrashed();
    }

    /**
     * Get the level that owns the Student
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function level(): BelongsTo
    {
        return $this->belongsTo(Level::class)->withTrashed();
    }

    /**
     * Get the program that owns the Student
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function program(): BelongsTo
    {
        return $this->belongsTo(Program::class)->withTrashed();
    }


    /**
     * Get the student registration associated with the Student
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function registration(): HasOne
    {
        return $this->hasOne(StudentRegistration::class, 'student_id');
    }

    /**
     * Get the schoolSetting that owns the Student
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function schoolSetting(): BelongsTo
    {
        return $this->belongsTo(SchoolSetting::class)->withTrashed();
    }

    /**
     * Get the student_type that owns the Student
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function student_type(): BelongsTo
    {
        return $this->belongsTo(StudentType::class);
    }

    /**
     * Get the last user that updated the record of Student
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by')->withTrashed();
    }

    /**
     * Get the user that owns the Student
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class)->withTrashed();
    }
}
