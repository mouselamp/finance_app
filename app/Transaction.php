<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $fillable = [
        'user_id',
        'account_id',
        'category_id',
        'type',
        'date',
        'amount',
        'note',
        'related_account_id'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'date' => 'date'
    ];

    /**
     * Get the user that owns the transaction.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the account that owns the transaction.
     */
    public function account()
    {
        return $this->belongsTo(Account::class);
    }

    /**
     * Get the category for the transaction.
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Get the related account (for transfers).
     */
    public function relatedAccount()
    {
        return $this->belongsTo(Account::class, 'related_account_id');
    }

    /**
     * Get formatted amount attribute.
     */
    public function getFormattedAmountAttribute()
    {
        return number_format($this->amount, 2, ',', '.');
    }

    /**
     * Get transaction type label.
     */
    public function getTypeLabelAttribute()
    {
        $labels = [
            'income' => 'Pemasukan',
            'expense' => 'Pengeluaran',
            'transfer' => 'Transfer',
            'paylater_payment' => 'Pembayaran Paylater'
        ];

        return $labels[$this->type] ?? ucfirst($this->type);
    }

    /**
     * Scope a query to only include income transactions.
     */
    public function scopeIncome($query)
    {
        return $query->where('type', 'income');
    }

    /**
     * Scope a query to only include expense transactions.
     */
    public function scopeExpense($query)
    {
        return $query->where('type', 'expense');
    }

    /**
     * Scope a query to only include transfers.
     */
    public function scopeTransfer($query)
    {
        return $query->where('type', 'transfer');
    }

    /**
     * Scope a query to only include paylater payments.
     */
    public function scopePaylaterPayment($query)
    {
        return $query->where('type', 'paylater_payment');
    }

    /**
     * Scope a query to include transactions in a date range.
     */
    public function scopeDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('date', [$startDate, $endDate]);
    }
}