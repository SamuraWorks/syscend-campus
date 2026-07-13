<?php

namespace App\Models;

use App\Traits\{BelongsToSchool, HasAuditLog};
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Attendance extends Model
{
    use BelongsToSchool, HasAuditLog;

    protected $fillable = [
        'school_id', 'academic_year_id', 'date',
        'attendable_type', 'attendable_id', 'status', 'remarks',
        'approved_at', 'approved_by',
    ];

    protected $casts = [
        'date' => 'date',
    ];

    public function attendable(): MorphTo
    {
        return $this->morphTo();
    }

    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class);
    }
}
