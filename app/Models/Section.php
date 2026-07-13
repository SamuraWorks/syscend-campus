<?php

namespace App\Models;

use App\Traits\BelongsToSchool;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Section extends Model
{
    use BelongsToSchool, SoftDeletes;

    protected $fillable = ['school_id', 'class_id', 'name', 'capacity', 'form_master_id'];

    public function schoolClass(): BelongsTo
    {
        return $this->belongsTo(SchoolClass::class, 'class_id');
    }

    public function formMaster(): BelongsTo
    {
        return $this->belongsTo(Staff::class, 'form_master_id');
    }
}
