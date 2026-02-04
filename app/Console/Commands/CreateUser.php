<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class CreateUser extends Command
{
    // Название команды, которую ты будешь вводить
    protected $signature = 'user:create';

    // Описание команды
    protected $description = 'Создание нового пользователя вручную';

    public function handle()
    {
        // Запрашиваем данные в консоли
        $name  = $this->ask('Введите ФИО полностью');
        $email = $this->ask('Введите Email');
        $password = $this->secret('Введите Пароль');

        if ($this->confirm("Создать пользователя {$name} ({$email})?")) {
            User::create([
                'name' => $name,
                'email' => $email,
                'password' => Hash::make($password),
            ]);

            $this->info('Пользователь успешно создан!');
        }
    }
}
