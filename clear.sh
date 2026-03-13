#!/bin/bash

echo "🚀 Начинаю полную очистку кэша Laravel Sail..."

./vendor/bin/sail artisan view:clear
./vendor/bin/sail artisan route:clear
./vendor/bin/sail artisan cache:clear
./vendor/bin/sail artisan config:clear
./vendor/bin/sail artisan clear-compiled

echo "✨ Очистка завершена! Проверьте страницу архива."


# 1. Удаляем все старые миграции по работам
 rm database/migrations/*work*.php 2>/dev/null
 rm database/migrations/*flower_color*.php 2>/dev/null

# 2. Удаляем старые модели
 rm app/Models/WorkType.php 2>/dev/null
 rm app/Models/FlowerColor.php 2>/dev/null
 rm app/Models/WorkRecord.php 2>/dev/null
 rm app/Models/WorkRecordFlower.php 2>/dev/null
 rm app/Models/WorkPhoto.php 2>/dev/null

# 3. Удаляем старые контроллеры
 rm app/Http/Controllers/WorkRecordController.php 2>/dev/null
 rm app/Http/Controllers/WorkTypeController.php 2>/dev/null
 rm app/Http/Controllers/FlowerWorkController.php 2>/dev/null
 rm app/Http/Controllers/FlowerReportController.php 2>/dev/null
 rm app/Http/Controllers/FlowerColorController.php 2>/dev/null

# 4. Удаляем старые seeders
 rm database/seeders/WorkTypeSeeder.php 2>/dev/null
 rm database/seeders/FlowerColorSeeder.php 2>/dev/null

# 5. Удаляем старые View
 rm -rf resources/views/work-records 2>/dev/null
 rm -rf resources/views/work-types 2>/dev/null
 rm -rf resources/views/flowers 2>/dev/null

# 6. Очищаем кэш
 php artisan cache:clear
 php artisan config:clear
 php artisan route:clear
 php artisan view:clear
