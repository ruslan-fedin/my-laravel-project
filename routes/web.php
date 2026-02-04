<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\PositionController;
use App\Http\Controllers\StatusController;
use App\Http\Controllers\TimesheetController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\TravelTimesheetController;


Route::patch('employees/{id}/restore', [EmployeeController::class, 'restore'])->name('employees.restore');

 Route::post('/travel-timesheets/{id}/update-status', [TravelTimesheetController::class, 'updateStatus']);

Route::post('/{id}/update-status', [TravelTimesheetController::class, 'updateStatus'])->name('travel-timesheets.update-status');

/*
|--------------------------------------------------------------------------
| ПУБЛИЧНЫЕ МАРШРУТЫ
|--------------------------------------------------------------------------
*/
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/forgot-password', [AuthController::class, 'showForgotPassword'])->name('password.request');
Route::post('/forgot-password', [AuthController::class, 'sendResetLink'])->name('password.email');

/*
|--------------------------------------------------------------------------
| ЗАЩИЩЕННЫЕ МАРШРУТЫ (AUTH)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->group(function () {

    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/', function () { return redirect()->route('timesheets.index'); })->name('home');
    Route::get('/dashboard', function () { return redirect()->route('timesheets.index'); })->name('dashboard');

    // Справочники

    Route::resource('employees', EmployeeController::class)->except(['destroy']);

    // Мягкое удаление (используем DELETE метод для стандартности)
    Route::delete('employees/{employee}/soft-delete', [EmployeeController::class, 'destroy'])
        ->name('employees.soft-delete');

    // Архив удаленных сотрудников
    Route::get('employees/trashed/list', [EmployeeController::class, 'trashed'])
        ->name('employees.trashed');

    // Восстановление (PATCH для частичного обновления)
    Route::patch('employees/{id}/restore', [EmployeeController::class, 'restore'])
        ->name('employees.restore');

    // Полное удаление из архива
    Route::delete('employees/{id}/force-delete', [EmployeeController::class, 'forceDelete'])
        ->name('employees.force-delete');
    
    Route::resource('employees', EmployeeController::class);
    Route::resource('positions', PositionController::class);
    Route::resource('statuses', StatusController::class);

    // --- СИСТЕМА УЧЁТА ВЫЕЗДОВ (Travel Timesheets) ---
    Route::prefix('travel-timesheets')->group(function () {
        // AJAX сохранение (должно быть ВЫШЕ resource или вне его, чтобы не путаться с ID)
        Route::post('/{id}/update-status', [TravelTimesheetController::class, 'updateStatus'])->name('travel-timesheets.update-status');
        Route::post('/update-comment', [TravelTimesheetController::class, 'saveComment'])->name('travel-timesheets.save-comment');

        // Действия внутри табеля
        Route::post('/{id}/add-employee', [TravelTimesheetController::class, 'addEmployee'])->name('travel-timesheets.add-employee');
        Route::post('/{id}/add-all', [TravelTimesheetController::class, 'addAll'])->name('travel-timesheets.add-all');
        Route::delete('/{id}/remove-employee/{empId}', [TravelTimesheetController::class, 'removeEmployee'])->name('travel-timesheets.remove-employee');
        Route::post('/{id}/mass-remove', [TravelTimesheetController::class, 'massRemove'])->name('travel-timesheets.mass-remove');
        Route::get('/{id}/export', [TravelTimesheetController::class, 'export'])->name('travel-timesheets.export');
    });
    // Основной ресурс (index, create, store, show, edit, update, destroy)
    Route::resource('travel-timesheets', TravelTimesheetController::class);


    // --- ОСНОВНОЙ ТАБЕЛЬ (Стандартный) ---
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
});
