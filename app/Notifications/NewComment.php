<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\User;
use App\Models\Post;

class NewComment extends Notification
{
    use Queueable;

    protected $commenter;
    protected $post;

    // Nhận vào người bình luận và bài viết
    public function __construct(User $commenter, Post $post)
    {
        $this->commenter = $commenter;
        $this->post = $post;
    }

    // Chỉ lưu vào Database (không gửi mail để đỡ spam lúc test)
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    // Cấu trúc dữ liệu sẽ lưu vào DB
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