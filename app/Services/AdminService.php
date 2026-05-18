<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Exception;

class AdminService
{
    public function PaginateUsers(int $perPage = 15): LengthAwarePaginator
    {
        return User::withCount('tickets')->paginate($perPage);
    }

    /**
     * Удалить пользователя из системы с проверкой прав.
     * * @throws Exception
     */
    public function removeUser(User $user, int $currentAdminId): bool
    {
        if ($currentAdminId === $user->id) {
            throw new Exception('Вы не можете удалить самого себя!');
        }

        return $user->delete();
    }
}