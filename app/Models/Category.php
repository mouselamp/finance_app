<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Category extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'name',
        'type'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public function getCategoryTypes()
    {
        return [
            'income' => 'Pemasukan',
            'expense' => 'Pengeluaran'
        ];
    }

    public function getTypeLabelAttribute()
    {
        $types = $this->getCategoryTypes();
        return $types[$this->type] ?? $this->type;
    }

    public function getDefaultIncomeCategories()
    {
        return [
            ['name' => 'Gaji', 'icon' => 'briefcase', 'color' => '#10B981'],
            ['name' => 'Bonus', 'icon' => 'gift', 'color' => '#10B981'],
            ['name' => 'Penjualan Barang', 'icon' => 'shopping-cart', 'color' => '#10B981'],
            ['name' => 'Hadiah', 'icon' => 'heart', 'color' => '#10B981'],
            ['name' => 'Investasi', 'icon' => 'chart-line', 'color' => '#10B981'],
            ['name' => 'Lainnya', 'icon' => 'plus-circle', 'color' => '#10B981']
        ];
    }

    public function getDefaultExpenseCategories()
    {
        return [
            ['name' => 'Makanan & Minuman', 'icon' => 'utensils', 'color' => '#EF4444'],
            ['name' => 'Transportasi', 'icon' => 'car', 'color' => '#EF4444'],
            ['name' => 'Tagihan', 'icon' => 'file-invoice', 'color' => '#EF4444'],
            ['name' => 'Belanja', 'icon' => 'shopping-bag', 'color' => '#EF4444'],
            ['name' => 'Hiburan', 'icon' => 'gamepad', 'color' => '#EF4444'],
            ['name' => 'Kesehatan', 'icon' => 'heartbeat', 'color' => '#EF4444'],
            ['name' => 'Pendidikan', 'icon' => 'graduation-cap', 'color' => '#EF4444'],
            ['name' => 'Donasi', 'icon' => 'hand-holding-heart', 'color' => '#EF4444'],
            ['name' => 'Lainnya', 'icon' => 'minus-circle', 'color' => '#EF4444']
        ];
    }

    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function scopeActive($query)
    {
        return $query->whereNull('deleted_at');
    }
}