<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\AdminService;
use Exception;

class AdminController extends Controller
{
    protected $adminService;

    public function __construct(AdminService $adminService)
    {
        $this->adminService = $adminService;
    }

    public function index() 
    {
        // Запрашиваем данные у сервиса
        $users = $this->adminService->PaginateUsers(15);
        
        return view('admin.users', compact('users'));
    }

    public function destroy(User $user)
    {
        try {
            $this->adminService->removeUser($user, auth()->id());
            
            return redirect()->back()->with('success', 'Пользователь успешно удален из системы RuGear.');
        } catch (Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
}