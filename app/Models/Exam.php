<?php

namespace App\Models;

use App\Traits\{BelongsToSchool, HasAuditLog};
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Exam extends Model
{
    use BelongsToSchool, HasAuditLog, SoftDeletes;

    protected $fillable = [
        'school_id', 'class_id', 'name', 'type', 'start_date', 'end_date', 'status', 'description',
        'academic_year_id', 'term_id', 'assessment_category', 'ca_weight', 'exam_weight',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date'   => 'date',
        'ca_weight'  => 'decimal:2',
        'exam_weight' => 'decimal:2',
    ];

    public function schoolClass(): BelongsTo
    {
        return $this->belongsTo(SchoolClass::class, 'class_id');
    }

    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function term(): BelongsTo
    {
        return $this->belongsTo(AcademicTerm::class, 'term_id');
    }

    public function marks(): HasMany
    {
        return $this->hasMany(Mark::class);
    }
}
