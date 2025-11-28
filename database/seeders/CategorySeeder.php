<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(User $user = null): void
    {
        $categories = [
            ['name' => 'Iuran', 'type' => 'expense', 'icon' => 'fa-file-invoice-dollar', 'color' => '#ef4444'],
            ['name' => 'Belanja', 'type' => 'expense', 'icon' => 'fa-shopping-cart', 'color' => '#f97316'],
            ['name' => 'Pulsa', 'type' => 'expense', 'icon' => 'fa-mobile-alt', 'color' => '#eab308'],
            ['name' => 'Pribadi', 'type' => 'expense', 'icon' => 'fa-user', 'color' => '#84cc16'],
            ['name' => 'Transport', 'type' => 'expense', 'icon' => 'fa-bus', 'color' => '#10b981'],
            ['name' => 'Hutang/Tagihan', 'type' => 'expense', 'icon' => 'fa-credit-card', 'color' => '#06b6d4'],
            ['name' => 'Makanan/Jajan', 'type' => 'expense', 'icon' => 'fa-utensils', 'color' => '#3b82f6'],
            ['name' => 'Donasi', 'type' => 'expense', 'icon' => 'fa-hand-holding-heart', 'color' => '#6366f1'],
            ['name' => 'Lain-lain', 'type' => 'expense', 'icon' => 'fa-ellipsis-h', 'color' => '#8b5cf6'],
            ['name' => 'Anak', 'type' => 'expense', 'icon' => 'fa-child', 'color' => '#d946ef'],
            ['name' => 'Piutang', 'type' => 'income', 'icon' => 'fa-hand-holding-usd', 'color' => '#f43f5e'],
            ['name' => 'Maint Kendaraan', 'type' => 'expense', 'icon' => 'fa-wrench', 'color' => '#64748b'],
            ['name' => 'Tabungan', 'type' => 'expense', 'icon' => 'fa-piggy-bank', 'color' => '#14b8a6'],
            ['name' => 'Perabotan', 'type' => 'expense', 'icon' => 'fa-couch', 'color' => '#a855f7'],
            ['name' => 'Peliharaan', 'type' => 'expense', 'icon' => 'fa-paw', 'color' => '#f59e0b'],
            ['name' => 'Pemasukan', 'type' => 'income', 'icon' => 'fa-paw', 'color' => '#f59e0b'],
        ];

        // If no specific user is provided, fetch all users to seed for existing users
        // Or just for the first user/admin if running manually
        if ($user) {
            $users = [$user];
        } else {
            $users = User::all(); // This returns a Collection
        }

        // If users is an array (from single user fallback), wrap it or check manually
        // But User::all() returns Collection, [$user] is array.
        // Let's normalize to iterable.

        if (empty($users) && $users instanceof \Illuminate\Support\Collection && $users->isEmpty()) {
             return;
        }

        foreach ($users as $currentUser) {
            if (!$currentUser || !$currentUser->id) continue; // Safety check

            foreach ($categories as $category) {
                // Check if category already exists for this user to prevent duplicates
                $exists = Category::where('user_id', $currentUser->id)
                    ->where('name', $category['name'])
                    ->exists();

                if (!$exists) {
                    Category::create([
                        'user_id' => $currentUser->id,
                        'name' => $category['name'],
                        'type' => $category['type'],
                        'icon' => $category['icon'],
                        'color' => $category['color'],
                    ]);
                }
            }
        }
    }
}
