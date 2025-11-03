<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class Installment extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'paylater_transaction_id',
        'due_date',
        'amount',
        'status',
        'paid_at'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'due_date' => 'date',
        'paid_at' => 'date'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function paylaterTransaction()
    {
        return $this->belongsTo(PaylaterTransaction::class);
    }

    public function getStatuses()
    {
        return [
            'unpaid' => 'Belum Dibayar',
            'paid' => 'Sudah Dibayar'
        ];
    }

    public function getStatusLabelAttribute()
    {
        $statuses = $this->getStatuses();
        return $statuses[$this->status] ?? $this->status;
    }

    public function getIsOverdueAttribute()
    {
        return $this->status === 'unpaid' &&
               $this->due_date &&
               $this->due_date->isPast();
    }

    public function getDaysUntilDueAttribute()
    {
        if (!$this->due_date) return null;

        return Carbon::today()->diffInDays($this->due_date, false);
    }

    public function markAsPaid()
    {
        $this->status = 'paid';
        $this->paid_at = Carbon::now();
        $this->save();
    }

    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeUnpaid($query)
    {
        return $query->where('status', 'unpaid');
    }

    public function scopePaid($query)
    {
        return $query->where('status', 'paid');
    }

    public function scopeUpcoming($query, $days = 30)
    {
        return $query->where('due_date', '>=', Carbon::today())
                    ->where('due_date', '<=', Carbon::today()->addDays($days))
                    ->where('status', 'unpaid');
    }

    public function scopeOverdue($query)
    {
        return $query->where('due_date', '<', Carbon::today())
                    ->where('status', 'unpaid');
    }
}