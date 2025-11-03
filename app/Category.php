<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'type'
    ];

    /**
     * Get the user that owns the category.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the transactions for the category.
     */
    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    /**
     * Get category type label.
     */
    public function getTypeLabelAttribute()
    {
        $labels = [
            'income' => 'Pemasukan',
            'expense' => 'Pengeluaran'
        ];

        return $labels[$this->type] ?? ucfirst($this->type);
    }

    /**
     * Scope a query to only include income categories.
     */
    public function scopeIncome($query)
    {
        return $query->where('type', 'income');
    }

    /**
     * Scope a query to only include expense categories.
     */
    public function scopeExpense($query)
    {
        return $query->where('type', 'expense');
    }
}