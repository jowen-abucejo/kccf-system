<?php

namespace App\Models;

use App\Traits\UserStamps;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Term extends Model
{
    use HasFactory, UserStamps, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'code',
        'deleted_by'
    ];

    public $timestamps =  false;

    /**
     * Get the user that soft deleted the Term
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function deletedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'deleted_by')->withTrashed();
    }

    /**
     * Get all of the program subjects for the Term
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function programSubjects(): HasMany
    {
        return $this->hasMany(ProgramSubject::class)->withTrashed();
    }

    /**
     * Get all of the schoolSettings for the Term
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function schoolSettings(): HasMany
    {
        return $this->hasMany(SchoolSetting::class)->withTrashed();
    }
}
