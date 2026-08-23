<?php

namespace App\Models;

use App\Traits\{BelongsToSchool, HasAuditLog};
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\{BelongsTo, BelongsToMany, HasMany};
use Illuminate\Database\Eloquent\SoftDeletes;

class Guardian extends Model
{
    use BelongsToSchool, HasAuditLog, SoftDeletes;

    protected $fillable = [
        'school_id', 'user_id', 'name', 'relation',
        'phone', 'email', 'occupation', 'address', 'photo',
        'claimed_by', 'claimed_at', 'registration_status',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function students(): HasMany
    {
        return $this->hasMany(Student::class);
    }

    public function children(): BelongsToMany
    {
        return $this->belongsToMany(Student::class, 'guardian_student')
            ->withPivot('relationship', 'is_primary')
            ->withTimestamps();
    }
}
