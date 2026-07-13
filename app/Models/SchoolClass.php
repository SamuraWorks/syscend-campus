<?php

namespace App\Models;

use App\Traits\BelongsToSchool;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class SchoolClass extends Model
{
    use BelongsToSchool, SoftDeletes;

    protected $table = 'classes';

    protected $fillable = [
        'school_id', 'name', 'numeric_name', 'capacity', 'class_teacher_id',
        'school_level', 'level_order', 'department_id',
    ];

    public function sections(): HasMany
    {
        return $this->hasMany(Section::class, 'class_id');
    }

    public function subjects(): HasMany
    {
        return $this->hasMany(Subject::class, 'class_id');
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class, 'department_id');
    }

    public function levelLabel(): string
    {
        return match($this->school_level) {
            'early_childhood'   => 'Early Childhood Education',
            'primary'           => 'Primary Education',
            'junior_secondary'  => 'Junior Secondary School',
            'senior_secondary'  => 'Senior Secondary School',
            default             => '',
        };
    }
}
