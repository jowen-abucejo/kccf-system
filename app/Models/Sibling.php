<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Sibling extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'id',
        'registration_id',
        'last_name',
        'first_name',
        'middle_name',
        'name_suffix',
        'birth_date'
    ];

    public $timestamps = false;

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'birth_date' => 'date',
    ];

    /**
     * All of the relationships to be touched.
     *
     * @var array
     */
    protected $touches = ['registration'];

    /**
     * Get the student registration that owns the Sibling
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function registration(): BelongsTo
    {
        return $this->belongsTo(StudentRegistration::class, 'registration_id');
    }
}
