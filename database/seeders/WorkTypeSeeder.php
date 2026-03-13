<?php

namespace Database\Seeders;

use App\Models\WorkType;
use Illuminate\Database\Seeder;

class WorkTypeSeeder extends Seeder
{
    public function run(): void
    {
        $workTypes = [
            ['name' => 'Культивация почвы', 'code' => 'cultivation', 'color' => '#8B5CF6', 'icon' => 'fa-seedling', 'description' => 'Подготовка почвы к посадке', 'sort_order' => 1],
            ['name' => 'Посадка тюльпанов', 'code' => 'planting_tulips', 'color' => '#EC4899', 'icon' => 'fa-flower-tulip', 'description' => 'Посадка луковиц тюльпанов осенью', 'sort_order' => 2],
            ['name' => 'Посадка однолетников', 'code' => 'planting_annuals', 'color' => '#F59E0B', 'icon' => 'fa-flower', 'description' => 'Посадка однолетних цветов', 'sort_order' => 3],
            ['name' => 'Укрытие щепой', 'code' => 'mulching', 'color' => '#6B7280', 'icon' => 'fa-layer-group', 'description' => 'Укрытие растений на зиму', 'sort_order' => 4],
            ['name' => 'Снятие укрытия', 'code' => 'unmulching', 'color' => '#6B7280', 'icon' => 'fa-layer-minus', 'description' => 'Снятие зимнего укрытия', 'sort_order' => 5],
            ['name' => 'Полив', 'code' => 'watering', 'color' => '#3B82F6', 'icon' => 'fa-tint', 'description' => 'Полив клумб', 'sort_order' => 6],
            ['name' => 'Прополка', 'code' => 'weeding', 'color' => '#22C55E', 'icon' => 'fa-hand-holding-seedling', 'description' => 'Удаление сорняков', 'sort_order' => 7],
            ['name' => 'Обрезка', 'code' => 'pruning', 'color' => '#EF4444', 'icon' => 'fa-scissors', 'description' => 'Обрезка растений', 'sort_order' => 8],
        ];

        foreach ($workTypes as $wt) {
            WorkType::updateOrCreate(['code' => $wt['code']], $wt);
        }
    }
}
