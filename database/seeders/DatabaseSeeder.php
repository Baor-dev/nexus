<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Community;
use App\Models\Post;
use App\Models\Comment;
use App\Models\Vote;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. TẠO USER
        // Tạo Admin
        $admin = User::firstOrCreate(
            ['email' => 'admin@nexus.com'],
            [
                'name' => 'Admin User',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'avatar' => null,
                'email_verified_at' => now(),
            ]
        );

        // Tạo 5 User thường
        $users = [];
        for ($i = 1; $i <= 5; $i++) {
            $users[] = User::firstOrCreate(
                ['email' => "user$i@nexus.com"],
                [
                    'name' => "User $i",
                    'password' => Hash::make('password'),
                    'role' => 'user',
                    'avatar' => null,
                    'email_verified_at' => now(),
                ]
            );
        }
        
        // Gộp admin vào danh sách user để dùng chung cho việc post bài/comment
        $allUsers = collect($users)->push($admin);

        // 2. TẠO CỘNG ĐỒNG (COMMUNITIES)
        $communitiesData = [
            ['name' => 'Lập Trình Viên', 'desc' => 'Cộng đồng chia sẻ kiến thức code, fix bug và meme IT.'],
            ['name' => 'Review Công Nghệ', 'desc' => 'Đánh giá điện thoại, laptop, gear mới nhất.'],
            ['name' => 'Góc Gaming', 'desc' => 'Thảo luận về game PC, Console, Mobile và Esport.'],
            ['name' => 'Chuyện Trò Linh Tinh', 'desc' => 'Nơi chém gió về mọi thứ trong cuộc sống.'],
            ['name' => 'Đầu Tư Tài Chính', 'desc' => 'Kiến thức chứng khoán, crypto, bất động sản.'],
        ];

        $communities = [];
        foreach ($communitiesData as $data) {
            $communities[] = Community::firstOrCreate(
                ['slug' => Str::slug($data['name'])],
                [
                    'name' => $data['name'],
                    'description' => $data['desc'],
                ]
            );
        }

        // 3. TẠO BÀI VIẾT MẪU (POSTS)
        $sampleTitles = [
            'Làm sao để học Laravel hiệu quả trong năm 2024?',
            'Đánh giá iPhone 15 sau 1 tháng sử dụng: Có đáng tiền?',
            'Vừa build xong dàn PC 50 củ, anh em vào thẩm giúp',
            'Hôm nay trời mưa buồn quá, có ai muốn tâm sự không?',
            'Thị trường đỏ lửa, anh em còn thở không hay đi bụi rồi?',
            'Hỏi về lỗi CORS khi gọi API từ React sang Laravel',
            'Top 5 tựa game cốt truyện hay nhất mọi thời đại',
            'Chia sẻ lộ trình trở thành Fullstack Developer',
            'Góc setup bàn làm việc tại nhà (Work from home)',
            'Review quán bún chả ngon nức tiếng khu Cầu Giấy',
        ];

        foreach ($sampleTitles as $index => $title) {
            $randomUser = $allUsers->random();
            $randomCommunity = $communities[array_rand($communities)]; // Lấy ngẫu nhiên 1 cộng đồng

            // Tạo bài viết
            $post = Post::create([
                'title' => $title,
                'slug' => Str::slug($title) . '-' . time() . '-' . $index,
                'description' => "Đây là mô tả ngắn cho bài viết về chủ đề $title. Bài viết này rất thú vị và hữu ích cho mọi người.",
                'content' => "<p>Xin chào mọi người,</p><p>Đây là nội dung chi tiết của bài viết <strong>$title</strong>.</p><p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.</p><ul><li>Điểm nhấn 1</li><li>Điểm nhấn 2</li><li>Điểm nhấn 3</li></ul><p>Mong mọi người góp ý nhiệt tình!</p>",
                'user_id' => $randomUser->id,
                'community_id' => $randomCommunity->id,
                'status' => 'published',
                'views' => rand(50, 5000), // Random lượt xem
                'thumbnail' => null, // Để null hoặc đường dẫn ảnh mẫu nếu có
                'created_at' => now()->subHours(rand(1, 100)), // Random thời gian đăng (trong vòng 100 giờ trước)
            ]);

            // 4. TẠO COMMENT MẪU
            $numComments = rand(2, 8);
            for ($j = 0; $j < $numComments; $j++) {
                Comment::create([
                    'content' => 'Bài viết rất hay! Cảm ơn bạn đã chia sẻ. Điểm ' . rand(1, 10) . '/10.',
                    'user_id' => $allUsers->random()->id,
                    'post_id' => $post->id,
                    'created_at' => now()->subMinutes(rand(1, 60)),
                ]);
            }

            // 5. TẠO VOTE MẪU
            foreach ($allUsers as $u) {
                // 70% cơ hội là user sẽ vote
                if (rand(1, 10) <= 7) { 
                    Vote::create([
                        'user_id' => $u->id,
                        'votable_id' => $post->id,
                        'votable_type' => Post::class,
                        'value' => rand(1, 10) > 2 ? 1 : -1, // 80% là Upvote (1), 20% là Downvote (-1)
                        'created_at' => now(),
                    ]);
                }
            }
        }
    }
}