<?php

namespace App\Models;

use App\Traits\BelongsToSchool;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SchoolSubscription extends Model
{
    use BelongsToSchool, SoftDeletes;

    protected $fillable = [
        'school_id', 'package_id', 'coupon_id', 'academic_year_id',
        'start_date', 'end_date', 'term_number',
        'status', 'is_trial', 'trial_ends_at',
        'price_per_term', 'amount_paid', 'payment_method', 'notes',
    ];

    protected $casts = [
        'start_date'    => 'date',
        'end_date'      => 'date',
        'trial_ends_at' => 'date',
        'is_trial'      => 'boolean',
        'price_per_term' => 'decimal:2',
        'amount_paid'   => 'decimal:2',
    ];

    public function package(): BelongsTo  { return $this->belongsTo(Package::class); }
    public function coupon(): BelongsTo   { return $this->belongsTo(Coupon::class); }
    public function academicYear(): BelongsTo { return $this->belongsTo(AcademicYear::class); }
    public function school(): BelongsTo   { return $this->belongsTo(School::class); }

    public function payments(): HasMany
    {
        return $this->hasMany(SubscriptionPayment::class);
    }

    public function confirmedPayments(): HasMany
    {
        return $this->hasMany(SubscriptionPayment::class)->where('status', 'confirmed');
    }

    public function getBalanceAttribute(): float
    {
        $price = (float) ($this->price_per_term ?? 0);
        $paid  = (float) $this->confirmedPayments()->sum('amount');
        return max(0, $price - $paid);
    }

    public function getIsFullyPaidAttribute(): bool
    {
        return $this->balance <= 0;
    }
}
