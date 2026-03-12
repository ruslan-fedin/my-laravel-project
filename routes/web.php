<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\FlowerBedController;
use App\Http\Controllers\NotificationSettingsController;
use App\Http\Controllers\PositionController;
use App\Http\Controllers\SharedBoardController;
use App\Http\Controllers\StatusController;
use App\Http\Controllers\TelegramReportController;
use Illuminate\Support\Facades\Auth;  // ✅ Импортируем Auth

use App\Http\Controllers\TelegramReportLogController;
use App\Http\Controllers\TimesheetController;
use App\Http\Controllers\TravelTimesheetController;
use App\Http\Controllers\VacationController;
use Illuminate\Support\Facades\Route;

// ============================================================================
// 🔹 КОРНЕВОЙ МАРШРУТ
// ============================================================================
Route::get('/', function () {
    if (Auth::check()) {  // ✅ Используем фасад вместо auth()->check()
        return redirect()->route('dashboard');
    }
    return redirect()->route('login');
});

// ============================================================================
// 🔹 ПУБЛИЧНЫЕ МАРШРУТЫ (без авторизации)
// ============================================================================
Route::middleware(['guest'])->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/forgot-password', [AuthController::class, 'showForgotPassword'])->name('password.request');
    Route::post('/forgot-password', [AuthController::class, 'sendResetLink'])->name('password.email');
});

// Публичный доступ к табелю
Route::get('/public-tabel/{secret}', [SharedBoardController::class, 'showBoard'])->name('public.tabel');

// ============================================================================
// 🔹 ЗАЩИЩЕННЫЕ МАРШРУТЫ (требуют авторизацию)
// ============================================================================
Route::middleware(['auth'])->group(function () {

    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');



        // 🔥 AJAX поиск клумб
    Route::get('flower-beds/search', [FlowerBedController::class, 'search'])
        ->name('flower-beds.search');
    // -------------------------------------------------------------------------
    // 🔹 ОТПУСКА
    // -------------------------------------------------------------------------
    Route::prefix('vacations')->group(function () {
        Route::get('/', [VacationController::class, 'index'])->name('vacations.index');
        Route::get('/timeline', [VacationController::class, 'timeline'])->name('vacations.timeline');
        Route::get('/api', [VacationController::class, 'api'])->name('vacations.api');
        Route::get('/api/timeline', [VacationController::class, 'timelineApi'])->name('vacations.timeline.api');
        Route::post('/update', [VacationController::class, 'update'])->name('vacations.update');
        Route::get('/create', [VacationController::class, 'create'])->name('vacations.create');
        Route::post('/', [VacationController::class, 'store'])->name('vacations.store');
        Route::get('/{id}/edit', [VacationController::class, 'edit'])->name('vacations.edit');
        Route::put('/{id}', [VacationController::class, 'updateVacation'])->name('vacations.updateVacation');
        Route::delete('/{id}', [VacationController::class, 'destroy'])->name('vacations.destroy');
        Route::delete('/mass-delete', [VacationController::class, 'massDelete'])->name('vacations.mass-delete');
        Route::get('/export', [VacationController::class, 'export'])->name('vacations.export');
    });

    // -------------------------------------------------------------------------
    // 🔹 СПРАВОЧНИК КЛУМБ
    // -------------------------------------------------------------------------

    // 🔥 Сначала КАСТОМНЫЕ маршруты (ДО resource!)
    // AJAX маршруты для файлов
    Route::post('flower-beds/{flowerBed}/files/upload', [FlowerBedController::class, 'uploadFile'])
        ->name('flower-beds.files.upload');
    Route::get('flower-beds/files/{file}/download', [FlowerBedController::class, 'downloadFile'])
        ->name('flower-beds.files.download');
    Route::get('flower-beds/files/{file}/view', [FlowerBedController::class, 'viewFile'])
        ->name('flower-beds.files.view');
    Route::delete('flower-beds/files/{file}', [FlowerBedController::class, 'destroyFile'])
        ->name('flower-beds.files.destroy');

    // Маршруты для логов
    Route::get('flower-beds/logs/{log}/edit', [FlowerBedController::class, 'editFlowerBedLog'])
        ->name('flower-beds.logs.edit');
    Route::put('flower-beds/logs/{log}', [FlowerBedController::class, 'updateFlowerBedLog'])
        ->name('flower-beds.logs.update');
    Route::delete('flower-beds/logs/{log}', [FlowerBedController::class, 'deleteFlowerBedLog'])
        ->name('flower-beds.logs.delete');

    // 🔥 Потом resource (стандартные CRUD маршруты)
    Route::resource('flower-beds', FlowerBedController::class);

    // -------------------------------------------------------------------------
    // 🔹 TELEGRAM LOGS
    // -------------------------------------------------------------------------
    Route::prefix('telegram-logs')->group(function () {
        Route::get('/', [TelegramReportLogController::class, 'index'])->name('telegram-logs.index');
        Route::match(['post', 'delete'], '/bulk-delete', [TelegramReportLogController::class, 'bulkDestroy'])->name('telegram-logs.bulk-destroy');
        Route::post('/clear-all', [TelegramReportLogController::class, 'clearAll'])->name('telegram-logs.clear-all');
        Route::get('/{id}', [TelegramReportLogController::class, 'show'])->name('telegram-logs.show');
        Route::post('/{id}/resend', [TelegramReportLogController::class, 'resend'])->name('telegram-logs.resend');
        Route::delete('/{id}', [TelegramReportLogController::class, 'destroy'])->name('telegram-logs.destroy');
    });

    // -------------------------------------------------------------------------
    // 🔹 TELEGRAM ОТЧЕТЫ
    // -------------------------------------------------------------------------
    Route::post('/report/send-telegram', [TelegramReportController::class, 'sendCenterReport'])->name('report.send.telegram');
    Route::post('/telegram/send-public-link', [TelegramReportController::class, 'sendPublicLink'])->name('telegram.public-link');

    // -------------------------------------------------------------------------
    // 🔹 СОТРУДНИКИ
    // -------------------------------------------------------------------------
    Route::prefix('employees')->group(function () {
        Route::get('/import', [EmployeeController::class, 'showImportForm'])->name('employees.import.form');
        Route::post('/import', [EmployeeController::class, 'import'])->name('employees.import.store');
        Route::get('/archive', [EmployeeController::class, 'archive'])->name('employees.archive');
        Route::patch('/{id}/restore', [EmployeeController::class, 'restore'])->name('employees.restore');
        Route::delete('/{id}/force-delete', [EmployeeController::class, 'forceDelete'])->name('employees.forceDelete');
    });

    Route::resource('employees', EmployeeController::class)->where(['employee' => '[0-9]+']);
    Route::post('/employees/{id}/activate', [EmployeeController::class, 'activate'])->name('employees.activate');

    // -------------------------------------------------------------------------
    // 🔹 БРИГАДЫ
    // -------------------------------------------------------------------------
    Route::prefix('brigades')->group(function () {
        Route::get('/', [EmployeeController::class, 'showBrigades'])->name('brigades.index');
        Route::post('/vacation/start', [EmployeeController::class, 'startVacation'])->name('brigades.start-vacation');
        Route::post('/vacation/return', [EmployeeController::class, 'returnVacation'])->name('brigades.return-vacation');
        Route::post('/update-leader', [EmployeeController::class, 'updateLeader'])->name('brigades.update-leader');
        Route::post('/update-location', [EmployeeController::class, 'updateLocation'])->name('brigades.update-location');
    });

    // -------------------------------------------------------------------------
    // 🔹 СПРАВОЧНИКИ
    // -------------------------------------------------------------------------
    Route::resource('positions', PositionController::class);
    Route::resource('statuses', StatusController::class);

    // -------------------------------------------------------------------------
    // 🔹 СИСТЕМА УЧЁТА ВЫЕЗДОВ
    // -------------------------------------------------------------------------
    Route::prefix('travel-timesheets')->group(function () {
        Route::post('/{id}/update-status', [TravelTimesheetController::class, 'updateStatus'])->name('travel-timesheets.update-status');
        Route::post('/update-comment', [TravelTimesheetController::class, 'saveComment'])->name('travel-timesheets.save-comment');
        Route::post('/{id}/add-employee', [TravelTimesheetController::class, 'addEmployee'])->name('travel-timesheets.add-employee');
        Route::post('/{id}/add-all', [TravelTimesheetController::class, 'addAll'])->name('travel-timesheets.add-all');
        Route::delete('/{id}/remove-employee/{empId}', [TravelTimesheetController::class, 'removeEmployee'])->name('travel-timesheets.remove-employee');
        Route::post('/{id}/mass-remove', [TravelTimesheetController::class, 'massRemove'])->name('travel-timesheets.mass-remove');
        Route::get('/{id}/export', [TravelTimesheetController::class, 'export'])->name('travel-timesheets.export');
    });
    Route::resource('travel-timesheets', TravelTimesheetController::class);

    // -------------------------------------------------------------------------
    // 🔹 ОСНОВНОЙ ТАБЕЛЬ
    // -------------------------------------------------------------------------
    Route::prefix('timesheets')->group(function () {
        Route::get('/', [TimesheetController::class, 'index'])->name('timesheets.index');
        Route::get('/create', [TimesheetController::class, 'create'])->name('timesheets.create');
        Route::post('/', [TimesheetController::class, 'store'])->name('timesheets.store');
        Route::get('/{timesheet}', [TimesheetController::class, 'show'])->name('timesheets.show');
        Route::get('/{timesheet}/edit', [TimesheetController::class, 'edit'])->name('timesheets.edit');
        Route::put('/{timesheet}', [TimesheetController::class, 'update'])->name('timesheets.update');
        Route::delete('/{timesheet}', [TimesheetController::class, 'destroy'])->name('timesheets.destroy');

        Route::post('/save-item', [TimesheetController::class, 'saveItem'])->name('timesheets.save-item');
        Route::post('/save-comment', [TimesheetController::class, 'saveComment'])->name('timesheets.save-comment');
        Route::post('/{timesheet}/fill-active', [TimesheetController::class, 'fillActive'])->name('timesheets.fill-active');
        Route::post('/{timesheet}/add-employee', [TimesheetController::class, 'addEmployee'])->name('timesheets.add-employee');
        Route::delete('/{timesheet}/remove-employee/{employee}', [TimesheetController::class, 'removeEmployee'])->name('timesheets.remove-employee');
        Route::get('/{timesheet}/excel', [TimesheetController::class, 'exportExcel'])->name('timesheets.excel');
        Route::get('/{timesheet}/pdf', [TimesheetController::class, 'exportPDF'])->name('timesheets.pdf');
    });

    // -------------------------------------------------------------------------
    // 🔹 УВЕДОМЛЕНИЯ
    // -------------------------------------------------------------------------
    Route::prefix('notifications')->group(function () {
        Route::get('/settings', [NotificationSettingsController::class, 'index'])->name('notifications.settings');
        Route::post('/settings', [NotificationSettingsController::class, 'update'])->name('notifications.settings.update');
        Route::post('/send-summary', [NotificationSettingsController::class, 'sendSummary'])->name('notifications.send-summary');
        Route::post('/save-template', [NotificationSettingsController::class, 'saveTemplate'])->name('notifications.save-template');
        Route::post('/send-from-template/{id}', [NotificationSettingsController::class, 'sendFromTemplate'])->name('notifications.send-from-template');
        Route::delete('/delete-template/{id}', [NotificationSettingsController::class, 'deleteTemplate'])->name('notifications.delete-template');
        Route::get('/logs', [NotificationSettingsController::class, 'logs'])->name('notifications.logs');
        Route::delete('/logs/{id}', [NotificationSettingsController::class, 'deleteLog'])->name('notifications.logs.delete');
        Route::delete('/logs', [NotificationSettingsController::class, 'clearLogs'])->name('notifications.logs.clear');
    });

});
