<?php

namespace Database\Seeders;

use App\Models\Food;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class FoodSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $foodItems = [
            // Appetizers
            ['name' => 'Caesar Salad', 'description' => 'Fresh romaine lettuce with caesar dressing, croutons, and parmesan cheese', 'price' => 12.99, 'category' => 'appetizer', 'available' => true, 'preparation_time' => 10],
            ['name' => 'Garlic Bread', 'description' => 'Toasted bread with garlic butter and herbs', 'price' => 6.99, 'category' => 'appetizer', 'available' => true, 'preparation_time' => 8],
            ['name' => 'Soup of the Day', 'description' => 'Chef\'s special homemade soup', 'price' => 8.99, 'category' => 'appetizer', 'available' => true, 'preparation_time' => 15],
            
            // Main Courses
            ['name' => 'Grilled Chicken Breast', 'description' => 'Tender grilled chicken with seasonal vegetables and rice pilaf', 'price' => 24.99, 'category' => 'main', 'available' => true, 'preparation_time' => 25],
            ['name' => 'Beef Steak', 'description' => 'Premium beef steak cooked to your preference with mashed potatoes', 'price' => 34.99, 'category' => 'main', 'available' => true, 'preparation_time' => 30],
            ['name' => 'Grilled Salmon', 'description' => 'Fresh Atlantic salmon with lemon butter sauce and asparagus', 'price' => 28.99, 'category' => 'main', 'available' => true, 'preparation_time' => 20],
            ['name' => 'Vegetarian Pasta', 'description' => 'Penne pasta with roasted vegetables in a creamy tomato sauce', 'price' => 18.99, 'category' => 'main', 'available' => true, 'preparation_time' => 20],
            ['name' => 'Margherita Pizza', 'description' => 'Classic pizza with fresh mozzarella, tomatoes, and basil', 'price' => 16.99, 'category' => 'main', 'available' => true, 'preparation_time' => 15],
            
            // Desserts
            ['name' => 'Chocolate Cake', 'description' => 'Rich chocolate cake with chocolate ganache', 'price' => 8.99, 'category' => 'dessert', 'available' => true, 'preparation_time' => 5],
            ['name' => 'Ice Cream Sundae', 'description' => 'Vanilla ice cream with chocolate sauce, nuts, and whipped cream', 'price' => 7.99, 'category' => 'dessert', 'available' => true, 'preparation_time' => 5],
            ['name' => 'Fruit Tart', 'description' => 'Fresh seasonal fruits on a pastry cream base', 'price' => 9.99, 'category' => 'dessert', 'available' => true, 'preparation_time' => 5],
            
            // Beverages
            ['name' => 'Fresh Orange Juice', 'description' => 'Freshly squeezed orange juice', 'price' => 4.99, 'category' => 'beverage', 'available' => true, 'preparation_time' => 3],
            ['name' => 'Coffee', 'description' => 'Freshly brewed premium coffee', 'price' => 3.99, 'category' => 'beverage', 'available' => true, 'preparation_time' => 5],
            ['name' => 'Soft Drink', 'description' => 'Selection of Coca-Cola products', 'price' => 2.99, 'category' => 'beverage', 'available' => true, 'preparation_time' => 2],
            ['name' => 'Mineral Water', 'description' => 'Premium bottled mineral water', 'price' => 2.49, 'category' => 'beverage', 'available' => true, 'preparation_time' => 1],
        ];

        foreach ($foodItems as $item) {
            Food::create($item);
        }
    }
}
