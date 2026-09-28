<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Loan extends Model
{
    protected $table = 'loan';
    protected $primaryKey = 'loan_id';
    public $timestamps = false;

    protected $fillable = [
        'item_code',
        'member_id',
        'loan_date',
        'due_date',
        'renewed',
        'loan_rules_id',
        'actual',
        'is_lent',
        'is_return',
        'return_date',
        'input_date',
        'last_update',
        'uid',
    ];

    protected $casts = [
        'loan_date' => 'date',
        'due_date' => 'date',
        'return_date' => 'date',
    ];

    public function member()
    {
        return $this->belongsTo(Member::class, 'member_id', 'member_id');
    }

    public function item()
    {
        return $this->belongsTo(Item::class, 'item_code', 'item_code');
    }

    public function scopeActive($query)
    {
        return $query->where('is_return', 0);
    }

    public function scopeOverdue($query)
    {
        return $query->where('is_return', 0)->where('due_date', '<', Carbon::today());
    }

    public function isOverdue(): bool
    {
        if ($this->is_return) {
            return false;
        }
        return Carbon::parse($this->due_date)->isPast();
    }

    public function overdueDays(): int
    {
        if (!$this->isOverdue()) {
            return 0;
        }
        return (int) Carbon::parse($this->due_date)->diffInDays(Carbon::today());
    }

    public function calculateFine(): int
    {
        $days = $this->overdueDays();
        if ($days <= 0) {
            return 0;
        }
        $finePerDay = $this->member?->memberType?->fine_each_day ?? 1000;
        return $days * $finePerDay;
    }
}
