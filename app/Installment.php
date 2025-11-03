<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Installment extends Model
{
    protected $fillable = [
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

    /**
     * Get the paylater transaction that owns the installment.
     */
    public function paylaterTransaction()
    {
        return $this->belongsTo(PaylaterTransaction::class);
    }

    /**
     * Get formatted amount attribute.
     */
    public function getFormattedAmountAttribute()
    {
        return number_format($this->amount, 2, ',', '.');
    }

    /**
     * Get status label.
     */
    public function getStatusLabelAttribute()
    {
        $labels = [
            'unpaid' => 'Belum Dibayar',
            'paid' => 'Sudah Dibayar'
        ];

        return $labels[$this->status] ?? ucfirst($this->status);
    }

    /**
     * Check if installment is overdue.
     */
    public function getIsOverdueAttribute()
    {
        return $this->status === 'unpaid' && $this->due_date < now()->startOfDay();
    }

    /**
     * Mark installment as paid.
     */
    public function markAsPaid()
    {
        $this->update([
            'status' => 'paid',
            'paid_at' => now()
        ]);
    }

    /**
     * Scope a query to only include unpaid installments.
     */
    public function scopeUnpaid($query)
    {
        return $query->where('status', 'unpaid');
    }

    /**
     * Scope a query to only include paid installments.
     */
    public function scopePaid($query)
    {
        return $query->where('status', 'paid');
    }

    /**
     * Scope a query to only include overdue installments.
     */
    public function scopeOverdue($query)
    {
        return $query->unpaid()->where('due_date', '<', now()->startOfDay());
    }
}