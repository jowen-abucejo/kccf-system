<?php

namespace App\Models;

use App\Traits\UserStamps;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProgramLevel extends Model
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
        'deleted_by',
    ];

    public $timestamps = false;

    /**
     * Get all of the levels for the ProgramLevel
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function levels(): HasMany
    {
        return $this->hasMany(Level::class)->withTrashed();
    }

    /**
     * Get the user that soft deleted the ProgramLevel
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function deletedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'deleted_by')->withTrashed();
    }
}
