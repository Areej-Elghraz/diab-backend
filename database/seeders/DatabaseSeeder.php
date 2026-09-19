<?php

namespace Database\Seeders;

use App\Models\Banner;
use App\Models\User;
use App\Models\Category;
use App\Models\PhoneNumber;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\SocialLink;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name'              => 'Admin',
            'username'          => 'ayadiab123',
            'email'             => 'diabfurnitures@gmail.com',
            'email_verified_at' => now(),
            'password'          => Hash::make('ayadiab'), // default password
            'role'              => 'admin',
        ]);

        // Only run fake seeders if the environment is local
        if (app()->environment('local')) {
            Category::factory(10)->create();
            Banner::factory(7)->create();
            Product::factory(30)->create()->each(function ($product) {
                ProductImage::factory(3)->create([
                    'product_id' => $product->id,
                ]);
            });
            PhoneNumber::factory(15)->create();
            SocialLink::factory(5)->create();
        } else {
            $this->command->info('Production environment detected. Fake data was not seeded.');
        }
    }
}
