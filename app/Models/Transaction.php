<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Transaction extends Model
{
    use SoftDeletes;

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

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function account()
    {
        return $this->belongsTo(Account::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function relatedAccount()
    {
        return $this->belongsTo(Account::class, 'related_account_id');
    }

    
    public function getTransactionTypes()
    {
        return [
            'income' => 'Pemasukan',
            'expense' => 'Pengeluaran',
            'transfer' => 'Transfer',
            'paylater_payment' => 'Pembayaran Paylater'
        ];
    }

    public function getTypeLabelAttribute()
    {
        $types = $this->getTransactionTypes();
        return $types[$this->type] ?? $this->type;
    }

    public function getFormattedAmountAttribute()
    {
        $prefix = '';
        if ($this->type === 'income') {
            $prefix = '+';
        } elseif ($this->type === 'expense') {
            $prefix = '-';
        }

        return $prefix . ' Rp ' . number_format($this->amount, 0, ',', '.');
    }

    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function scopeByDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('date', [$startDate, $endDate]);
    }

    public function scopeByAccount($query, $accountId)
    {
        return $query->where('account_id', $accountId);
    }

    public function scopeByCategory($query, $categoryId)
    {
        return $query->where('category_id', $categoryId);
    }

    public function scopeActive($query)
    {
        return $query->whereNull('deleted_at');
    }
}