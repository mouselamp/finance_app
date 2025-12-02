<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Account extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'name',
        'type',
        'balance',
        'note'
    ];

    protected $appends = [
        'type_label'
    ];

    protected $casts = [
        'balance' => 'decimal:2'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public function incomeTransactions()
    {
        return $this->hasMany(Transaction::class)->where('type', 'income');
    }

    public function expenseTransactions()
    {
        return $this->hasMany(Transaction::class)->where('type', 'expense');
    }

    public function transferFromTransactions()
    {
        return $this->hasMany(Transaction::class)->where('type', 'transfer');
    }

    public function transferToTransactions()
    {
        return $this->hasMany(Transaction::class, 'related_account_id');
    }

    public function getAccountTypes()
    {
        return [
            'cash' => 'Cash/Tunai',
            'bank' => 'Bank',
            'paylater' => 'Paylater'
        ];
    }

    public function getTypeLabelAttribute()
    {
        $types = $this->getAccountTypes();
        return $types[$this->type] ?? $this->type;
    }

    public function updateBalance($amount, $type = 'add')
    {
        if ($type === 'add') {
            $this->balance += $amount;
        } elseif ($type === 'subtract') {
            $this->balance -= $amount;
        } else {
            $this->balance = $amount;
        }

        $this->save();
    }

    public function scopeActive($query)
    {
        return $query->whereNull('deleted_at');
    }
}