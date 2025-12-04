<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\User;
use App\Models\Post;

class NewReply extends Notification implements ShouldQueue
{
    use Queueable;

    protected $commenter;
    protected $post;

    public function __construct(User $commenter, Post $post)
    {
        $this->commenter = $commenter;
        $this->post = $post;
    }

    public function via(object $notifiable): array
    {
        $channels = ['database'];
        
        // Gửi email nếu user đã xác thực
        if ($notifiable->hasVerifiedEmail()) {
            $channels[] = 'mail';
        }

        return $channels;
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('💬 Phản hồi mới từ ' . $this->commenter->name)
            ->greeting('Xin chào ' . $notifiable->name . '!')
            ->line($this->commenter->name . ' đã trả lời bình luận của bạn trong bài viết "' . $this->post->title . '"')
            ->action('Xem phản hồi', route('posts.show', $this->post->slug))
            ->line('Cảm ơn bạn đã tham gia thảo luận tại Nexus!');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'commenter_id' => $this->commenter->id,
            'commenter_name' => $this->commenter->name,
            'commenter_avatar' => $this->commenter->avatar,
            'post_id' => $this->post->id,
            'post_title' => $this->post->title,
            'post_slug' => $this->post->slug,
            'message' => $this->commenter->name . ' đã trả lời bình luận của bạn.',
            'link' => route('posts.show', $this->post->slug)
        ];
    }
}