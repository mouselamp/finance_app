<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PaylaterTransaction extends Model
{
    use SoftDeletes;

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
        'date' => 'date',
        'tenor' => 'integer'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function account()
    {
        return $this->belongsTo(Account::class);
    }

    public function installments()
    {
        return $this->hasMany(Installment::class);
    }

    public function getPaymentTypes()
    {
        return [
            'full' => 'Bayar Penuh',
            'installment' => 'Cicilan'
        ];
    }

    public function getPaymentTypeLabelAttribute()
    {
        $types = $this->getPaymentTypes();
        return $types[$this->payment_type] ?? $this->payment_type;
    }

    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeInstallment($query)
    {
        return $query->where('payment_type', 'installment');
    }

    public function scopeFullPayment($query)
    {
        return $query->where('payment_type', 'full');
    }
}