<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StudentType extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'type'
    ];

    public $timestamps = false;

    /**
     * Get all of the enrollmentHistory for the StudentType
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function enrollmentHistory(): HasMany
    {
        return $this->hasMany(EnrollmentHistory::class)->withTrashed();
    }

    /**
     * Get all of the student registrations for the StudentType
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function studentRegistrations(): HasMany
    {
        return $this->hasMany(StudentRegistration::class);
    }

    /**
     * Get all of the students for the StudentType
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function students(): HasMany
    {
        return $this->hasMany(Student::class)->withTrashed();
    }
}
