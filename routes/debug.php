<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;

Route::get('/debug-speed', function() {
    $times = [];
    
    // 1. Время старта
    $start = microtime(true);
    
    // 2. Загрузка Laravel ядра
    $times['laravel_load'] = microtime(true) - $start;
    
    // 3. Запросы к БД
    $db_start = microtime(true);
    try {
        $count = DB::table('users')->count();
        $times['db_query'] = microtime(true) - $db_start;
    } catch (\Exception $e) {
        $times['db_query'] = 'Ошибка: ' . $e->getMessage();
    }
    
    // 4. Рендеринг вьюшки
    $view_start = microtime(true);
    $html = view('welcome')->render();
    $times['view_render'] = microtime(true) - $view_start;
    
    // 5. Общее время
    $times['total'] = microtime(true) - $start;
    
    return response()->json($times);
});
