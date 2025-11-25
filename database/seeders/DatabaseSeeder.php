<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Tạo 1 User Admin để bạn đăng nhập
        $admin = \App\Models\User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@nexus.com',
            'password' => bcrypt('password'), // Pass là 'password'
            'role' => 'admin'
        ]);

        // Tạo 3 Cộng đồng
        $c1 = \App\Models\Community::create(['name' => 'Lập Trình Viên', 'slug' => 'lap-trinh-vien', 'description' => 'Nơi chém gió code dạo']);
        $c2 = \App\Models\Community::create(['name' => 'Review Công Nghệ', 'slug' => 'review-cong-nghe', 'description' => 'Đánh giá đồ chơi số']);
    }

}
