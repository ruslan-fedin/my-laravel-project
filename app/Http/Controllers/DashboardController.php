<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $viewDate = $request->get('view_date', now()->format('Y-m-d'));
        $viewDate = Carbon::parse($viewDate);
        $isHistory = $viewDate->lt(now()->startOfDay());

        return view('dashboard', [
            'viewDate' => $viewDate,
            'isHistory' => $isHistory,
            'stats' => $this->getStats(),
            'birthdaySaints' => $this->getBirthdaySaints(),
            'anniversaries' => $this->getAnniversaries(),
        ]);
    }

    private function getStats(): array
    {
        return [
            'active_count' => Employee::whereNull('deleted_at')->where('is_active', true)->count(),
            'new_this_month' => Employee::whereNull('deleted_at')
                ->whereBetween('hire_date', [now()->startOfMonth(), now()->endOfMonth()])
                ->count(),
            'total_capacity' => 100,
            'archive_count' => Employee::onlyTrashed()->count(),
        ];
    }

    private function getBirthdaySaints()
    {
        return Employee::whereNotNull('birth_date')
            ->where('birth_date', '!=', '0000-00-00')
            ->whereNull('deleted_at')
            ->get()
            ->map(function ($emp) {
                $birthDate = Carbon::parse($emp->birth_date);
                $nextBirthday = $birthDate->year(now()->year);
                if ($nextBirthday->lt(now())) $nextBirthday->addYear();
                $emp->days_until = (int) $nextBirthday->diffInDays(now());
                return $emp;
            })
            ->sortBy('days_until')
            ->take(10);
    }

    private function getAnniversaries()
    {
        return Employee::whereNotNull('hire_date')
            ->whereNull('deleted_at')
            ->get()
            ->filter(function ($emp) {
                $years = now()->diffInYears(Carbon::parse($emp->hire_date));
                return $years >= 5 && $years % 5 === 0;
            })
            ->take(10);
    }
}
