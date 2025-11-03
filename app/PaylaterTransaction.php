<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PaylaterTransaction extends Model
{
    protected $fillable = [
        'user_id',
        'account_id',
        'date',
        'total_amount',
        'payment_type',
        'tenor',
        'monthly_amount',
        'note'
    ];

    protected $casts = [
        'total_amount' => 'decimal:2',
        'monthly_amount' => 'decimal:2',
        'date' => 'date'
    ];

    /**
     * Get the user that owns the paylater transaction.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the account that owns the paylater transaction.
     */
    public function account()
    {
        return $this->belongsTo(Account::class);
    }

    /**
     * Get the installments for the paylater transaction.
     */
    public function installments()
    {
        return $this->hasMany(Installment::class);
    }

    /**
     * Get formatted total amount attribute.
     */
    public function getFormattedTotalAmountAttribute()
    {
        return number_format($this->total_amount, 2, ',', '.');
    }

    /**
     * Get formatted monthly amount attribute.
     */
    public function getFormattedMonthlyAmountAttribute()
    {
        return number_format($this->monthly_amount, 2, ',', '.');
    }

    /**
     * Get payment type label.
     */
    public function getPaymentTypeLabelAttribute()
    {
        $labels = [
            'full' => 'Bayar Penuh',
            'installment' => 'Cicilan'
        ];

        return $labels[$this->payment_type] ?? ucfirst($this->payment_type);
    }

    /**
     * Get paid installments count.
     */
    public function getPaidInstallmentsCountAttribute()
    {
        return $this->installments()->where('status', 'paid')->count();
    }

    /**
     * Get unpaid installments count.
     */
    public function getUnpaidInstallmentsCountAttribute()
    {
        return $this->installments()->where('status', 'unpaid')->count();
    }

    /**
     * Check if all installments are paid.
     */
    public function getIsFullyPaidAttribute()
    {
        return $this->installments()->where('status', 'unpaid')->count() === 0;
    }
}