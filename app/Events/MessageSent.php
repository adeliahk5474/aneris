<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

use App\Models\Chat;

class MessageSent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Chat $chat;

    public function __construct(Chat $chat)
    {
        $this->chat = $chat;
    }

    public function broadcastOn(): array
    {
        if ($this->chat->order_id) {
            return [new PrivateChannel('order.' . $this->chat->order_id)];
        }

        $ids = [$this->chat->sender_id, $this->chat->receiver_id];
        sort($ids);
        return [new PrivateChannel('dm.' . implode('.', $ids))];
    }

    public function broadcastWith(): array
    {
        return [
            'chat_id'    => $this->chat->chat_id,
            'order_id'   => $this->chat->order_id,
            'sender_id'  => $this->chat->sender_id,
            'message'    => $this->chat->message,
            'image'      => $this->chat->image,
            'created_at' => $this->chat->created_at?->toISOString(),
            'sender' => [
                'user_id' => $this->chat->sender->user_id,
                'name'    => $this->chat->sender->name,
                'avatar'  => $this->chat->sender->avatar,
            ],
        ];
    }

    public function broadcastAs(): string
    {
        return 'MessageSent';
    }
}
