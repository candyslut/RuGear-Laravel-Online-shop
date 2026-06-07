<?php

namespace App\Services;

use App\Models\Achievement;
use App\Models\Commentary;
use App\Models\Notification;
use App\Models\Order;
use App\Models\Ticket;
use App\Models\User;

class NotificationService
{
    /**
     * Create a notification for a user.
     */
    public function push(User $user, string $type, string $title, ?string $message = null, ?string $url = null): Notification
    {
        return $user->notifications()->create([
            'type'    => $type,
            'title'   => $title,
            'message' => $message,
            'url'     => $url,
        ]);
    }

    public function achievement(User $user, Achievement $achievement): Notification
    {
        return $this->push(
            $user,
            'achievement',
            'Достижение разблокировано',
            $achievement->title,
            route('dashboard'),
        );
    }

    public function levelUp(User $user, int $level): Notification
    {
        return $this->push(
            $user,
            'level_up',
            'Новый уровень!',
            "Вы достигли уровня {$level}",
            route('dashboard'),
        );
    }

    public function ticketStatus(Ticket $ticket): ?Notification
    {
        if (!$ticket->user) {
            return null;
        }

        $labels = [
            'pending' => 'В ожидании',
            'replied' => 'Получен ответ',
            'closed'  => 'Закрыта',
        ];
        $label = $labels[$ticket->status] ?? $ticket->status;

        return $this->push(
            $ticket->user,
            'ticket_status',
            'Статус заявки изменён',
            "«{$ticket->name}» — {$label}",
            route('support'),
        );
    }

    public function orderStatus(Order $order): ?Notification
    {
        if (!$order->user) {
            return null;
        }

        return $this->push(
            $order->user,
            'order_status',
            'Статус заказа изменён',
            "Заказ #{$order->id}: {$order->status_label}",
            route('dashboard'),
        );
    }

    public function commentReply(Commentary $reply): ?Notification
    {
        $parent = $reply->parent;
        if (!$parent || !$parent->user) {
            return null;
        }

        // Don't notify yourself when you reply to your own review.
        if ($parent->user_id === $reply->user_id) {
            return null;
        }

        $replierName = $reply->user->name ?? 'Пользователь';

        return $this->push(
            $parent->user,
            'comment_reply',
            'Ответ на ваш отзыв',
            "{$replierName} ответил(а) на ваш отзыв",
            route('products.show', $parent->product_id),
        );
    }
}
