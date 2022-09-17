<?php

namespace App\Models;

use App\Traits\UserStamps;
use Illuminate\Database\Eloquent\Casts\AsArrayObject;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StudentRegistration extends Model
{
    use HasFactory, UserStamps;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'student_id',
        'school_setting_id',
        'program_id',
        'level_id',
        'student_type_id',
        'last_name',
        'first_name',
        'middle_name',
        'name_suffix',
        'sex',
        'civil_status',
        'birth_date',
        'birth_place',
        'address',
        'email',
        'contact_number',
        'religion',
        'other_info',
        'created_by',
        'updated_by',
        'created_at'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'birth_date' => 'date',
        'address' => AsArrayObject::class,
        'religion' => AsArrayObject::class,
        'other_info' => AsArrayObject::class,
    ];

    /**
     * Get the user that created the StudentRegistration
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by')->withTrashed();
    }

    /**
     * Get all of the education backgrounds for the StudentRegistration
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function educationBackgrounds(): HasMany
    {
        return $this->hasMany(EducationBackground::class, 'registration_id');
    }

    /**
     * Get all of the guardians for the StudentRegistration
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function guardians(): HasMany
    {
        return $this->hasMany(Guardian::class, 'registration_id');
    }

    /**
     * Get the level that owns the StudentRegistration
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function level(): BelongsTo
    {
        return $this->belongsTo(Level::class)->withTrashed();
    }

    /**
     * Get the program that owns the StudentRegistration
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function program(): BelongsTo
    {
        return $this->belongsTo(Program::class)->withTrashed();
    }

    /**
     * Get the school setting that owns the StudentRegistration
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function schoolSetting(): BelongsTo
    {
        return $this->belongsTo(SchoolSetting::class)->withTrashed();
    }

    /**
     * Get all of the siblings for the StudentRegistration
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function siblings(): HasMany
    {
        return $this->hasMany(Sibling::class, 'registration_id');
    }

    /**
     * Get the student that owns the StudentRegistration
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class, 'student_id')->withTrashed();
    }

    /**
     * Get the student_type that owns the StudentRegistration
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function student_type(): BelongsTo
    {
        return $this->belongsTo(StudentType::class);
    }

    /**
     * Get the last user that updated the StudentRegistration
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by')->withTrashed();
    }
}
