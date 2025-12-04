<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\User;
use App\Models\Post;

class NewComment extends Notification implements ShouldQueue // Thêm ShouldQueue để gửi mail không bị lag web
{
    use Queueable;

    protected $commenter;
    protected $post;

    public function __construct(User $commenter, Post $post)
    {
        $this->commenter = $commenter;
        $this->post = $post;
    }

    // LOGIC QUYẾT ĐỊNH KÊNH GỬI
    public function via(object $notifiable): array
    {
        // Mặc định luôn có thông báo trên web (quả chuông)
        $channels = ['database'];

        // Chỉ gửi email nếu user đó đã xác thực email
        if ($notifiable->hasVerifiedEmail()) {
            $channels[] = 'mail';
        }

        return $channels;
    }

    // CẤU HÌNH NỘI DUNG EMAIL
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('💬 Bình luận mới từ ' . $this->commenter->name)
            ->greeting('Xin chào ' . $notifiable->name . '!')
            ->line($this->commenter->name . ' vừa bình luận vào bài viết của bạn: "' . $this->post->title . '"')
            ->action('Xem bình luận', route('posts.show', $this->post->slug))
            ->line('Cảm ơn bạn đã sử dụng Nexus!');
    }

    // CẤU HÌNH NỘI DUNG TRÊN WEB (DATABASE)
    public function toArray(object $notifiable): array
    {
        return [
            'commenter_id' => $this->commenter->id,
            'commenter_name' => $this->commenter->name,
            'commenter_avatar' => $this->commenter->avatar,
            'post_id' => $this->post->id,
            'post_title' => $this->post->title,
            'post_slug' => $this->post->slug,
            'message' => $this->commenter->name . ' đã bình luận về bài viết của bạn.',
            'link' => route('posts.show', $this->post->slug)
        ];
    }
}