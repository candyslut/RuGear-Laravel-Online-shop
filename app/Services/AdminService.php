<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Exception;
use GuzzleHttp\Psr7\Request;

use App\Models\Ticket;

class AdminService
{
    public function PaginateUsers(int $perPage = 15): LengthAwarePaginator
    {
        return User::withCount('tickets')->paginate($perPage);
    }

    /**
     * * @throws Exception
     */
    public function removeUser(User $user, int $currentAdminId): bool
    {
        if ($currentAdminId === $user->id) {
            throw new Exception('Вы не можете удалить самого себя!');
        }

        return $user->delete();
    }

    public function reply(Ticket $ticket, array $data) {
        return $ticket->update([
            'reply' => $data['reply'],
            'status' => $data['status']
        ]);
    }
}