<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Position;
use App\Models\Status;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        $data = [
            'employees_count' => Employee::count(),
            'active_employees' => Employee::where('is_active', true)->count(),
            'positions_count' => Position::count(),
            'statuses_count' => Status::count(),
        ];

        return view('welcome', $data); // Используем стандартный файл welcome
    }
}
