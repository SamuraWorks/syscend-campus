<?php

namespace App\Models;

use App\Traits\{BelongsToSchool, HasAuditLog};
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Guardian extends Model
{
    use BelongsToSchool, HasAuditLog, SoftDeletes;

    protected $fillable = [
        'school_id', 'user_id', 'name', 'relation',
        'phone', 'email', 'occupation', 'address', 'photo',
    ];

    public function students(): HasMany
    {
        return $this->hasMany(Student::class);
    }
}
