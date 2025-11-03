<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Account extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'type',
        'balance',
        'note'
    ];

    protected $casts = [
        'balance' => 'decimal:2'
    ];

    /**
     * Get the user that owns the account.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the transactions for the account.
     */
    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    /**
     * Get the paylater transactions for the account.
     */
    public function paylaterTransactions()
    {
        return $this->hasMany(PaylaterTransaction::class);
    }

    /**
     * Get related transactions (as destination in transfers).
     */
    public function relatedTransactions()
    {
        return $this->hasMany(Transaction::class, 'related_account_id');
    }

    /**
     * Get formatted balance attribute.
     */
    public function getFormattedBalanceAttribute()
    {
        return number_format($this->balance, 2, ',', '.');
    }

    /**
     * Get account type label.
     */
    public function getTypeLabelAttribute()
    {
        $labels = [
            'cash' => 'Cash',
            'bank' => 'Bank',
            'paylater' => 'Paylater'
        ];

        return $labels[$this->type] ?? ucfirst($this->type);
    }
}